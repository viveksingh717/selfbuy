<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CouponModel;
use App\Models\ProductAttribute;
use App\Models\ProductGalleryImage;
use App\Models\SubCategory;
use App\Models\TaxModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;

    protected $table = 'product_models';
    protected $fillable = [
        'product_name',
        'product_slug',
        'short_description',
        'description',
        'additional_description',
        'category_id',
        'sub_category_id',
        'brand_id',
        'original_price',
        'selling_price',
        'cost_price',
        'discount',
        'tax_id',
        'coupon_id',
        'sku',
        'qty',
        'low_stock_alert',
        'stock_status',
        'product_image',
        'status',
        'is_featured',
        'is_trending',
        'meta_title',
        'meta_description'
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'selling_price'  => 'decimal:2',
        'cost_price'     => 'decimal:2',
        'discount'       => 'decimal:2',
        'is_featured'    => 'boolean',
        'is_trending'    => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function tax()
    {
        return $this->belongsTo(TaxModel::class, 'tax_id');
    }

    public function coupon()
    {
        return $this->belongsTo(CouponModel::class, 'coupon_id');
    }

    public function galleryImages()
    {
        return $this->hasMany(ProductGalleryImage::class, 'product_id');
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id');
    }


}
