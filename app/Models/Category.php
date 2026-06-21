<?php

namespace App\Models;

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
}
