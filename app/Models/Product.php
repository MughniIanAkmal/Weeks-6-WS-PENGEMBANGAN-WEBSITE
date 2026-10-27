<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'sku', 'price', 'stock', 'image'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get product image URL or default placeholder
     */
    public function getImageUrlAttribute()
    {
        if ($this->image && \Illuminate\Support\Facades\Storage::disk('public')->exists('products/' . $this->image)) {
            return asset('storage/products/' . $this->image);
        }
        return null;
    }

    /**
     * Get product thumbnail URL or default placeholder
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->image && \Illuminate\Support\Facades\Storage::disk('public')->exists('products/thumbnails/' . $this->image)) {
            return asset('storage/products/thumbnails/' . $this->image);
        }
        return $this->image_url;
    }
}