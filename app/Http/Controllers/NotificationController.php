<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function open(Notification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === Auth::id(), 403);

        if (!$notification->is_read) {
            $notification->update(['is_read' => true]);
        }

        return redirect($notification->redirect_url ?: route('notifications.index'));
    }

    public function markRead(Notification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === Auth::id(), 403);

        $notification->update([
            'is_read' => true,
        ]);

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead(): RedirectResponse
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }
}