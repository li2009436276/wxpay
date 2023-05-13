<?php

namespace WxLogin\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function map()
    {

        Route::middleware('web')
            ->namespace('WxLogin\Controllers\Web')
            ->group(__DIR__.'/../routes/web.php');


        Route::prefix(config('wx.api_prefix'))
            ->middleware('api')
            ->namespace('WxLogin\Controllers\Api')
            ->group(__DIR__ . '/../routes/api.php');
    }
}