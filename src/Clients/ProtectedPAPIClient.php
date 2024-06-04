<?php

namespace Blashbrook\PAPIClient\Clients;

use Blashbrook\PAPIClient\Concerns\Headers;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Psr\Http\Message\ResponseInterface;

class ProtectedPAPIClient
{
    use Headers;

    /**
     * @param  $method
     * @param  $uri
     * @param  null[]  $params
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public static function protectedRequest($method, $uri, $params = [null])
    {
        $accessToken = Cache::get('accessToken');
        $accessSecret = Cache::get('accessSecret');
        $papiDate = self::getDate();
        $uri = 'https://catalog.dcplibrary.org/PAPIService/REST/protected/v1/1033/100/3/'.$accessToken.'/sysadmin/mobilephonecarriers';
//        //$uriForHash = config('papiclient.protectedURI') . '/' . $uri;

        $headers = self::getProtectedHeaders($method, $uri, $accessToken, $accessSecret);
        $client = new Client();

        return $client->request($method, $uri,
            ['headers' => $headers]
        );
    }

    /**
     * Creates value and hash signature for PAPI Request Authorization header.
     *
     * @param  $method  - HTTP Request method (GET|POST|PUT)
     * @param  $uri  - HTTP Request URI
     * @param  $papiDate  - Polaris server local date and time
     * @param  $accessSecret
     * @return string
     */
    public static function getProtectedHash($method, $uri, $papiDate, $accessSecret): string
    {
        return 'PWS '.config('papiclient.id').':'
            .base64_encode(hash_hmac(
                'sha1',
                $method.$uri.$papiDate.$accessSecret, config('papiclient.key'),
                true));
    }

    /**
     * Returns request headers required for Polaris API authorization.
     *
     * @param  $method  - HTTP Request method (GET|POST|PUT)
     * @param  $uri  - HTTP Request URI
     * @param  $accessToken
     * @param  $accessSecret
     * @return array
     */
    public static function getProtectedHeaders($method, $uri, $accessToken, $accessSecret): array
    {
        $papiDate = self::getDate();
        $papiToken = self::getProtectedHash($method, $uri, $papiDate, $accessSecret);

        return [
            'Accept' => 'application/json',
            //'X-PAPI-AccessToken' => $accessToken,
            'Authorization' => $papiToken,
            'PolarisDate' => $papiDate,
        ];
    }

    /**
     * Authenticate staff account specified in the environment variables.
     * Upon success, Cache the access token and secret for subsequent requests.
     *
     * @return void
     *
     * @throws GuzzleException
     * @throws \JsonException
     */
    public static function authorizeStaff(): void
    {
        $json = [
            'Domain' => env('PAPI_DOMAIN'),
            'Username' => env('PAPI_STAFF'),
            'Password' => env('PAPI_PASSWORD'),
        ];
        $response = self::getAuthTokens('POST', '/authenticator/staff', $json);
        $body = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $expires = $body['AuthExpDate'];
        $end = substr($expires, 6, 10);
        $seconds = $end - Carbon::now()->timestamp;
        Cache::put('accessToken', $body['AccessToken'], $seconds);
        Cache::put('accessSecret', $body['AccessSecret'], $seconds);
    }

    /**
     * Sends protected request to Polaris API.  Protected requests
     * require staff usernames and passwords.
     *
     * TODO: Add method to validate staff Polaris credentials
     *
     * @param  $method  - HTTP Request method (GET|POST|PUT)
     * @param  $uri  - HTTP Request URI, without the base URI
     * @param  null[]  $params  - Optional request parameters
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public static function getAuthTokens($method, $uri, $params = [null]): ResponseInterface
    {
        $uri = config('papiclient.protectedURI').$uri;
        $headers = self::getHeaders($method, $uri);
        $client = new Client();

        return $client->request($method, $uri,
            [
                'headers' => $headers,
                'json' => $params,
            ],
        );
    }
}
