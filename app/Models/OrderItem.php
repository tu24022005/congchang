<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    // Cho phép gán dữ liệu hàng loạt
    protected $fillable = [
        'order_id',
        'product_id',
        'variation_id',
        'quantity',
        'price'
    ];

    // Quan hệ: Chi tiết đơn hàng thuộc về 1 Đơn hàng[cite: 1]
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Quan hệ: Chi tiết đơn hàng liên kết tới 1 Sản phẩm[cite: 1]
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class);
    }
}