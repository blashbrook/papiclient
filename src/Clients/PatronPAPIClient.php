<?php

namespace Blashbrook\PAPIClient\Clients;

use Blashbrook\PAPIClient\Concerns\Config;
use Blashbrook\PAPIClient\Concerns\Headers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PublicPAPIClient.
 */
class PatronPAPIClient extends Client
{
    use Headers, Config;

    /**
     * Sends public request to Polaris API.  Public requests do not
     * require staff usernames and passwords.
     *
     * @param  $method  - HTTP Request method (GET|POST|PUT)
     * @param  $requestURI
     * @param  null[]  $params  - Optional request parameters
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public static function patronRequest($method, $requestURI, array $params = [null])
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
}
