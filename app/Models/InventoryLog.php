<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryLog extends Model
{
    protected $fillable = [
        'product_variation_id',
        'product_id',
        'user_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reason',
        'reference_type',
        'reference_id',
    ];

    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public static function record(
        ProductVariation $variation,
        int $before,
        int $after,
        string $reason,
        ?Model $reference = null
    ): ?self {
        $quantity = $after - $before;
        if ($quantity === 0) {
            return null;
        }

        return static::create([
            'product_variation_id' => $variation->id,
            'product_id' => $variation->product_id,
            'user_id' => auth()->id(),
            'type' => $quantity > 0 ? 'in' : 'out',
            'quantity' => $quantity,
            'stock_before' => $before,
            'stock_after' => $after,
            'reason' => $reason,
            'reference_type' => $reference?->getMorphClass(),
            'reference_id' => $reference?->getKey(),
        ]);
    }
}
