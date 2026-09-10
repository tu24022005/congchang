<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image', // <--- Thêm chữ 'image' vào đây nhé!
    ];

    // Quan hệ với sản phẩm (nếu có)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}