<?php

namespace Wuwx\LaravelSocialiteAlipay;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class LaravelSocialiteAlipayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Socialite::extend('alipay', function () {
            $config = $this->app['config']['services.alipay'];
            $provider = Socialite::buildProvider(AlipayProvider::class, $config);
            return $provider;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
     public function register()
     {
         //
     }
}
