<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['code', 'scope', 'user_id', 'type', 'value', 'min_order_value', 'usage_limit', 'expires_at'];

    protected $casts = [
        'expires_at' => 'date',
        'used_count' => 'integer',
        'usage_limit' => 'integer',
    ];

    public function isAvailable(): bool
    {
        return (!$this->expires_at || !$this->expires_at->isBefore(today()))
            && (!$this->usage_limit || $this->used_count < $this->usage_limit);
    }

    public function remainingUses(): ?int
    {
        return $this->usage_limit === null
            ? null
            : max(0, $this->usage_limit - $this->used_count);
    }
}