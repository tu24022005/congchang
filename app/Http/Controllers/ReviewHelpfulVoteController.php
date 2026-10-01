<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;
use App\Models\ReviewHelpfulVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewHelpfulVoteController extends Controller
{
    public function toggle(Request $request, ProductReview $review)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để đánh giá độ hữu ích.',
            ], 401);
        }

        // Không cho vote review của chính mình
        if ($review->user_id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể bình chọn cho đánh giá của chính mình.',
            ], 422);
        }

        $existingVote = ReviewHelpfulVote::where('review_id', $review->id)
            ->where('user_id', $user->id)
            ->first();

        $voted = false;
        if ($existingVote) {
            $existingVote->delete();
            $message = 'Đã hủy bình chọn.';
        } else {
            ReviewHelpfulVote::create([
                'review_id' => $review->id,
                'user_id' => $user->id,
            ]);
            $voted = true;
            $message = 'Cảm ơn bạn đã bình chọn!';
        }

        $helpfulCount = ReviewHelpfulVote::where('review_id', $review->id)->count();

        return response()->json([
            'success' => true,
            'voted' => $voted,
            'helpful_count' => $helpfulCount,
            'message' => $message,
        ]);
    }
}
