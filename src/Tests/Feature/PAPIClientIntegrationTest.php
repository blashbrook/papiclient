<?php

namespace Tests\Feature;

    use PAPIClient;
    use Tests\TestCase;

    class PAPIClientIntegrationTest extends TestCase
    {
        public function test_public_request_returns_successful_response()
        {
            // Call the facade method
            $response = PAPIClient::publicRequest('GET', 'patroncodes');

            // Assert the response is OK
            $this->assertEquals(200, $response->getStatusCode());

            // Optionally check the body
            $this->assertArrayHasKey('PatronCodesRows', json_decode($response->getBody(), true));
        }
    }
