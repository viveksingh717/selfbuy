<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSetting extends Model
{
    protected $table = 'home_settings';

    protected $guarded = ['id'];

    protected $casts = [
        'signup_offer_enabled'     => 'boolean',
        'newsletter_popup_enabled' => 'boolean',
        'newsletter_coupon_enabled' => 'boolean',
        'signup_offer_coupon_enabled' => 'boolean',
    ];
}
