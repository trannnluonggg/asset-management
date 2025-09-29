<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        // Middleware will be handled in routes or via attributes
    }

    public function show()
    {
        $user = auth()->user();
        $user->load('employee.department', 'employee.assetAssignments', 'employee.reportedIncidents');
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        $user->load('employee.department');
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->update([
            'username' => $request->username,
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Thông tin cá nhân đã được cập nhật thành công.');
    }

    public function changePassword()
    {
        return view('profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = auth()->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.'])
                ->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Mật khẩu đã được thay đổi thành công.');
    }

    public function notifications()
    {
        $user = auth()->user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('profile.notifications', compact('notifications'));
    }

    public function markNotificationAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->update(['is_read' => true]);

        return redirect()->back()
            ->with('success', 'Thông báo đã được đánh dấu là đã đọc.');
    }

    public function markAllNotificationsAsRead()
    {
        auth()->user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()
            ->with('success', 'Tất cả thông báo đã được đánh dấu là đã đọc.');
    }

    public function deleteNotification($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        return redirect()->back()
            ->with('success', 'Thông báo đã được xóa.');
    }

    public function activity()
    {
        $user = auth()->user();
        
        // Get user's asset assignments
        $assignments = collect();
        if ($user->employee) {
            $assignments = $user->employee->assetAssignments()
                ->with(['asset', 'assignedBy'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        // Get user's incident reports
        $incidents = collect();
        if ($user->employee) {
            $incidents = $user->employee->reportedIncidents()
                ->with(['asset'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        return view('profile.activity', compact('user', 'assignments', 'incidents'));
    }
}