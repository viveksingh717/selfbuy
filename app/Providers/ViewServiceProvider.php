<?php

namespace App\Providers;

use App\Http\ViewComposers\HeaderComposer;
use App\Http\ViewComposers\MobileMenuComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.inc.header', HeaderComposer::class);
        View::composer('layouts.inc.mobile_menu', MobileMenuComposer::class);
    }
}