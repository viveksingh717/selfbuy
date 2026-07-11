<?php

namespace App\Models;

use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = "categories";
    protected $fillable = [
        'category_name',
        'category_slug',
        'description',
        'category_image',
        'is_featured',
        'status',
        'meta_title',
        'meta_description'
    ];

    public function subcategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id')->where('status', 1)->orderBy('subcategory_name');
    }
}
