<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $table = 'wishlists';

    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'product_attribute_id',
        'color_id',
        'size_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }

    public function productAttribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    public function color()
    {
        return $this->belongsTo(ColorModel::class, 'color_id');
    }

    public function size()
    {
        return $this->belongsTo(SizeModel::class, 'size_id');
    }
}
