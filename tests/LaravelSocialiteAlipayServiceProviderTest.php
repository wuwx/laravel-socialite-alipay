<?php

namespace Wuwx\LaravelSocialiteAlipay\Tests;

use PHPUnit\Framework\TestCase;
use Wuwx\LaravelSocialiteAlipay\LaravelSocialiteAlipayServiceProvider;

class LaravelSocialiteAlipayServiceProviderTest extends TestCase
{
    public function testServiceProviderCanBeInstantiated()
    {
        $app = new class {
            public $config = [];
            public function __get($key)
            {
                return $this;
            }
            public function __call($method, $args)
            {
                return $this;
            }
        };

        $provider = new LaravelSocialiteAlipayServiceProvider($app);

        $this->assertInstanceOf(LaravelSocialiteAlipayServiceProvider::class, $provider);
    }

    public function testServiceProviderHasBootMethod()
    {
        $app = new class {
            public $config = [];
            public function __get($key)
            {
                return $this;
            }
            public function __call($method, $args)
            {
                return $this;
            }
        };

        $provider = new LaravelSocialiteAlipayServiceProvider($app);

        $this->assertTrue(method_exists($provider, 'boot'));
    }

    public function testServiceProviderHasRegisterMethod()
    {
        $app = new class {
            public $config = [];
            public function __get($key)
            {
                return $this;
            }
            public function __call($method, $args)
            {
                return $this;
            }
        };

        $provider = new LaravelSocialiteAlipayServiceProvider($app);

        $this->assertTrue(method_exists($provider, 'register'));
    }
}
