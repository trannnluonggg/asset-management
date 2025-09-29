<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;
use App\Models\Department;
use App\Models\IncidentReport;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_assets' => Asset::count(),
            'available_assets' => Asset::where('status', 'available')->count(),
            'assigned_assets' => Asset::where('status', 'assigned')->count(),
            'maintenance_assets' => Asset::where('status', 'maintenance')->count(),
            'total_employees' => Employee::where('status', 'active')->count(),
            'total_departments' => Department::where('is_active', true)->count(),
            'pending_incidents' => IncidentReport::where('status', 'pending')->count(),
        ];

        $recent_assignments = AssetAssignment::with(['asset', 'employee'])
            ->where('status', 'active')
            ->orderBy('assigned_date', 'desc')
            ->limit(5)
            ->get();

        $recent_incidents = IncidentReport::with(['asset', 'reportedBy'])
            ->orderBy('incident_date', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recent_assignments', 'recent_incidents'));
    }
}
