<?php

namespace Tests\Feature\PAPIClient;

use Blashbrook\PAPIClient\PAPIClient;
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
     *
     */
    public function test_api_key_is_valid()
    {
        $response = PAPIClient::publicRequest('GET', 'apikeyvalidate');
        $this->assertEquals(json_decode($response->getStatusCode()), '200');
    }

    public function test_fails_at_api_server_if_birthdate_format_is_incorrect ()
    {
        $json = [
            'LogonBranchID' => '3',
            'LogonUserID' => '56',
            'LogonWorkstationID' => '1',
            'PatronBranchID' => '3',
            'State' => 'KY',
            'PostalCode' => '42301',
            'Birthdate' => 'beetlejuice',
            'NameFirst' => 'Test',
            'NameMiddle' => 'Joe',
            'NameLast' => 'Test',
            'Barcode' => '0000000001',
        ];

        $response = PAPIClient::publicRequest('POST', 'patron', $json);

        $error = null;
        $body = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        foreach ($body as $key => $value) {
            if ($key === 'ErrorMessage') {
                $error = $value;
            }
        }
        $this->assertEqualsIgnoringCase($error, 'birthdate is invalid');
        $this->assertEquals(json_decode($response->getStatusCode()), '200');

    }

}
