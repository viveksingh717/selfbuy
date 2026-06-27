<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image',
        'button_text',
        'button_link',
        'position',
        'sort_order',
        'status',
    ];
}
