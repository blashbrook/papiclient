<?php

namespace Blashbrook\PAPIClient\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * PAPIClient Facade
 * 
 * Provides static access to the PAPIClient instance through Laravel's facade system.
 * All methods from the underlying PAPIClient class are available through this facade.
 * 
 * @package Blashbrook\PAPIClient\Facades
 * @author Brian Lashbrook <blashbrook@gmail.com>
 * 
 * @method static \Blashbrook\PAPIClient\PAPIClient method(string $method) Set HTTP method
 * @method static \Blashbrook\PAPIClient\PAPIClient protected() Use protected API endpoints
 * @method static \Blashbrook\PAPIClient\PAPIClient patron(string $barcode) Set patron barcode
 * @method static \Blashbrook\PAPIClient\PAPIClient uri(string $uri) Set API endpoint URI
 * @method static \Blashbrook\PAPIClient\PAPIClient params(array $params) Set request parameters
 * @method static \Blashbrook\PAPIClient\PAPIClient auth(string $accessSecret) Set authentication token
 * @method static array<string, mixed> execRequest() Execute the API request
 * 
 * @example Basic usage:
 *   $response = PAPIClient::method('GET')->uri('apikeyvalidate')->execRequest();
 * 
 * @example Patron authentication:
 *   $result = PAPIClient::protected()->patron('1234567890123')
 *                      ->uri('authenticator/patron')
 *                      ->params(['Password' => 'secret'])
 *                      ->execRequest();
 * 
 * @example Authenticated API call:
 *   $holds = PAPIClient::protected()->patron('1234567890123')
 *                     ->auth($accessToken)
 *                     ->uri('patron/holds')
 *                     ->execRequest();
 * 
 * @see \Blashbrook\PAPIClient\PAPIClient
 * @since 1.0.0
 */
class PAPIClient extends Facade
{
    /**
     * Get the registered name of the component.
     * 
     * Returns the service container binding name for the PAPIClient instance.
     *
     * @return string The facade accessor name
     */
    protected static function getFacadeAccessor(): string
    {
        return 'papiclient';
    }
}
