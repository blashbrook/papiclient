<?php

namespace Blashbrook\PAPIClient;

use Blashbrook\PAPIClient\Concerns\CreateHeaders;
use Blashbrook\PAPIClient\Concerns\Formatters;
use Blashbrook\PAPIClient\Concerns\GetConfig;
use Blashbrook\PAPIClient\Concerns\ReadResponses;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * PAPIClient - Polaris API Client.
 *
 * A fluent HTTP client for interacting with Polaris ILS API services.
 * Extends GuzzleHttp\Client with specialized methods for Polaris authentication,
 * request building, and response handling.
 *
 * @author Brian Lashbrook <blashbrook@gmail.com>
 *
 * @version 2.0.0
 *
 * @example Basic Usage:
 *   $response = $papiclient->method('GET')->uri('apikeyvalidate')->execRequest();
 * @example Patron Authentication:
 *   $response = $papiclient->protected()->patron('1234567890123')
 *                         ->uri('authenticator/patron')
 *                         ->params(['Password' => 'pass123'])
 *                         ->execRequest();
 * @example Authenticated API Call:
 *   $response = $papiclient->protected()->patron('1234567890123')
 *                         ->auth($accessSecret)
 *                         ->uri('patron/holds')
 *                         ->execRequest();
 *
 * @see https://polaris.polarislibrary.com/api/
 * @since 1.0.0
 */
class PAPIClient extends Client
{
    use CreateHeaders, GetConfig, Formatters, ReadResponses;

    /**
     * HTTP method for the request (GET, POST, PUT, DELETE, etc.).
     *
     * @var string
     */
    protected string $method = 'GET';

    /**
     * API endpoint URI (e.g., 'apikeyvalidate', 'authenticator/patron').
     *
     * @var string
     */
    protected string $uri = '';

    /**
     * Request parameters/payload for POST/PUT requests.
     *
     * @var array<string, mixed>
     */
    protected array $params = [];

    /**
     * Temporary patron authentication token from successful login.
     *
     * @var string|null
     */
    protected ?string $accessSecret = null;

    /**
     * Access token for API authentication (reserved for future use).
     *
     * @var string|null
     */
    protected ?string $accessToken = null;

    /**
     * Patron barcode for patron-specific API calls.
     *
     * @var string|null
     */
    protected ?string $patron = null;

    /**
     * Whether to use protected API endpoints (requires authentication).
     *
     * @var bool
     */
    protected bool $protected = false;

    /**
     * Set the HTTP method for the API request.
     *
     * Supports standard HTTP methods: GET, POST, PUT, DELETE, PATCH, etc.
     * Method is automatically converted to uppercase.
     *
     * @param  string  $method  HTTP method (case-insensitive)
     * @return static Returns this instance for method chaining
     *
     * @example $client->method('GET')->uri('apikeyvalidate');
     * @example $client->method('post')->uri('patron/holds');
     *
     * @since 1.0.0
     */
    public function method(string $method): self
    {
        $this->method = strtoupper($method);

        return $this;
    }

    /**
     *  Replaces the default public API URL with the protected URI.
     *
     * @return $this
     */
    public function protected(): self
    {
        $this->protected = true;

        return $this;
    }

    /**
     * Accepts a Barcode when it is required by the API.
     *
     * @param  string  $barcode
     * @return self
     */
    public function patron(string $barcode): self
    {
        $this->patron = $barcode;

        return $this;
    }

    /**
     * Accepts the function-specific endpoint for an API URL.
     *
     * @param  string  $uri
     * @return $this
     */
    public function uri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Accepts any array, or content, expected by the API call.
     *
     * @param  array  $params
     * @return $this
     */
    public function params(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    /**
     *  Adds the temporary AccessSecret for an authenticated patron to the header of subsequent API calls.
     *
     * @param  string  $accessSecret
     * @return $this
     */
    public function auth(string $accessSecret): self
    {
        $this->accessSecret = $accessSecret;

        return $this;
    }

    /**
     * Execute the configured API request and return the response as an array.
     *
     * This method builds the complete API URL, prepares headers with authentication,
     * sends the HTTP request, and processes the JSON response into a PHP array.
     *
     * The method automatically:
     * - Builds the full URI based on public/protected scope
     * - Adds patron barcode to URI if specified
     * - Includes authentication headers if access secret is provided
     * - Converts request parameters to proper Polaris format
     * - Handles JSON response parsing
     *
     * @return array<string, mixed> Decoded JSON response from the API
     *
     * @throws GuzzleException If HTTP request fails (network, 4xx, 5xx errors)
     * @throws \JsonException If response JSON cannot be decoded
     *
     * @example Basic API validation:
     *   $result = $client->method('GET')->uri('apikeyvalidate')->execRequest();
     * @example Patron authentication:
     *   $result = $client->protected()->patron('1234567890123')
     *                   ->uri('authenticator/patron')
     *                   ->params(['Password' => 'secret'])
     *                   ->execRequest();
     * @example Authenticated patron data retrieval:
     *   $holds = $client->protected()->patron('1234567890123')
     *                  ->auth($accessToken)
     *                  ->uri('patron/holds')
     *                  ->execRequest();
     *
     * @since 1.0.0
     */
    public function execRequest(): array
    {
        $fullUri = $this->protected
            ? config('papiclient.protectedURI')
            : config('papiclient.publicURI');
        if ($this->patron) {
            $fullUri .= 'patron/'.$this->patron;
        }
        $fullUri .= $this->uri;
        $headers = $this->accessSecret
            ? $this->getAuthenticatedPatronHeaders($this->method, $fullUri, $this->accessSecret)
            : $this->getHeaders($this->method, $fullUri);
        $json = $this->getPolarisSettings($this->params);
        $response = $this->request($this->method, $fullUri, [
            'headers' => $headers,
            'json' => $json,
        ]);

        return $this->toArray($response);
    }
}
