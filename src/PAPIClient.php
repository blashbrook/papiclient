<?php

namespace Blashbrook\PAPIClient;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PAPIClient
 * @package Blashbrook\PAPIClient
 */
class PAPIClient extends Client
{

    /**
     * Creates value and hash signature for PAPI Request Authorization header.
     *
     * @param $method - HTTP Request method (GET|POST|PUT)
     * @param $uri - HTTP Request URI
     * @param $papiDate - Polaris server local date and time
     * @return string
     */
    static protected function getHash($method, $uri, $papiDate): string
    {
        //
        return 'PWS ' . config('papiclient.id') . ':'
            . base64_encode(hash_hmac(
                'sha1',
                $method . $uri . $papiDate, config('papiclient.key'),
                true));
    }

    /**
     * Returns date and time formatted for Polaris API
     *
     * @return string
     */
    static protected function getDate(): string
    {
        return Carbon::now()->format('D, d M Y H:i:s \G\M\T');
    }

    /**
     * Returns request headers required for Polaris API authorization
     *
     * @param $method - HTTP Request method (GET|POST|PUT)
     * @param $uri - HTTP Request URI
     * @return array
     */
    static protected function getHeaders($method, $uri): array
    {
        $papiDate = self::getDate();
        $papiToken = self::getHash($method, $uri, $papiDate);
        return ['Accept' => 'application/json',
            'Authorization' => $papiToken,
            'PolarisDate' => $papiDate];
    }

    /**
     * Returns LoginWorkstationID from PAPI_LOGINWORKSTATIONID if it is not supplied.
     *
     * @param null $loginWorkstationID - Polaris WorkstationID
     * @return Repository|Application|mixed
     */
    static protected function setLoginWorkstationID($loginWorkstationID = null)
    {
        return ($loginWorkstationID) ? $loginWorkstationID : config('papiclient.loginWorkstationID');
    }

    /**
     * Returns LogonBranchID from PAPI_LOGONBRANCHID if it is not supplied.
     *
     * @param null $patronBranchID
     * @return Repository|Application|mixed
     */
    static protected function setPatronBranchID($patronBranchID = null)
    {
        return ($patronBranchID) ? $patronBranchID : config('papiclient.logonBranchID');
    }

    /**
     * Adds Library-specific parameters required to make a Polaris API request.
     *
     * The function setPatronBranchID accepts a specified BranchID for Polaris,
     * or defaults to the LogonBranchID environment variable.
     *
     * The function setLoginWorkstationID accepts a specified WorkstationID for Polaris,
     * or defaults to the LoginWorkstationID environment variable.
     *
     * @param $params
     * @return array
     */
    static protected function getPolarisSettings($params): array
    {
       $params = Arr::prepend($params, self::setPatronBranchID(), 'PatronBranchID');
       return  Arr::prepend($params, self::setLoginWorkstationID(), 'LoginWorkstationID');
    }

    /**
     * Sends public request to Polaris API.  Public requests do not
     * require staff usernames and passwords.
     *
     * @param $method - HTTP Request method (GET|POST|PUT)
     * @param $requestURI
     * @param null[] $params - Optional request parameters
     * @return ResponseInterface
     * @throws GuzzleException
     */
    static public function publicRequest($method, $requestURI, array $params = [null]): ResponseInterface
    {
        $uri = config('papiclient.publicURI') . $requestURI;
        $headers = self::getHeaders($method, $uri);
        $client = new Client();
        $json = self::getPolarisSettings($params);
        return $client->request($method, $uri,
            ['headers' => $headers,
                'json' => $json],
        );
    }

    /**
     * Sends protected request to Polaris API.  Protected requests
     * require staff usernames and passwords.
     *
     * TODO: Add method to validate staff Polaris credentials
     *
     * @param $method - HTTP Request method (GET|POST|PUT)
     * @param $uri - HTTP Request URI, without the base URI
     * @param null[] $params - Optional request parameters
     * @return ResponseInterface
     * @throws GuzzleException
     */
    static public function protectedRequest($method, $uri, $params = [null])
    {
        $uri = config('papiclient.protectedURI') . $uri;
        $headers = self::getHeaders($method, $uri);
        $client = new Client();
        return $client->request($method, $uri,
            ['headers' => $headers,
                'json' => $params],
        );
    }
}
