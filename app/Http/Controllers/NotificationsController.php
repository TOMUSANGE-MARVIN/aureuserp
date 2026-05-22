<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationsController extends Controller
{
    /* --------------------------------------------------
     * GET /app/notifications — full notifications page
     * -------------------------------------------------- */
    public function index()
    {
        $userId = Auth::id();

        $notifications = DB::table('erp_notifications')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(20);

        // Mark all visible as read while browsing this page
        DB::table('erp_notifications')
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('app.notifications.index', compact('notifications'));
    }

    /* --------------------------------------------------
     * GET /app/notifications/unread  — JSON for navbar
     * -------------------------------------------------- */
    public function unread()
    {
        $userId = Auth::id();

        $items = DB::table('erp_notifications')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(15)
            ->get(['id', 'type', 'title', 'body', 'url', 'icon', 'read_at', 'created_at']);

        $unreadCount = DB::table('erp_notifications')
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        // Humanise timestamps
        $items = $items->map(function ($n) {
            $n->time_ago = $this->timeAgo($n->created_at);
            $n->is_read  = !is_null($n->read_at);
            return $n;
        });

        return response()->json([
            'items'        => $items,
            'unread_count' => $unreadCount,
        ]);
    }

    /* --------------------------------------------------
     * POST /app/notifications/{id}/read
     * -------------------------------------------------- */
    public function markRead(int $id)
    {
        $userId = Auth::id();

        $notification = DB::table('erp_notifications')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            DB::table('erp_notifications')
                ->where('id', $id)
                ->update(['read_at' => now()]);
        }

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect($notification->url ?? '/app/notifications');
    }

    /* --------------------------------------------------
     * POST /app/notifications/mark-all-read
     * -------------------------------------------------- */
    public function markAllRead()
    {
        DB::table('erp_notifications')
            ->where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    /* --------------------------------------------------
     * DELETE /app/notifications/{id}
     * -------------------------------------------------- */
    public function destroy(int $id)
    {
        DB::table('erp_notifications')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['ok' => true]);
    }

    /* --------------------------------------------------
     * DELETE /app/notifications/clear-all
     * -------------------------------------------------- */
    public function clearAll()
    {
        DB::table('erp_notifications')
            ->where('user_id', Auth::id())
            ->where(function ($q) {
                $q->whereNotNull('read_at');
            })
            ->delete();

        return response()->json(['ok' => true]);
    }

    private function timeAgo(string $dateStr): string
    {
        $diff = time() - strtotime($dateStr);
        if ($diff < 60)    return 'just now';
        if ($diff < 3600)  return floor($diff / 60)   . 'm ago';
        if ($diff < 86400) return floor($diff / 3600)  . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        return date('M j', strtotime($dateStr));
    }
}
