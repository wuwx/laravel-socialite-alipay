<?php

namespace Wuwx\LaravelSocialiteAlipay\Tests;

use Illuminate\Http\Request;
use Laravel\Socialite\Two\User;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Wuwx\LaravelSocialiteAlipay\AlipayProvider;

class AlipayProviderTest extends TestCase
{
    private function callProtectedMethod($object, $methodName, $args = [])
    {
        $reflection = new ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        return $method->invokeArgs($object, $args);
    }

    public function testGetAuthUrl()
    {
        $provider = new AlipayProvider(
            new Request(),
            'client_id',
            'client_secret',
            'redirect_url'
        );

        $authUrl = $this->callProtectedMethod($provider, 'getAuthUrl', ['state123']);

        $this->assertStringContainsString('https://openauth.alipay.com/oauth2/publicAppAuthorize.htm', $authUrl);
        $this->assertStringContainsString('state=state123', $authUrl);
    }

    public function testGetTokenUrl()
    {
        $provider = new AlipayProvider(
            new Request(),
            'client_id',
            'client_secret',
            'redirect_url'
        );

        $tokenUrl = $this->callProtectedMethod($provider, 'getTokenUrl');

        $this->assertEquals('https://openapi.alipay.com/gateway.do', $tokenUrl);
    }

    public function testMapUserToObject()
    {
        $provider = new AlipayProvider(
            new Request(),
            'client_id',
            'client_secret',
            'redirect_url'
        );

        $userArray = [
            'user_id' => '2088102123456789',
            'nick_name' => '测试用户',
        ];

        $user = $this->callProtectedMethod($provider, 'mapUserToObject', [$userArray]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('2088102123456789', $user->id);
    }

    public function testGetCodeFields()
    {
        $provider = new AlipayProvider(
            new Request(),
            'client_id',
            'client_secret',
            'redirect_url'
        );

        $fields = $this->callProtectedMethod($provider, 'getCodeFields', ['state123']);

        $this->assertArrayHasKey('app_id', $fields);
        $this->assertEquals('client_id', $fields['app_id']);
        $this->assertArrayNotHasKey('client_id', $fields);
        $this->assertArrayNotHasKey('response_type', $fields);
    }

    public function testGetCode()
    {
        $request = new Request();
        $request->merge(['auth_code' => 'auth_code_123']);

        $provider = new AlipayProvider(
            $request,
            'client_id',
            'client_secret',
            'redirect_url'
        );

        $code = $this->callProtectedMethod($provider, 'getCode');

        $this->assertEquals('auth_code_123', $code);
    }
}
