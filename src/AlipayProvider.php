<?php

namespace Wuwx\LaravelSocialiteAlipay;

use Illuminate\Support\Arr;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class AlipayProvider extends AbstractProvider implements ProviderInterface
{

    protected $scopes = ['auth_user'];

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase("https://openauth.alipay.com/oauth2/publicAppAuthorize.htm", $state);
        //return $this->buildAuthUrlFromBase("https://openauth.alipaydev.com/oauth2/publicAppAuthorize.htm", $state);
    }

    protected function getCodeFields($state = null)
    {
        $fields = parent::getCodeFields($state);

        $fields['app_id'] = $this->clientId;

        unset($fields['client_id']);
        unset($fields['response_type']);
        return $fields;
    }

    protected function getTokenUrl()
    {
        return "https://openapi.alipay.com/gateway.do";
        //return "https://openapi.alipaydev.com/gateway.do";
    }

    protected function getTokenFields($code)
    {
        $fields = [];
        $fields['app_id'] = $this->clientId;
        $fields['method'] = 'alipay.system.oauth.token';
        $fields['charset'] = 'utf8';
        $fields['sign_type'] = 'RSA2';
        $fields['timestamp'] = date("Y-m-d H:i:s");
        $fields['version'] = '1.0';
        $fields['grant_type'] = 'authorization_code';
        $fields['code'] = $code;
        ksort($fields);

        openssl_sign(urldecode(http_build_query($fields)), $fields['sign'], openssl_get_privatekey($this->clientSecret), "sha256");

        $fields['sign'] = base64_encode($fields['sign']);

        return $fields;
    }

    protected function getUserByToken($token)
    {
        $fields = [];
        $fields['app_id'] = $this->clientId;
        $fields['method'] = 'alipay.user.info.share';
        $fields['charset'] = 'utf8';
        $fields['sign_type'] = 'RSA2';
        $fields['timestamp'] = date("Y-m-d H:i:s");
        $fields['version'] = '1.0';
        $fields['auth_token'] = $token;
        ksort($fields);

        openssl_sign(urldecode(http_build_query($fields)), $fields['sign'], openssl_get_privatekey($this->clientSecret), "sha256");

        $fields['sign'] = base64_encode($fields['sign']);

        $response = $this->getHttpClient()->request("GET", "https://openapi.alipay.com/gateway.do", ['query' => $fields]);
        //$response = $this->getHttpClient()->request("GET", "https://openapi.alipaydev.com/gateway.do", ['query' => $fields]);

        $user = Arr::get(json_decode($response->getBody(), true), "alipay_user_info_share_response");

        return $user;
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['user_id'],
            //'name' => $user['name'],
            //'email' => $user['email'],
        ]);
    }

    protected function getCode()
    {
        return $this->request->input('auth_code');
    }

    public function user()
    {
        if ($this->hasInvalidState()) {
            throw new InvalidStateException;
        }

        $response = $this->getAccessTokenResponse($this->getCode());

        $user = $this->mapUserToObject($this->getUserByToken(
            $token = Arr::get($response, 'alipay_system_oauth_token_response.access_token')
        ));

        return $user->setToken($token)
            ->setRefreshToken(Arr::get($response, 'refresh_token'))
            ->setExpiresIn(Arr::get($response, 'expires_in'));
    }

    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->get($this->getTokenUrl(), [
            'query' => $this->getTokenFields($code),
        ]);

        return json_decode($response->getBody(), true);
    }
}
