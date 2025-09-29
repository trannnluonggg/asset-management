<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id())
            ->orWhereNull('user_id'); // System notifications
        
        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Filter by read status
        if ($request->has('is_read') && $request->is_read !== '') {
            $query->where('is_read', $request->is_read);
        }
        
        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }

    public function show(Notification $notification)
    {
        // Mark as read if it belongs to current user
        if ($notification->user_id === Auth::id() || $notification->user_id === null) {
            $notification->update(['is_read' => true]);
        }
        
        return view('notifications.show', compact('notification'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id === Auth::id() || $notification->user_id === null) {
            $notification->update(['is_read' => true]);
        }
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->orWhereNull('user_id')
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->orWhereNull('user_id')
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }

    public function getRecent()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orWhereNull('user_id')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json($notifications);
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id === Auth::id() || $notification->user_id === null) {
            $notification->delete();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}