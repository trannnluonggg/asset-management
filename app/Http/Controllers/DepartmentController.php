<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::withCount('employees');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }
        
        $departments = $query->paginate(15);
        
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        return view('departments.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:departments,code|max:10',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:employees,id',
            'is_active' => 'boolean'
        ]);

        Department::create($request->all());

        return redirect()->route('departments.index')
            ->with('success', 'Bộ phận đã được tạo thành công.');
    }

    public function show(Department $department)
    {
        $department->load(['employees', 'manager']);
        $employeeCount = $department->employees()->count();
        $activeEmployeeCount = $department->employees()->where('status', 'active')->count();
        
        return view('departments.show', compact('department', 'employeeCount', 'activeEmployeeCount'));
    }

    public function edit(Department $department)
    {
        $employees = Employee::where('status', 'active')->get();
        return view('departments.edit', compact('department', 'employees'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'code' => 'required|unique:departments,code,' . $department->id . '|max:10',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:employees,id',
            'is_active' => 'boolean'
        ]);

        $department->update($request->all());

        return redirect()->route('departments.index')
            ->with('success', 'Thông tin bộ phận đã được cập nhật.');
    }

    public function destroy(Department $department)
    {
        // Check if department has employees
        if ($department->employees()->exists()) {
            return redirect()->route('departments.index')
                ->with('error', 'Không thể xóa bộ phận đang có nhân viên.');
        }

        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Bộ phận đã được xóa.');
    }

    public function getEmployees(Department $department)
    {
        $employees = $department->employees()
            ->select('id', 'employee_code', 'full_name', 'email', 'position', 'status')
            ->get();
            
        return response()->json($employees);
    }
}