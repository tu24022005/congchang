<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = ['product_id', 'image', 'sku', 'color', 'storage', 'size_value', 'size_unit', 'price', 'stock'];

    protected $casts = [
        'size_value' => 'decimal:2',
        'price' => 'decimal:2',
    ];

    // Một biến thể luôn thuộc về 1 sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class, 'product_variation_id');
    }
}