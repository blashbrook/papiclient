<?php

namespace Blashbrook\PAPIClient;

use Blashbrook\PAPIClient\Concerns\Config;
use Blashbrook\PAPIClient\Concerns\Formatters;
use Blashbrook\PAPIClient\Concerns\Headers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PublicPAPIClient.
 */
class PAPIClient extends Client
{
    use Headers, Config, Formatters;

    /**
     * Sends public request to Polaris API.  Public requests do not
     * require staff usernames and passwords.
     *
     * @param  $method  - HTTP Request method (GET|POST|PUT)
     * @param  $requestURI - function-specific part of API Endpoint
     * @param  null[]  $params  - Optional request parameters
     *
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public function publicRequest($method, $requestURI, array $params = [null])
    {
        $uri = config('papiclient.publicURI').$requestURI;
        $headers = self::getHeaders($method, $uri);
        $client = new Client();
        $json = self::getPolarisSettings($params);

        return $client->request($method, $uri,
            ['headers' => $headers,
                'json' => $json, ],
        );
    }

    /**
     * Sends an authenticated patron's request to Polaris API.  Public patron requests
     * require the AccessSecret returned from a successful patron authentication
     *
     * @param  $method  - HTTP Request method (GET|POST|PUT)
     * @param  $requestURI - function-specific part of API Endpoint
     * @param $accessSecret - AccessSecret returned after patron authentication
     * @param  null[]  $params  - Optional request parameters
     *
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public static function authenticatedPatronRequest($method, $requestURI, $accessSecret, array $params = [null])
    {
        $uri = PAPIClient.phpconfig('papiclient.publicURI').$requestURI;
        $headers = self::getAuthenticatedPatronHeaders($method, $uri, $accessSecret);
        $client = new Client();
        $json = self::getPolarisSettings($params);

        return $client->request($method, $uri,
            ['headers' => $headers,
                'json' => $json, ],
        );
    }

    /**
     * Formats timestamp in milliseconds to YYYY-MM-DD
     *
     * @param $timestamp
     * @return string
     */
/*    public static function formatDate($timestamp): string
    {
        return Carbon::createFromTimestampMs($timestamp)->format('Y-m-d');
    }*/

}
