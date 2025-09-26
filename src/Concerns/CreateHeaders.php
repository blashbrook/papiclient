<?php

namespace Blashbrook\PAPIClient\Concerns;

use Carbon\Carbon;

/**
 * CreateHeaders Trait
 * 
 * Provides methods for creating authentication headers required by the Polaris API.
 * Handles HMAC-SHA1 signature generation and proper header formatting.
 * 
 * @package Blashbrook\PAPIClient\Concerns
 * @author Brian Lashbrook <blashbrook@gmail.com>
 * 
 * @since 1.0.0
 */
trait CreateHeaders
{
    /**
     * Creates HMAC-SHA1 hash signature for PAPI Request Authorization header.
     * 
     * Generates a Base64-encoded HMAC-SHA1 signature using the API access key.
     * The signature is created from the concatenated HTTP method, URI, and timestamp.
     *
     * @param string $method HTTP Request method (GET, POST, PUT, DELETE)
     * @param string $uri Complete HTTP Request URI
     * @param string $papiDate Polaris server date/time in RFC format
     * @return string PWS authorization header value with access ID and signature
     * 
     * @internal This method is used internally by header generation methods
     * @since 1.0.0
     */
    private function getHash($method, $uri, $papiDate): string
    {
            //
        return 'PWS '.config('papiclient.id').':'
            .base64_encode(hash_hmac(
                'sha1',
                $method.$uri.$papiDate, config('papiclient.key'),
                true));
    }

    /**
     * Returns request headers required for Polaris API authorization.
     *
     * @param  $method  - HTTP Request method (GET|POST|PUT)
     * @param  $uri  - HTTP Request URI
     * @return array
     */
    private function getHeaders($method, $uri): array
    {
        $papiDate = $this->getDate();
        $papiToken = $this->getHash($method, $uri, $papiDate);

        return [
            'Accept' => 'application/json',
            'Authorization' => $papiToken,
            'PolarisDate' => $papiDate,
        ];
    }
    /**
     * Returns request headers required for Polaris API patron authentication.
     *
     * @param  $method  - HTTP Request method (GET|POST|PUT)
     * @param  $uri  - HTTP Request URI
     * @return array
     */

    /**
     * Returns date and time formatted for Polaris API.
     *
     * @return string
     */
    private function getDate(): string
    {
        return Carbon::now()->format('D, d M Y H:i:s \G\M\T');
    }

    /**
     * Returns request headers for authenticated patron API calls.
     * 
     * Creates headers that include both API authentication and patron session token.
     * Used for patron-specific operations that require authentication.
     *
     * @param string $method HTTP Request method (GET, POST, PUT, DELETE)
     * @param string $uri Complete HTTP Request URI
     * @param string $accessSecret Patron's temporary authentication token
     * @return array<string, string> Complete request headers with patron authentication
     * 
     * @since 1.0.0
     */
    private function getAuthenticatedPatronHeaders($method, $uri, $accessSecret): array
    {
        $papiDate = $this->getDate();
        $papiToken = $this->getHash($method, $uri, $papiDate);

        return [
            'Accept' => 'application/json',
            'Authorization' => $papiToken,
            'PolarisDate' => $papiDate,
            'X-PAPI-AccessToken' => $accessSecret,
        ];
    }
}
