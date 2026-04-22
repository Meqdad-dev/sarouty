<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display all notifications.
     */
    public function index()
    {
        $notifications = DB::table('user_notifications')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Group by date
        $grouped = $notifications->groupBy(function ($notification) {
            return \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y');
        });

        $stats = [
            'total' => DB::table('user_notifications')->where('user_id', Auth::id())->count(),
            'unread' => DB::table('user_notifications')->where('user_id', Auth::id())->where('read', false)->count(),
        ];

        return view('pages.user.notifications.index', compact('notifications', 'grouped', 'stats'));
    }

    /**
     * Mark a notification as read.
     */
    public function markRead($id)
    {
        $notification = DB::table('user_notifications')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$notification) {
            abort(404, 'Notification non trouvée.');
        }

        DB::table('user_notifications')
            ->where('id', $id)
            ->update(['read' => true, 'updated_at' => now()]);

        // Redirect to URL if exists
        if ($notification->url) {
            return redirect($notification->url);
        }

        return back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        DB::table('user_notifications')
            ->where('user_id', Auth::id())
            ->where('read', false)
            ->update(['read' => true, 'updated_at' => now()]);

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    /**
     * Delete a notification.
     */
    public function destroy($id)
    {
        DB::table('user_notifications')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return back()->with('success', 'Notification supprimée.');
    }

    /**
     * Get unread count (for AJAX).
     */
    public function unreadCount()
    {
        $count = DB::table('user_notifications')
            ->where('user_id', Auth::id())
            ->where('read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications (for dropdown).
     */
    public function recent()
    {
        $notifications = DB::table('user_notifications')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json($notifications);
    }
}
