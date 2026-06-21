<?php

namespace App\Models;

use App\Models\ProductModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGalleryImage extends Model
{
    use HasFactory;

    protected $table = 'product_gallery_images';

    protected $fillable = [
        'product_id',
        'image_path',
        'sort_order',
    ];

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }
}
