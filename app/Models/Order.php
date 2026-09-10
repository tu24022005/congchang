<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Cho phép gán dữ liệu hàng loạt vào các cột này[cite: 1]
    protected $fillable = [
        'user_id',
        'total',
        'status',
        'payment_method',
        // Bổ sung 3 trường khách hàng để tránh lỗi Mass Assignment
        'customer_name',
        'customer_phone',
        'customer_address',
        'latitude',
        'longitude'
    ];

    // Quan hệ: Một Đơn hàng (Order) có nhiều Chi tiết đơn hàng (OrderItem)[cite: 1]
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Quan hệ: Một Đơn hàng thuộc về một Người dùng (User)[cite: 1]
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}