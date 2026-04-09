<?php

namespace Wuwx\LaravelSocialiteAlipay\Tests;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Wuwx\LaravelSocialiteAlipay\AlipayProvider;

class AlipayProviderTest extends TestCase
{
    public function testProviderCanBeInstantiated()
    {
        $provider = new AlipayProvider(
            new Request(),
            'client_id',
            'client_secret',
            'redirect_url'
        );

        $this->assertInstanceOf(AlipayProvider::class, $provider);
    }
}
