<?php

    namespace Tests\Feature;

    use PAPIClient;
    use Tests\TestCase;


    class PAPIClientTest extends TestCase
    {
        /**
         * @Test
         * Verifies that the Polaris API Server is online and accessible.
         *
         * @return void
         */
        public function test_PAPIClient_server_is_accessible()
        {
            $response = file_get_contents('https://catalog.dcplibrary.org/polaris');
            $this->assertStringContainsStringIgnoringCase('daviess county', $response);
        }

        /**
         * @Test
         * Validates an PAPIClient access key and request headers returning a 401 on failure and a 200 on success.
         */
        public function test_api_key_is_valid()
        {
            $response = PAPIClient::publicRequest('GET', 'apikeyvalidate');
            $this->assertEquals(json_decode($response->getStatusCode()), '200');
        }

    }
