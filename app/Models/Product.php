<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'price',
        'category_id',
        'image',
    ];
    // Một sản phẩm có nhiều biến thể
    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class)->latest();
    }
// Liên kết 1 - Nhiều: 1 Sản phẩm có nhiều ảnh trong gallery
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    // Một sản phẩm thuộc về một danh mục
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}