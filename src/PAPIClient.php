<?php

namespace Blashbrook\PAPIClient;

use Blashbrook\PAPIClient\Concerns\CreateHeaders;
use Blashbrook\PAPIClient\Concerns\Formatters;
use Blashbrook\PAPIClient\Concerns\GetConfig;
use Blashbrook\PAPIClient\Concerns\ReadResponses;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;


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

    /**
     * Accepts request methods like GET or POST.
     *
     * @param  string  $method
     * @return $this
     */
    public function method(string $method): self
    {
        $this->method = strtoupper($method);

        return $this;
    }

    /**
     *  Replaces the default public API URL with the protected URI
     *
     * @return $this
     */
    public function protected(): self
    {
        $this->protected = true;

        return $this;
    }

    /**
     * Accepts a Barcode when it is required by the API
     *
     * @param  string  $barcode
     * @return self
     */
    public function patron(string $barcode): self
    {
        $this->patron = $barcode;
    }

    /**
     * Accepts the function-specific endpoint for an API URL
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
     * @throws GuzzleException
     * @throws \JsonException
     */
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

}
