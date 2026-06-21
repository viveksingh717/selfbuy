<?php

namespace App\Models;

use App\Models\ColorModel;
use App\Models\ProductModel;
use App\Models\SizeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $table = 'product_attributes';

    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'additional_price',
        'stock',
        'extra_price',
        'sku_variant',
        'status'
    ];

    protected $casts = [
        'additional_price' => 'decimal:2',
        'extra_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
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
