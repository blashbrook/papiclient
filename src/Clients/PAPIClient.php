<?php

namespace Blashbrook\PAPIClient\Clients;

use Blashbrook\PAPIClient\Concerns\Headers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PublicPAPIClient.
 */
class PAPIClient extends Client
{
    use Headers;

    /**
     * Returns request headers required for Polaris API authorization.
     *
     * @param  $method  - HTTP Request method (GET|POST|PUT)
     * @param  $uri  - HTTP Request URI
     * @return array
     */

    /**
     * Returns LogonWorkstationID from PAPI_LOGONWORKSTATIONID if it is not supplied.
     *
     * @param  null  $logonWorkstationID  - Polaris WorkstationID
     * @return Repository|Application|mixed
     */
    protected static function setLogonWorkstationID($logonWorkstationID = null)
    {
        return ($logonWorkstationID) ? $logonWorkstationID : config('papiclient.logonWorkstationID');
    }

    /**
     * Returns LogonBranchID from PAPI_LOGONBRANCHID if it is not supplied.
     *
     * @param  null  $patronBranchID
     * @return Repository|Application|mixed
     */
    protected static function setPatronBranchID($patronBranchID = null)
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
     * @param  $params
     * @return array
     */
    protected static function getPolarisSettings($params): array
    {
        $params = Arr::prepend($params, self::setPatronBranchID(), 'PatronBranchID');

        return  Arr::prepend($params, self::setLogonWorkstationID(), 'LogonWorkstationID');
    }

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
    public static function publicRequest($method, $requestURI, array $params = [null])
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
