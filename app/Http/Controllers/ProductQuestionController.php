<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductQuestionController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'question' => 'required|string|min:5|max:1000',
        ]);

        $question = ProductQuestion::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'question' => $validated['question'],
            'is_visible' => true,
        ]);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Câu hỏi của bạn đã được gửi. BeatyCare sẽ phản hồi sớm nhất!',
                'question' => [
                    'id' => $question->id,
                    'user_name' => Auth::check() ? Auth::user()->name : 'Khách hàng',
                    'question' => $question->question,
                    'created_at' => 'Vừa xong',
                ],
            ]);
        }

        return back()->with('success', 'Câu hỏi của bạn đã được gửi. BeatyCare sẽ phản hồi sớm nhất!');
    }

    // Admin methods
    public function answer(Request $request, ProductQuestion $question)
    {
        $validated = $request->validate([
            'answer' => 'required|string|max:2000',
        ]);

        $question->update([
            'answer' => $validated['answer'],
            'answered_by' => Auth::id(),
            'answered_at' => now(),
        ]);

        return back()->with('success', 'Đã trả lời câu hỏi thành công.');
    }

    public function toggleVisibility(ProductQuestion $question)
    {
        $question->update([
            'is_visible' => !$question->is_visible,
        ]);

        return back()->with('success', $question->is_visible ? 'Đã hiển thị câu hỏi.' : 'Đã ẩn câu hỏi.');
    }
}
