<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('department');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by department
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $employees = $query->paginate(15);
        $departments = Department::where('is_active', true)->get();
        
        return view('employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        return view('employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_code' => 'required|unique:employees,employee_code',
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:15',
            'department_id' => 'required|exists:departments,id',
            'position' => 'nullable|string|max:50',
            'hire_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,terminated'
        ]);

        Employee::create($request->all());

        return redirect()->route('employees.index')
            ->with('success', 'Nhân viên đã được tạo thành công.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'assignments.asset', 'incidentReports']);
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::where('is_active', true)->get();
        return view('employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'employee_code' => 'required|unique:employees,employee_code,' . $employee->id,
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:15',
            'department_id' => 'required|exists:departments,id',
            'position' => 'nullable|string|max:50',
            'hire_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,terminated'
        ]);

        $employee->update($request->all());

        return redirect()->route('employees.index')
            ->with('success', 'Thông tin nhân viên đã được cập nhật.');
    }

    public function destroy(Employee $employee)
    {
        // Check if employee has active assignments
        if ($employee->assignments()->where('status', 'active')->exists()) {
            return redirect()->route('employees.index')
                ->with('error', 'Không thể xóa nhân viên đang được cấp phát tài sản.');
        }

        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Nhân viên đã được xóa.');
    }

    public function getAssignments(Employee $employee)
    {
        $assignments = $employee->assignments()
            ->with(['asset.category'])
            ->orderBy('assigned_date', 'desc')
            ->get();
            
        return response()->json($assignments);
    }
}