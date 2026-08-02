<?php

namespace App\Models;

use App\Models\ColorModel;
use App\Models\SizeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'product_attribute_id',
        'color_id',
        'size_id',
        'qty',
        'unit_price',
        'extra_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'extra_price' => 'decimal:2',
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

    public function getLineTotalAttribute(): float
    {
        return ((float) $this->unit_price + (float) $this->extra_price) * $this->qty;
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
