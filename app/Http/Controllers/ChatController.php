<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    private const PRESENCE_TTL_SECONDS = 120;

    public function adminIndex()
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        return view('admin.chat.index');
    }

    // Lấy danh sách khách hàng đã từng chat (Chỉ dành cho Admin)
    public function fetchUsers()
    {
        if (Auth::user()->role !== 'admin') return response()->json([]);
        
        $users = User::whereHas('messages', function($query) {
            $query->where('is_admin', false);
        })
            ->withCount(['messages as unread_messages_count' => function ($query) {
                $query->where('is_admin', false)->where('is_read', false);
            }])
            ->with(['messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->get()
            ->sortByDesc(fn ($user) => optional($user->messages->first())->created_at)
            ->values()
            ->each(function ($user) {
                $presence = Cache::get('chat_presence_' . $user->id);
                $user->is_online = $presence && now()->timestamp - $presence <= self::PRESENCE_TTL_SECONDS;
                $user->last_seen_minutes = $presence ? max(0, (int) floor((now()->timestamp - $presence) / 60)) : null;
            });
        
        return response()->json($users);
    }

    // Lấy lịch sử chat của 1 phòng (1-1)
    public function fetchMessages(Request $request)
    {
        // Admin chat với ai thì truyền ID người đó lên, Khách thì tự động lấy ID của mình
        $targetUserId = Auth::user()->role === 'admin' ? $request->user_id : Auth::id();
        
        if (Auth::user()->role === 'admin') {
            Message::where('user_id', $targetUserId)
                ->where('is_admin', false)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return Message::where('user_id', $targetUserId)->with('user')->oldest()->get();
    }

    // Nhận tin nhắn và phân phòng chat
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:2000|required_without:attachment',
            'attachment' => 'nullable|file|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'receiver_id' => 'nullable|exists:users,id',
        ]);

        $isAdmin = Auth::user()->role === 'admin';
        // Quyết định phòng chat: Nếu Admin nhắn thì phòng là receiver_id, nếu Khách nhắn thì phòng là ID của khách
        $targetUserId = $isAdmin ? $request->receiver_id : Auth::id();

        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('chat', 'public')
            : null;

        $message = Message::create([
            'user_id' => $targetUserId,
            'message' => $request->input('message', ''),
            'attachment_path' => $attachmentPath,
            'is_admin' => $isAdmin,
        ]);

        $message->load('user');
        $message->attachment_url = $attachmentPath ? Storage::disk('public')->url($attachmentPath) : null;

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['status' => 'Đã gửi', 'message' => $message]);
    }

    public function heartbeat()
    {
        Cache::put('chat_presence_' . Auth::id(), now()->timestamp, now()->addSeconds(self::PRESENCE_TTL_SECONDS));

        return response()->json(['status' => 'ok']);
    }

    public function presence(Request $request)
    {
        $targetUserId = Auth::user()->role === 'admin'
            ? $request->integer('user_id')
            : User::where('role', 'admin')->value('id');

        $lastSeen = $targetUserId ? Cache::get('chat_presence_' . $targetUserId) : null;
        $minutes = $lastSeen ? max(0, (int) floor((now()->timestamp - $lastSeen) / 60)) : null;

        return response()->json([
            'online' => $lastSeen && now()->timestamp - $lastSeen <= self::PRESENCE_TTL_SECONDS,
            'last_seen_minutes' => $minutes,
        ]);
    }
}