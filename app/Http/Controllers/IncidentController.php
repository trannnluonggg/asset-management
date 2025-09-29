<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\IncidentReport;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $query = IncidentReport::with(['asset.category', 'reportedBy.department', 'resolvedBy']);
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('asset_code', 'like', "%{$search}%")
                  ->orWhere('asset_name', 'like', "%{$search}%");
            })->orWhereHas('reportedBy', function($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }
        
        // Filter by incident type
        if ($request->has('incident_type') && $request->incident_type) {
            $query->where('incident_type', $request->incident_type);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $incidents = $query->orderBy('incident_date', 'desc')->paginate(15);
        
        return view('incidents.index', compact('incidents'));
    }

    public function create(Request $request)
    {
        $assets = Asset::where('status', 'assigned')
            ->with(['category', 'currentAssignment.employee'])
            ->get();
        
        $selectedAssetId = $request->get('asset_id');
            
        return view('incidents.create', compact('assets', 'selectedAssetId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'incident_type' => 'required|in:damage,lost,theft,malfunction',
            'incident_date' => 'required|date',
            'description' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            // Create incident report
            $incident = IncidentReport::create([
                'asset_id' => $request->asset_id,
                'reported_by' => Auth::user()->employee_id ?? Auth::id(),
                'incident_type' => $request->incident_type,
                'incident_date' => $request->incident_date,
                'description' => $request->description,
                'status' => 'pending'
            ]);

            // Update asset status if needed
            $asset = Asset::findOrFail($request->asset_id);
            if (in_array($request->incident_type, ['damage', 'malfunction'])) {
                $asset->update(['status' => 'maintenance']);
            } elseif (in_array($request->incident_type, ['lost', 'theft'])) {
                $asset->update(['status' => 'retired']);
            }

            // Log history
            AssetHistory::create([
                'asset_id' => $request->asset_id,
                'action_type' => 'maintenance',
                'performed_by' => Auth::id(),
                'old_value' => json_encode(['status' => $asset->getOriginal('status')]),
                'new_value' => json_encode(['status' => $asset->status]),
                'notes' => "Báo cáo sự cố: {$request->incident_type}"
            ]);

            // Create notification for admin/HR
            Notification::create([
                'user_id' => null, // System notification
                'title' => 'Báo cáo sự cố mới',
                'message' => "Sự cố {$request->incident_type} được báo cáo cho tài sản {$asset->asset_code}",
                'type' => 'incident',
                'data' => json_encode(['incident_id' => $incident->id]),
                'is_read' => false
            ]);
        });

        return redirect()->route('incidents.index')
            ->with('success', 'Báo cáo sự cố đã được gửi thành công.');
    }

    public function show(IncidentReport $incident)
    {
        $incident->load(['asset.category', 'reportedBy.department', 'resolvedBy']);
        return view('incidents.show', compact('incident'));
    }

    public function edit(IncidentReport $incident)
    {
        if ($incident->status === 'closed') {
            return redirect()->route('incidents.index')
                ->with('error', 'Không thể chỉnh sửa sự cố đã đóng.');
        }

        return view('incidents.edit', compact('incident'));
    }

    public function update(Request $request, IncidentReport $incident)
    {
        if ($incident->status === 'closed') {
            return redirect()->route('incidents.index')
                ->with('error', 'Không thể cập nhật sự cố đã đóng.');
        }

        $request->validate([
            'incident_type' => 'required|in:damage,lost,theft,malfunction',
            'incident_date' => 'required|date',
            'description' => 'required|string',
            'resolution' => 'nullable|string',
            'status' => 'required|in:pending,investigating,resolved,closed'
        ]);

        $oldStatus = $incident->status;
        
        $updateData = [
            'incident_type' => $request->incident_type,
            'incident_date' => $request->incident_date,
            'description' => $request->description,
            'resolution' => $request->resolution,
            'status' => $request->status
        ];

        // If resolving or closing, add resolved info
        if (in_array($request->status, ['resolved', 'closed']) && $oldStatus !== $request->status) {
            $updateData['resolved_by'] = Auth::user()->employee_id ?? Auth::id();
            $updateData['resolved_date'] = now();
        }

        $incident->update($updateData);

        // Log history
        AssetHistory::create([
            'asset_id' => $incident->asset_id,
            'action_type' => 'maintenance',
            'performed_by' => Auth::id(),
            'old_value' => json_encode(['incident_status' => $oldStatus]),
            'new_value' => json_encode(['incident_status' => $request->status]),
            'notes' => "Cập nhật trạng thái sự cố: {$request->status}"
        ]);

        return redirect()->route('incidents.index')
            ->with('success', 'Thông tin sự cố đã được cập nhật.');
    }

    public function resolve(Request $request, IncidentReport $incident)
    {
        if ($incident->status === 'closed') {
            return redirect()->route('incidents.index')
                ->with('error', 'Sự cố đã được đóng.');
        }

        $request->validate([
            'resolution' => 'required|string',
            'asset_status' => 'required|in:available,maintenance,retired'
        ]);

        DB::transaction(function () use ($request, $incident) {
            // Update incident
            $incident->update([
                'resolution' => $request->resolution,
                'status' => 'resolved',
                'resolved_by' => Auth::user()->employee_id ?? Auth::id(),
                'resolved_date' => now()
            ]);

            // Update asset status
            $incident->asset->update(['status' => $request->asset_status]);

            // Log history
            AssetHistory::create([
                'asset_id' => $incident->asset_id,
                'action_type' => 'maintenance',
                'performed_by' => Auth::id(),
                'old_value' => json_encode(['status' => $incident->asset->getOriginal('status')]),
                'new_value' => json_encode(['status' => $request->asset_status]),
                'notes' => 'Giải quyết sự cố: ' . $request->resolution
            ]);
        });

        return redirect()->route('incidents.index')
            ->with('success', 'Sự cố đã được giải quyết.');
    }

    public function close(IncidentReport $incident)
    {
        if ($incident->status !== 'resolved') {
            return redirect()->route('incidents.index')
                ->with('error', 'Chỉ có thể đóng sự cố đã được giải quyết.');
        }

        $incident->update(['status' => 'closed']);

        return redirect()->route('incidents.index')
            ->with('success', 'Sự cố đã được đóng.');
    }

    public function getAssetIncidents(Asset $asset)
    {
        $incidents = $asset->incidentReports()
            ->with(['reportedBy', 'resolvedBy'])
            ->orderBy('incident_date', 'desc')
            ->get();
            
        return response()->json($incidents);
    }
}