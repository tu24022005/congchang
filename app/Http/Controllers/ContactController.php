<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        ContactMessage::create($validated);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã liên hệ! BeatyCare đã nhận tin nhắn và sẽ phản hồi sớm nhất.',
            ]);
        }

        return back()->with('success', 'Cảm ơn bạn đã liên hệ! BeatyCare đã nhận tin nhắn và sẽ phản hồi sớm nhất.');
    }
}
