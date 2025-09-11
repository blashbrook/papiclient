<?php

namespace Blashbrook\PAPIClient;

use Blashbrook\PAPIClient\Concerns\CreateHeaders;
use Blashbrook\PAPIClient\Concerns\Formatters;
use Blashbrook\PAPIClient\Concerns\GetConfig;
use Blashbrook\PAPIClient\Concerns\ReadResponses;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PublicPAPIClient.
 */
class PAPIClient extends Client
{
    use CreateHeaders, GetConfig, Formatters, ReadResponses;

    protected string $method = 'GET';
    protected string $uri = '';
    protected array $params = [];
    protected ?string $accessSecret = null;
    protected ?string $accessToken = null;
    protected ?string $patron = null;
    protected bool $protected = false;

    public function method(string $method): self
    {
        $this->method = strtoupper($method);

        return $this;
    }

    public function protected(): static
    {
        $this->protected = true;

        return $this;
    }

    public function patron(string $barcode)
    {
        $this->patron = $barcode;
    }

    public function uri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function params(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    public function auth(string $accessSecret): self
    {
        $this->accessSecret = $accessSecret;

        return $this;
    }

    public function execRequest(): array
    {
        $fullUri = $this->protected
            ? config('papiclient.protectedURI')
            : config('papiclient.publicURI');
        $fullUri .= $this->uri;
        if ($this->patron) {
            $fullUri .= 'patron/'.$this->patron;
        }
        $headers = $this->accessSecret
            ? self::getAuthenticatedPatronHeaders($this->method, $fullUri, $this->accessSecret)
            : self::getHeaders($this->method, $fullUri);
        $json = self::getPolarisSettings($this->params);
        $response = $this->request($this->method, $fullUri, [
            'headers' => $headers,
            'json' => $json,
        ]);

        return $this->toArray($response);
    }

    /**
     * Sends public request to Polaris API.  Public requests do not
     * require staff usernames and passwords.
     *
     * @param  $method  - HTTP Request method (GET|POST|PUT)
     * @param  $requestURI  - function-specific part of API Endpoint
     * @param  null[]  $params  - Optional request parameters
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
     * require the AccessSecret returned from a successful patron authentication.
     *
     * @param  $method  - HTTP Request method (GET|POST|PUT)
     * @param  $requestURI  - function-specific part of API Endpoint
     * @param  $accessSecret  - AccessSecret returned after patron authentication
     * @param  null[]  $params  - Optional request parameters
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    public static function authenticatedPatronRequest($method, $requestURI, $accessSecret, array $params = [null])
    {
        $uri = config('papiclient.publicURI').$requestURI;
        $headers = self::getAuthenticatedPatronHeaders($method, $uri, $accessSecret);
        $client = new Client();
        $json = self::getPolarisSettings($params);

        return $client->request($method, $uri,
            ['headers' => $headers,
                'json' => $json, ],
        );
    }

    /**
     * Formats timestamp in milliseconds to YYYY-MM-DD.
     *
     * @param  $timestamp
     * @return string
     */
    /*    public static function formatDate($timestamp): string
        {
            return Carbon::createFromTimestampMs($timestamp)->format('Y-m-d');
        }*/
}
