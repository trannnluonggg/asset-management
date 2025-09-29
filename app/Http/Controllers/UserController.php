<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        // Middleware will be handled in routes or via attributes
    }
    
    private function checkAdminAccess()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Chỉ Admin mới có quyền quản lý người dùng.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAdminAccess();
        
        $query = User::with('employee.department');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhereHas('employee', function($eq) use ($search) {
                      $eq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->checkAdminAccess();
        
        $employees = Employee::whereDoesntHave('user')->get();
        return view('users.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $this->checkAdminAccess();
        
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'employee_id' => 'nullable|exists:employees,id|unique:users',
            'role' => 'required|in:admin,hr,user',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'employee_id' => $request->employee_id,
            'role' => $request->role,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Người dùng đã được tạo thành công.');
    }

    public function show(User $user)
    {
        $this->checkAdminAccess();
        
        $user->load('employee.department', 'notifications');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->checkAdminAccess();
        
        $employees = Employee::whereDoesntHave('user')
            ->orWhere('id', $user->employee_id)
            ->get();
        return view('users.edit', compact('user', 'employees'));
    }

    public function update(Request $request, User $user)
    {
        $this->checkAdminAccess();
        
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'employee_id' => [
                'nullable',
                'exists:employees,id',
                Rule::unique('users')->ignore($user->id)
            ],
            'role' => 'required|in:admin,hr,user',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'username' => $request->username,
            'employee_id' => $request->employee_id,
            'role' => $request->role,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('users.index')
            ->with('success', 'Thông tin người dùng đã được cập nhật.');
    }

    public function destroy(User $user)
    {
        $this->checkAdminAccess();
        
        // Prevent deleting the current user
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Không thể xóa tài khoản đang đăng nhập.');
        }

        // Prevent deleting the last admin
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('users.index')
                ->with('error', 'Không thể xóa admin cuối cùng trong hệ thống.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Người dùng đã được xóa thành công.');
    }

    public function toggleStatus(User $user)
    {
        $this->checkAdminAccess();
        
        // Prevent deactivating the current user
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Không thể vô hiệu hóa tài khoản đang đăng nhập.');
        }

        // Prevent deactivating the last admin
        if ($user->isAdmin() && $user->is_active && User::where('role', 'admin')->where('is_active', true)->count() <= 1) {
            return redirect()->route('users.index')
                ->with('error', 'Không thể vô hiệu hóa admin cuối cùng đang hoạt động.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        return redirect()->route('users.index')
            ->with('success', "Tài khoản đã được {$status} thành công.");
    }

    public function resetPassword(User $user)
    {
        $this->checkAdminAccess();
        
        $newPassword = 'Livespo@' . date('Y');
        $user->update(['password' => Hash::make($newPassword)]);

        return redirect()->route('users.show', $user)
            ->with('success', "Mật khẩu đã được reset thành: {$newPassword}");
    }
}