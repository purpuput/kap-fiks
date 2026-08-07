<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Tampilkan halaman daftar notifikasi pengguna
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Tandai notifikasi tertentu sebagai dibaca dan buka link tujuan
     */
    public function markAsRead(string $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            $targetUrl = $notification->data['url'] ?? route('dashboard');
            return redirect($targetUrl);
        }

        return redirect()->back();
    }

    /**
     * Tandai semua notifikasi milik user sebagai telah dibaca
     */
    public function markAllAsRead(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi berhasil ditandai telah dibaca.'
            ]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi berhasil ditandai telah dibaca.');
    }

    /**
     * API JSON untuk data unread counter dan preview di header bell dropdown
     */
    public function unreadData()
    {
        $user = Auth::user();
        $unreadCount = $user->unreadNotifications()->count();
        $recentNotifications = $user->notifications()
            ->limit(5)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->data['type'] ?? 'general',
                    'title' => $n->data['title'] ?? 'Notifikasi',
                    'message' => $n->data['message'] ?? '',
                    'url' => route('notifications.read', $n->id),
                    'read' => $n->read_at !== null,
                    'created_at' => $n->created_at->diffForHumans(),
                    'icon' => $n->data['icon'] ?? 'bell',
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'recent' => $recentNotifications
        ]);
    }
}
