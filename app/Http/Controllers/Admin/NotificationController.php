<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->string('category')->toString();
        $notifications = $request->user()->notifications()->latest()->get();
        $notifications = $category === ''
            ? $notifications
            : $notifications->filter(fn ($notification) => ($notification->data['category'] ?? 'general') === $category);
        $notifications = new \Illuminate\Pagination\LengthAwarePaginator(
            $notifications->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 20)->values(),
            $notifications->count(),
            20,
            \Illuminate\Pagination\Paginator::resolveCurrentPage(),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.notifications.index', compact('notifications', 'category'));
    }

    public function read(Request $request, string $notification): RedirectResponse
    {
        $item = $request->user()->notifications()->whereKey($notification)->firstOrFail();
        $item->markAsRead();

        $url = $item->data['url'] ?? route('admin.notifications.index');

        return redirect()->to($url);
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }
}
