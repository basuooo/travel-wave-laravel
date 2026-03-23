<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function read(Request $request, string $notification)
    {
        $item = $request->user()
            ->notifications()
            ->whereKey($notification)
            ->firstOrFail();

        if (is_null($item->read_at)) {
            $item->markAsRead();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', __('admin.notification_marked_read'));
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', __('admin.notifications_marked_read'));
    }
}
