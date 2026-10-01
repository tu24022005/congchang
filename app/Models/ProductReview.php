<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'reviewer_name',
        'rating',
        'comment',
        'variant_label',
        'is_verified_purchase',
        'has_media',
        'media_paths',
        'is_visible',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified_purchase' => 'boolean',
        'has_media' => 'boolean',
        'is_visible' => 'boolean',
        'media_paths' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function helpfulVotes()
    {
        return $this->hasMany(ReviewHelpfulVote::class, 'review_id');
    }

    public function isHelpfulVotedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->helpfulVotes()->where('user_id', $user->id)->exists();
    }
}
