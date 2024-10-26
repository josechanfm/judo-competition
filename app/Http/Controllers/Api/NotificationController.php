<?php

namespace Modules\Admin\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function unreadNotifications()
    {
        $notifications = auth()->user()->unreadNotifications;

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return response()->json([
            'message' => 'ok'
        ]);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'ok'
        ]);
    }
}
