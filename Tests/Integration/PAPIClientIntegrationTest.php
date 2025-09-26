<?php

namespace Blashbrook\PAPIClient\Tests\Integration;

use Blashbrook\PAPIClient\PAPIClient;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * PAPIClient Integration Tests.
 *
 * Tests real API interactions with the Polaris system.
 * These tests require actual PAPI credentials and may make real API calls.
 *
 * WARNING: These tests should only be run against a development/test environment.
 * Set ENABLE_INTEGRATION_TESTS=true in your environment to run these tests.
 *
 * @author Brian Lashbrook <blashbrook@gmail.com>
 *
 * @group integration
 */
#[Group('integration')]
class PAPIClientIntegrationTest extends TestCase
{
    private PAPIClient $client;
    private bool $integrationTestsEnabled;

    protected function setUp(): void
    {
        parent::setUp();

        $this->integrationTestsEnabled = env('ENABLE_INTEGRATION_TESTS', false);

        if (! $this->integrationTestsEnabled) {
            $this->markTestSkipped('Integration tests are disabled. Set ENABLE_INTEGRATION_TESTS=true to enable.');
        }

        // Verify required configuration is present
        $requiredConfig = ['id', 'key', 'publicURI', 'protectedURI'];
        foreach ($requiredConfig as $configKey) {
            if (empty(config("papiclient.{$configKey}"))) {
                $this->markTestSkipped("Missing required configuration: papiclient.{$configKey}");
            }
        }

        $this->client = new PAPIClient();
    }

    #[Test]
    public function it_can_validate_api_key_with_real_api()
    {
        if (! $this->integrationTestsEnabled) {
            $this->markTestSkipped('Integration tests disabled');
        }

        $response = $this->client
            ->method('GET')
            ->uri('apikeyvalidate')
            ->execRequest();

        // Should return successful validation
        $this->assertIsArray($response);
        $this->assertArrayHasKey('PAPIErrorCode', $response);
        $this->assertEquals(0, $response['PAPIErrorCode'],
            'API key validation failed: '.($response['ErrorMessage'] ?? 'Unknown error'));
    }

