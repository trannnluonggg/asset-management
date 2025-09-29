<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetHistory;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Thống kê tổng quan
        $totalAssets = Asset::count();
        $availableAssets = Asset::where('status', 'available')->count();
        $assignedAssets = Asset::where('status', 'assigned')->count();
        $maintenanceAssets = Asset::where('status', 'maintenance')->count();
        $retiredAssets = Asset::where('status', 'retired')->count();

        // Thống kê theo danh mục
        $assetsByCategory = Asset::select('asset_categories.name as category_name', DB::raw('count(*) as total'))
            ->join('asset_categories', 'assets.category_id', '=', 'asset_categories.id')
            ->groupBy('asset_categories.id', 'asset_categories.name')
            ->get();

        // Thống kê theo trạng thái
        $assetsByStatus = Asset::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // Tài sản có giá trị cao nhất
        $highValueAssets = Asset::where('purchase_price', '>', 0)
            ->orderBy('purchase_price', 'desc')
            ->limit(10)
            ->get();

        // Tài sản sắp hết bảo hành
        $expiringWarranty = Asset::whereNotNull('warranty_expiry')
            ->where('warranty_expiry', '>', now())
            ->where('warranty_expiry', '<=', now()->addMonths(3))
            ->orderBy('warranty_expiry')
            ->get();

        // Hoạt động gần đây
        $recentActivities = AssetHistory::with(['asset', 'performedBy'])
            ->orderBy('action_date', 'desc')
            ->limit(20)
            ->get();

        // Thống kê theo tháng (12 tháng gần đây)
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $date->format('Y-m'),
                'month_name' => $date->format('M Y'),
                'created' => Asset::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'activities' => AssetHistory::whereYear('action_date', $date->year)
                    ->whereMonth('action_date', $date->month)
                    ->count()
            ];
        }

        return view('reports.index', compact(
            'totalAssets',
            'availableAssets', 
            'assignedAssets',
            'maintenanceAssets',
            'retiredAssets',
            'assetsByCategory',
            'assetsByStatus',
            'highValueAssets',
            'expiringWarranty',
            'recentActivities',
            'monthlyStats'
        ));
    }

    public function assetReport(Request $request)
    {
        $query = Asset::with(['category', 'assignedUser']);

        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo khoảng thời gian mua
        if ($request->filled('purchase_from')) {
            $query->where('purchase_date', '>=', $request->purchase_from);
        }
        if ($request->filled('purchase_to')) {
            $query->where('purchase_date', '<=', $request->purchase_to);
        }

        // Lọc theo giá trị
        if ($request->filled('price_from')) {
            $query->where('purchase_price', '>=', $request->price_from);
        }
        if ($request->filled('price_to')) {
            $query->where('purchase_price', '<=', $request->price_to);
        }

        $assets = $query->orderBy('asset_code')->get();
        $categories = AssetCategory::where('is_active', true)->get();

        return view('reports.assets', compact('assets', 'categories'));
    }

    public function activityReport(Request $request)
    {
        $query = AssetHistory::with(['asset', 'performedBy']);

        // Lọc theo loại hoạt động
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        // Lọc theo khoảng thời gian
        if ($request->filled('date_from')) {
            $query->where('action_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('action_date', '<=', $request->date_to);
        }

        // Lọc theo tài sản
        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        // Lọc theo người dùng
        if ($request->filled('user_id')) {
            $query->where('performed_by', $request->user_id);
        }

        $activities = $query->orderBy('action_date', 'desc')->paginate(50);
        $assets = Asset::orderBy('asset_code')->get();
        $users = Employee::orderBy('full_name')->get();

        return view('reports.activities', compact('activities', 'assets', 'users'));
    }

    public function exportAssets(Request $request)
    {
        $query = Asset::with(['category', 'assignedUser']);

        // Áp dụng các bộ lọc tương tự như assetReport
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('purchase_from')) {
            $query->where('purchase_date', '>=', $request->purchase_from);
        }
        if ($request->filled('purchase_to')) {
            $query->where('purchase_date', '<=', $request->purchase_to);
        }

        $assets = $query->orderBy('asset_code')->get();

        $filename = 'assets_report_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($assets) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fwrite($file, "\xEF\xBB\xBF");
            
            // Header row
            fputcsv($file, [
                'Mã tài sản',
                'Tên tài sản', 
                'Danh mục',
                'Thương hiệu',
                'Model',
                'Serial Number',
                'Trạng thái',
                'Vị trí',
                'Người được giao',
                'Ngày mua',
                'Giá mua',
                'Nhà cung cấp',
                'Bảo hành đến',
                'Ghi chú'
            ]);

            // Data rows
            foreach ($assets as $asset) {
                fputcsv($file, [
                    $asset->asset_code,
                    $asset->asset_name,
                    $asset->category->name ?? '',
                    $asset->brand ?? '',
                    $asset->model ?? '',
                    $asset->serial_number ?? '',
                    $this->getStatusText($asset->status),
                    $asset->location ?? '',
                    $asset->assignedUser->full_name ?? '',
                    $asset->purchase_date ? $asset->purchase_date->format('d/m/Y') : '',
                    $asset->purchase_price ? number_format($asset->purchase_price, 0, ',', '.') : '',
                    $asset->supplier ?? '',
                    $asset->warranty_expiry ? $asset->warranty_expiry->format('d/m/Y') : '',
                    $asset->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getStatusText($status)
    {
        $statusMap = [
            'available' => 'Khả dụng',
            'assigned' => 'Đã giao',
            'maintenance' => 'Bảo trì',
            'retired' => 'Ngừng sử dụng'
        ];

        return $statusMap[$status] ?? $status;
    }
}