<?php

namespace Wuwx\LaravelSocialiteAlipay;

use Illuminate\Support\Facades\Facade;

class LaravelSocialiteAlipayFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return LaravelSocialiteAlipay::class;
    }
}
