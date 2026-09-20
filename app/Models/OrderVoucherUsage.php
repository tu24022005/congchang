<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderVoucherUsage extends Model
{
    protected $fillable = ['order_id', 'voucher_id', 'released_at'];

    protected $casts = [
        'released_at' => 'datetime',
    ];
}
