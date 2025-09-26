<?php

namespace Blashbrook\PAPIClient\Tests\Unit;

use Blashbrook\PAPIClient\PAPIClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * PAPIClient Unit Tests
 * 
 * Tests the core functionality of the PAPIClient including method chaining,
 * header generation, authentication, and API request execution.
 * Uses mocked HTTP responses to avoid external API dependencies during testing.
 * 
 * @package Blashbrook\PAPIClient\Tests\Unit
 * @author Brian Lashbrook <blashbrook@gmail.com>
 */
class PAPIClientTest extends TestCase
{
    private PAPIClient $client;
    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up configuration mock
        config([
            'papiclient.id' => 'test_access_id',
            'papiclient.key' => 'test_access_key',
            'papiclient.publicURI' => 'https://api.test.com/public/v1/1033/100/1/',
            'papiclient.protectedURI' => 'https://api.test.com/protected/v1/1033/100/1/',
            'papiclient.logonBranchID' => 1,
            'papiclient.logonWorkstationID' => 1,
            'papiclient.logonUserID' => 1,
        ]);
        
        // Set up mock handler for HTTP requests
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        
        // Create client with mocked handler
        $this->client = new PAPIClient(['handler' => $handlerStack]);
    }

    #[Test]
    public function it_can_instantiate_papiclient()
    {
        $client = new PAPIClient();
        $this->assertInstanceOf(PAPIClient::class, $client);
        $this->assertInstanceOf(Client::class, $client);
    }

    #[Test]
    public function it_can_set_http_method()
    {
        $result = $this->client->method('POST');
        
        $this->assertInstanceOf(PAPIClient::class, $result);
        $this->assertSame($this->client, $result); // Ensures fluent interface
    }

    #[Test]
    public function it_converts_method_to_uppercase()
    {
        $this->client->method('get');
        
        // Use reflection to check private property
        $reflection = new \ReflectionClass($this->client);
        $methodProperty = $reflection->getProperty('method');
        $methodProperty->setAccessible(true);
        
        $this->assertEquals('GET', $methodProperty->getValue($this->client));
    }

    #[Test]
    public function it_can_set_protected_mode()
    {
        $result = $this->client->protected();
        
        $this->assertInstanceOf(PAPIClient::class, $result);
        
        // Check protected flag is set
        $reflection = new \ReflectionClass($this->client);
        $protectedProperty = $reflection->getProperty('protected');
        $protectedProperty->setAccessible(true);
        
        $this->assertTrue($protectedProperty->getValue($this->client));
    }

    #[Test]
    public function it_can_set_patron_barcode()
    {
        $barcode = '1234567890123';
        $result = $this->client->patron($barcode);
        
        $this->assertInstanceOf(PAPIClient::class, $result);
        
        // Check patron property is set
        $reflection = new \ReflectionClass($this->client);
        $patronProperty = $reflection->getProperty('patron');
        $patronProperty->setAccessible(true);
        
        $this->assertEquals($barcode, $patronProperty->getValue($this->client));
    }

    #[Test]
    public function it_can_set_uri_endpoint()
    {
        $uri = 'apikeyvalidate';
        $result = $this->client->uri($uri);
        
        $this->assertInstanceOf(PAPIClient::class, $result);
        
        // Check uri property is set
        $reflection = new \ReflectionClass($this->client);
        $uriProperty = $reflection->getProperty('uri');
        $uriProperty->setAccessible(true);
        
        $this->assertEquals($uri, $uriProperty->getValue($this->client));
    }

    #[Test]
    public function it_can_set_request_parameters()
    {
        $params = ['Barcode' => '1234567890123', 'Password' => 'test123'];
        $result = $this->client->params($params);
        
        $this->assertInstanceOf(PAPIClient::class, $result);
        
        // Check params property is set
        $reflection = new \ReflectionClass($this->client);
        $paramsProperty = $reflection->getProperty('params');
        $paramsProperty->setAccessible(true);
        
        $this->assertEquals($params, $paramsProperty->getValue($this->client));
    }

    #[Test]
    public function it_can_set_access_secret_for_authentication()
    {
        $accessSecret = 'temp_access_secret_token';
        $result = $this->client->auth($accessSecret);
        
        $this->assertInstanceOf(PAPIClient::class, $result);
        
        // Check accessSecret property is set
        $reflection = new \ReflectionClass($this->client);
        $accessSecretProperty = $reflection->getProperty('accessSecret');
        $accessSecretProperty->setAccessible(true);
        
        $this->assertEquals($accessSecret, $accessSecretProperty->getValue($this->client));
    }

    #[Test]
    public function it_supports_fluent_method_chaining()
    {
        $result = $this->client
            ->method('POST')
            ->protected()
            ->patron('1234567890123')
            ->uri('authenticator/patron')
            ->params(['Password' => 'test123'])
            ->auth('access_token');
        
        $this->assertInstanceOf(PAPIClient::class, $result);
        $this->assertSame($this->client, $result);
    }

    #[Test]
    public function it_executes_successful_api_request()
    {
        // Mock successful response
        $expectedData = ['PAPIErrorCode' => 0, 'ErrorMessage' => 'Success'];
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], json_encode($expectedData))
        );

        $response = $this->client
            ->method('GET')
            ->uri('apikeyvalidate')
            ->execRequest();

        $this->assertIsArray($response);
        $this->assertEquals($expectedData, $response);
    }

    #[Test]
    public function it_executes_public_api_request_with_correct_uri()
    {
        // Mock response
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], '{"success": true}')
        );

        $this->client
            ->method('GET')
            ->uri('apikeyvalidate')
            ->execRequest();

        // Get the last request from mock handler
        $lastRequest = $this->mockHandler->getLastRequest();
        $this->assertStringContains('public', $lastRequest->getUri()->__toString());
        $this->assertStringContains('apikeyvalidate', $lastRequest->getUri()->__toString());
    }

    #[Test]
    public function it_executes_protected_api_request_with_correct_uri()
    {
        // Mock response
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], '{"success": true}')
        );

        $this->client
            ->method('GET')
            ->protected()
            ->uri('patron/holds')
            ->execRequest();

        // Get the last request from mock handler
        $lastRequest = $this->mockHandler->getLastRequest();
        $this->assertStringContains('protected', $lastRequest->getUri()->__toString());
        $this->assertStringContains('patron/holds', $lastRequest->getUri()->__toString());
    }

    #[Test]
    public function it_includes_patron_barcode_in_uri_when_specified()
    {
        // Mock response
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], '{"success": true}')
        );

        $barcode = '1234567890123';
        $this->client
            ->method('GET')
            ->protected()
            ->patron($barcode)
            ->uri('holds')
            ->execRequest();

        $lastRequest = $this->mockHandler->getLastRequest();
        $uri = $lastRequest->getUri()->__toString();
        $this->assertStringContains("patron/{$barcode}", $uri);
        $this->assertStringContains('holds', $uri);
    }

    #[Test]
    public function it_sends_correct_headers_for_api_request()
    {
        // Mock response
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], '{"success": true}')
        );

        $this->client
            ->method('GET')
            ->uri('apikeyvalidate')
            ->execRequest();

        $lastRequest = $this->mockHandler->getLastRequest();
        $headers = $lastRequest->getHeaders();

        // Check required headers
        $this->assertArrayHasKey('Accept', $headers);
        $this->assertEquals(['application/json'], $headers['Accept']);
        
        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertStringStartsWith('PWS test_access_id:', $headers['Authorization'][0]);
        
        $this->assertArrayHasKey('PolarisDate', $headers);
        $this->assertNotEmpty($headers['PolarisDate'][0]);
    }

    #[Test]
    public function it_sends_authenticated_headers_when_access_secret_provided()
    {
        // Mock response
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], '{"success": true}')
        );

        $accessSecret = 'temp_access_token';
        $this->client
            ->method('GET')
            ->protected()
            ->patron('1234567890123')
            ->uri('holds')
            ->auth($accessSecret)
            ->execRequest();

        $lastRequest = $this->mockHandler->getLastRequest();
        $headers = $lastRequest->getHeaders();

        // Check authenticated headers
        $this->assertArrayHasKey('X-PAPI-AccessToken', $headers);
        $this->assertEquals([$accessSecret], $headers['X-PAPI-AccessToken']);
    }

    #[Test]
    public function it_sends_post_data_correctly()
    {
        // Mock response
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], '{"PAPIErrorCode": 0}')
        );

        $postData = [
            'Barcode' => '1234567890123',
            'Password' => 'test123'
        ];

        $this->client
            ->method('POST')
            ->protected()
            ->patron('1234567890123')
            ->uri('authenticator/patron')
            ->params($postData)
            ->execRequest();

        $lastRequest = $this->mockHandler->getLastRequest();
        $body = json_decode($lastRequest->getBody()->getContents(), true);

        // Check that Polaris settings are added
        $this->assertArrayHasKey('PatronBranchID', $body);
        $this->assertArrayHasKey('LogonWorkstationID', $body);
        
        // Check that original params are included
        $this->assertArrayHasKey('Barcode', $body);
        $this->assertArrayHasKey('Password', $body);
        $this->assertEquals('1234567890123', $body['Barcode']);
        $this->assertEquals('test123', $body['Password']);
    }

    #[Test]
    public function it_handles_json_response_parsing()
    {
        $expectedData = [
            'PAPIErrorCode' => 0,
            'ErrorMessage' => 'Success',
            'PatronID' => 12345,
            'AccessSecret' => 'temp_token_123'
        ];

        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], json_encode($expectedData))
        );

        $response = $this->client
            ->method('GET')
            ->uri('test')
            ->execRequest();

        $this->assertIsArray($response);
        $this->assertEquals($expectedData['PAPIErrorCode'], $response['PAPIErrorCode']);
        $this->assertEquals($expectedData['ErrorMessage'], $response['ErrorMessage']);
        $this->assertEquals($expectedData['PatronID'], $response['PatronID']);
        $this->assertEquals($expectedData['AccessSecret'], $response['AccessSecret']);
    }

    #[Test]
    public function it_throws_exception_on_http_error()
    {
        // Mock 401 Unauthorized response
        $this->mockHandler->append(
            new Response(401, [], 'Unauthorized')
        );

        $this->expectException(GuzzleException::class);

        $this->client
            ->method('GET')
            ->uri('protected/endpoint')
            ->execRequest();
    }

    #[Test]
    public function it_throws_exception_on_invalid_json()
    {
        // Mock response with invalid JSON
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], 'invalid json content')
        );

        $this->expectException(\JsonException::class);

        $this->client
            ->method('GET')
            ->uri('test')
            ->execRequest();
    }

    #[Test]
    public function it_handles_network_timeout_errors()
    {
        // Mock network timeout
        $this->mockHandler->append(
            new \GuzzleHttp\Exception\ConnectException(
                'Connection timeout',
                new \GuzzleHttp\Psr7\Request('GET', 'test')
            )
        );

        $this->expectException(GuzzleException::class);

        $this->client
            ->method('GET')
            ->uri('test')
            ->execRequest();
    }

    #[Test]
    public function it_resets_state_correctly_between_requests()
    {
        // First request
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], '{"result": "first"}')
        );

        $this->client
            ->method('POST')
            ->protected()
            ->patron('1234567890123')
            ->uri('test1')
            ->params(['param1' => 'value1'])
            ->auth('token1')
            ->execRequest();

        // Second request with different parameters
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], '{"result": "second"}')
        );

        $this->client
            ->method('GET')
            ->uri('test2')
            ->execRequest();

        $lastRequest = $this->mockHandler->getLastRequest();
        
        // Should be GET request (not POST from previous)
        $this->assertEquals('GET', $lastRequest->getMethod());
        
        // Should not include patron in URI
        $this->assertStringNotContains('1234567890123', $lastRequest->getUri()->__toString());
        
        // Should use public URI (not protected from previous)
        $this->assertStringContains('public', $lastRequest->getUri()->__toString());
    }

    #[Test]
    public function it_can_execute_common_api_key_validation_request()
    {
        // Mock successful API key validation
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], 
                json_encode(['PAPIErrorCode' => 0, 'ErrorMessage' => 'Success'])
            )
        );

        $response = $this->client
            ->method('GET')
            ->uri('apikeyvalidate')
            ->execRequest();

        $this->assertEquals(0, $response['PAPIErrorCode']);
        $this->assertEquals('Success', $response['ErrorMessage']);
    }

    #[Test]
    public function it_can_execute_patron_authentication_request()
    {
        // Mock successful patron authentication
        $authResponse = [
            'PAPIErrorCode' => 0,
            'ErrorMessage' => 'Success',
            'PatronID' => 12345,
            'AccessSecret' => 'temp_access_token_123'
        ];

        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], json_encode($authResponse))
        );

        $response = $this->client
            ->method('POST')
            ->protected()
            ->patron('1234567890123')
            ->uri('authenticator/patron')
            ->params(['Password' => 'patron_password'])
            ->execRequest();

        $this->assertEquals(0, $response['PAPIErrorCode']);
        $this->assertEquals(12345, $response['PatronID']);
        $this->assertNotEmpty($response['AccessSecret']);
    }

    #[Test]
    public function it_can_execute_authenticated_patron_data_request()
    {
        // Mock patron holds response
        $holdsResponse = [
            'PAPIErrorCode' => 0,
            'ErrorMessage' => 'Success',
            'PatronHoldsGetRows' => [
                [
                    'HoldRequestID' => 123,
                    'Title' => 'Test Book',
                    'Author' => 'Test Author',
                    'StatusDescription' => 'Ready for pickup'
                ]
            ]
        ];

        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], json_encode($holdsResponse))
        );

        $response = $this->client
            ->method('GET')
            ->protected()
            ->patron('1234567890123')
            ->uri('patron/holds')
            ->auth('temp_access_token_123')
            ->execRequest();

        $this->assertEquals(0, $response['PAPIErrorCode']);
        $this->assertArrayHasKey('PatronHoldsGetRows', $response);
        $this->assertCount(1, $response['PatronHoldsGetRows']);
        $this->assertEquals('Test Book', $response['PatronHoldsGetRows'][0]['Title']);
    }

    #[Test]
    public function it_handles_api_error_responses_gracefully()
    {
        // Mock API error response (valid JSON but with error code)
        $errorResponse = [
            'PAPIErrorCode' => -1001,
            'ErrorMessage' => 'Invalid barcode format'
        ];

        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], json_encode($errorResponse))
        );

        $response = $this->client
            ->method('GET')
            ->protected()
            ->patron('invalid_barcode')
            ->uri('patron/basicdata')
            ->auth('access_token')
            ->execRequest();

        $this->assertEquals(-1001, $response['PAPIErrorCode']);
        $this->assertEquals('Invalid barcode format', $response['ErrorMessage']);
    }
}