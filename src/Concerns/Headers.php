<?php

namespace Blashbrook\PAPIClient\Concerns;

use Carbon\Carbon;

trait Headers
{
    /**
     * Creates value and hash signature for PAPI Request Authorization header.
     *
     * @param  $method  - HTTP Request method (GET|POST|PUT)
     * @param  $uri  - HTTP Request URI
     * @param  $papiDate  - Polaris server local date and time
     * @return string
     */
    protected static function getHash($method, $uri, $papiDate): string
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
    protected static function getHeaders($method, $uri): array
    {
        $papiDate = self::getDate();
        $papiToken = self::getHash($method, $uri, $papiDate);

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
    protected static function getDate(): string
    {
        return Carbon::now()->format('D, d M Y H:i:s \G\M\T');
    }

    protected static function getAuthenticatedPatronHeaders($method, $uri, $accessSecret): array
    {
        $papiDate = self::getDate();
        $papiToken = self::getHash($method, $uri, $papiDate);

        return [
            'Accept' => 'application/json',
            'Authorization' => $papiToken,
            'PolarisDate' => $papiDate,
            'X-PAPI-AccessToken' => $accessSecret,
        ];
    }
}
