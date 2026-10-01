<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use App\Mail\NewsletterConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        // Honeypot field chống bot
        if ($request->filled('website_hp')) {
            return response()->json(['success' => true, 'message' => 'Cảm ơn bạn đã đăng ký!']);
        }

        $validated = $request->validate([
            'email' => 'required|email|max:190',
        ]);

        $subscriber = NewsletterSubscriber::firstOrNew(['email' => strtolower($validated['email'])]);
        
        if ($subscriber->isConfirmed()) {
            return response()->json([
                'success' => true,
                'message' => 'Email của bạn đã được đăng ký nhận tin từ trước đó rồi nhé!',
            ]);
        }

        $subscriber->token = Str::random(48);
        $subscriber->unsubscribed_at = null;
        $subscriber->save();

        // Gửi email xác nhận
        try {
            Mail::to($subscriber->email)->queue(new NewsletterConfirmationMail($subscriber));
        } catch (\Throwable $e) {
            // Log if queue or smtp not set up, but let user know
        }

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn bạn! Chúng tôi đã gửi email xác nhận đến hòm thư của bạn. Vui lòng kiểm tra để hoàn tất.',
        ]);
    }

    public function confirm(string $token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();
        $subscriber->confirmed_at = now();
        $subscriber->save();

        return redirect()->route('welcome')->with('success', 'Xác nhận đăng ký nhận tin thành công! Cảm ơn bạn đã đồng hành cùng BeatyCare 🌸');
    }

    public function unsubscribe(string $token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();
        $subscriber->unsubscribed_at = now();
        $subscriber->save();

        return redirect()->route('welcome')->with('info', 'Bạn đã hủy đăng ký nhận bản tin thành công.');
    }

    // Admin listing & CSV export
    public function adminIndex()
    {
        $subscribers = NewsletterSubscriber::latest()->paginate(20);
        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function adminExport()
    {
        $subscribers = NewsletterSubscriber::all();
        $filename = 'newsletter-subscribers-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($subscribers) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, ['ID', 'Email', 'Trạng thái', 'Ngày đăng ký', 'Ngày xác nhận']);

            foreach ($subscribers as $s) {
                fputcsv($handle, [
                    $s->id,
                    $s->email,
                    $s->isConfirmed() ? 'Đã xác nhận' : 'Chờ xác nhận',
                    $s->created_at->format('d/m/Y H:i'),
                    $s->confirmed_at ? $s->confirmed_at->format('d/m/Y H:i') : '',
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