    #[Test]
    public function it_handles_invalid_endpoint_gracefully()
    {
        if (! $this->integrationTestsEnabled) {
            $this->markTestSkipped('Integration tests disabled');
        }

        try {
            $response = $this->client
                ->method('GET')
                ->uri('nonexistent/endpoint')
                ->execRequest();

            // Should handle the error gracefully
            $this->assertIsArray($response);

            // May return error code or throw exception based on API behavior
            if (isset($response['PAPIErrorCode'])) {
                $this->assertNotEquals(0, $response['PAPIErrorCode']);
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            // HTTP errors (404, 500, etc.) are also acceptable for invalid endpoints
            $this->assertNotEmpty($e->getMessage());
        }
    }

    #[Test]
    public function it_can_handle_patron_authentication_attempt()
    {
        if (! $this->integrationTestsEnabled) {
            $this->markTestSkipped('Integration tests disabled');
        }

        // Use invalid credentials to test authentication flow without actually authenticating
        $response = $this->client
            ->method('POST')
            ->protected()
            ->patron('0000000000000') // Invalid barcode
            ->uri('authenticator/patron')
            ->params(['Password' => 'invalid_password'])
            ->execRequest();

        // Should return authentication failure
        $this->assertIsArray($response);
        $this->assertArrayHasKey('PAPIErrorCode', $response);
        $this->assertNotEquals(0, $response['PAPIErrorCode'],
            'Expected authentication to fail with invalid credentials');
        $this->assertArrayHasKey('ErrorMessage', $response);
        $this->assertNotEmpty($response['ErrorMessage']);
    }

    #[Test]
    public function it_requires_authentication_for_protected_endpoints()
    {
        if (! $this->integrationTestsEnabled) {
            $this->markTestSkipped('Integration tests disabled');
        }

        try {
            // Try to access protected endpoint without authentication
            $response = $this->client
                ->method('GET')
                ->protected()
                ->patron('1234567890123')
                ->uri('patron/basicdata')
                ->execRequest();

            // Should either return error or throw exception
            if (is_array($response) && isset($response['PAPIErrorCode'])) {
                $this->assertNotEquals(0, $response['PAPIErrorCode'],
                    'Expected protected endpoint to require authentication');
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            // HTTP 401/403 errors are expected for unauthenticated requests
            $this->assertTrue(
                str_contains($e->getMessage(), '401') || str_contains($e->getMessage(), '403'),
                'Expected 401 or 403 error for unauthenticated protected endpoint access'
            );
        }
    }

    #[Test]
    public function it_maintains_request_state_correctly()
    {
        if (! $this->integrationTestsEnabled) {
            $this->markTestSkipped('Integration tests disabled');
        }

        // First request - protected
        try {
            $this->client
                ->method('POST')
                ->protected()
                ->patron('test123')
                ->uri('authenticator/patron')
                ->params(['Password' => 'test'])
                ->execRequest();
        } catch (\Exception $e) {
            // Ignore authentication failures for this test
        }

        // Second request - should be independent (public)
        $response = $this->client
            ->method('GET')
            ->uri('apikeyvalidate')
            ->execRequest();

        // Should succeed with public endpoint
        $this->assertIsArray($response);
        $this->assertArrayHasKey('PAPIErrorCode', $response);
        $this->assertEquals(0, $response['PAPIErrorCode'],
            'Public endpoint should work after protected endpoint attempt');
    }

    #[Test]
    public function it_handles_malformed_requests_gracefully()
    {
        if (! $this->integrationTestsEnabled) {
            $this->markTestSkipped('Integration tests disabled');
        }

        try {
            // Try malformed POST data
            $response = $this->client
                ->method('POST')
                ->protected()
                ->uri('authenticator/patron')
                ->params(['InvalidField' => 'InvalidValue'])
                ->execRequest();

            // Should handle malformed request gracefully
            $this->assertIsArray($response);
            if (isset($response['PAPIErrorCode'])) {
                $this->assertNotEquals(0, $response['PAPIErrorCode']);
                $this->assertArrayHasKey('ErrorMessage', $response);
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            // HTTP errors for malformed requests are also acceptable
            $this->assertNotEmpty($e->getMessage());
        }
    }

    #[Test]
    public function it_handles_server_errors_gracefully()
    {
        if (! $this->integrationTestsEnabled) {
            $this->markTestSkipped('Integration tests disabled');
        }

        // This test may not always trigger a server error,
        // but should handle any server errors that do occur
        try {
            $response = $this->client
                ->method('GET')
                ->uri('apikeyvalidate')
                ->execRequest();

            // If successful, verify response structure
            if (is_array($response)) {
                $this->assertArrayHasKey('PAPIErrorCode', $response);
            }
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Server errors (5xx) should be caught and handled
            $this->assertGreaterThanOrEqual(500, $e->getResponse()->getStatusCode());
            $this->assertLessThan(600, $e->getResponse()->getStatusCode());
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            // Other HTTP errors should also be handled gracefully
            $this->assertNotEmpty($e->getMessage());
        }
    }

    #[Test]
    public function it_can_handle_different_http_methods()
    {
        if (! $this->integrationTestsEnabled) {
            $this->markTestSkipped('Integration tests disabled');
        }

        // Test GET request
        $getResponse = $this->client
            ->method('GET')
            ->uri('apikeyvalidate')
            ->execRequest();

        $this->assertIsArray($getResponse);
        $this->assertEquals(0, $getResponse['PAPIErrorCode'] ?? -1);

        // Test POST request (will fail authentication but should handle method correctly)
        try {
            $postResponse = $this->client
                ->method('POST')
                ->protected()
                ->patron('invalid')
                ->uri('authenticator/patron')
                ->params(['Password' => 'invalid'])
                ->execRequest();

            // Should return structured response even for failed authentication
            $this->assertIsArray($postResponse);
            $this->assertArrayHasKey('PAPIErrorCode', $postResponse);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            // HTTP errors are also acceptable
            $this->assertNotEmpty($e->getMessage());
        }
    }
}

/**
 * PAPIClient Performance Tests.
 *
 * Tests performance characteristics and resource usage.
 * These tests help ensure the client performs well under various conditions.
 *
 * @group performance
 */
#[Group('performance')]
class PAPIClientPerformanceTest extends TestCase
{
    private PAPIClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        if (! env('ENABLE_INTEGRATION_TESTS', false)) {
            $this->markTestSkipped('Performance tests require integration test environment');
        }

        $this->client = new PAPIClient();
    }

    #[Test]
    public function it_completes_api_key_validation_within_reasonable_time()
    {
        $startTime = microtime(true);

        $response = $this->client
            ->method('GET')
            ->uri('apikeyvalidate')
            ->execRequest();

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        // Should complete within 5 seconds (adjust based on your network/server)
        $this->assertLessThan(5.0, $duration,
            "API key validation took {$duration} seconds, which is too long");

        $this->assertIsArray($response);
        $this->assertEquals(0, $response['PAPIErrorCode'] ?? -1);
    }

    #[Test]
    public function it_can_handle_multiple_sequential_requests()
    {
        $startTime = microtime(true);
        $requests = 5;

        for ($i = 0; $i < $requests; $i++) {
            $response = $this->client
                ->method('GET')
                ->uri('apikeyvalidate')
                ->execRequest();

            $this->assertIsArray($response);
            $this->assertEquals(0, $response['PAPIErrorCode'] ?? -1);
        }

        $endTime = microtime(true);
        $totalDuration = $endTime - $startTime;
        $averageDuration = $totalDuration / $requests;

        // Each request should average less than 2 seconds
        $this->assertLessThan(2.0, $averageDuration,
            "Average request time of {$averageDuration} seconds is too slow");

        // Total time should be reasonable
        $this->assertLessThan(10.0, $totalDuration,
            "Total time for {$requests} requests ({$totalDuration}s) is excessive");
    }

    #[Test]
    public function it_does_not_leak_memory_during_multiple_requests()
    {
        $initialMemory = memory_get_usage(true);

        // Perform multiple requests
        for ($i = 0; $i < 10; $i++) {
            try {
                $this->client
                    ->method('GET')
                    ->uri('apikeyvalidate')
                    ->execRequest();
            } catch (\Exception $e) {
                // Ignore errors for this test
            }
        }

        $finalMemory = memory_get_usage(true);
        $memoryIncrease = $finalMemory - $initialMemory;

        // Memory increase should be minimal (less than 1MB)
        $this->assertLessThan(1024 * 1024, $memoryIncrease,
            'Memory usage increased by '.number_format($memoryIncrease).' bytes, indicating possible memory leak');
    }
}
