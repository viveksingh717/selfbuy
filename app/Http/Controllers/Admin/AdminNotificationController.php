<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\ContactUs;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /** Full notifications history. */
    public function index()
    {
        $notifications = AdminNotification::latestFirst()->paginate(30);

        return view('admin.pages.notifications', compact('notifications'));
    }

    /**
     * Lightweight JSON feed for the header bell (polled by admin.js).
     */
    public function poll()
    {
        $items = AdminNotification::latestFirst()->limit(8)->get()->map(fn ($n) => [
            'id'       => $n->id,
            'title'    => $n->title,
            'body'     => $n->body,
            'icon'     => $n->icon,
            'url'      => route('admin.notifications.open', $n->id),
            'unread'   => $n->is_unread,
            'ago'      => $n->created_at->diffForHumans(),
        ]);

        return response()->json([
            'notif_unread'   => AdminNotification::unread()->count(),
            'contact_unread' => ContactUs::where('status', 'unread')->count(),
            'items'          => $items,
        ]);
    }

    /** Mark one read, then bounce to wherever it points. */
    public function open($id)
    {
        $n = AdminNotification::find($id);

        if (!$n) {
            return redirect()->route('admin.notifications');
        }

        $n->markAsRead();

        return redirect($n->url ?: route('admin.notifications'));
    }

    public function markRead($id)
    {
        $n = AdminNotification::find($id);

        if ($n) {
            $n->markAsRead();
        }

        return response()->json(['success' => true, 'notif_unread' => AdminNotification::unread()->count()]);
    }

    public function markAllRead()
    {
        AdminNotification::unread()->update(['read_at' => now()]);

        return response()->json(['success' => true, 'notif_unread' => 0]);
    }
}
