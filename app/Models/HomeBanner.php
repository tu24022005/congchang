<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
    protected $fillable = [
        'badge', 'title', 'description', 'image_path', 'image_url',
        'alt_text', 'button_text', 'button_url', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getImageSourceAttribute(): ?string
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : $this->image_url;
    }
}
