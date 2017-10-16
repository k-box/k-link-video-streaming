<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\KlinkRegistry\Client;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegistryClientTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp();

        if(empty(getenv('TEST_REGISTRY_URL')) || 
        empty(getenv('TEST_REGISTRY_SECRET')) || 
        empty(getenv('TEST_REGISTRY_APP_URL'))){
            $this->markTestSkipped("K-Registry for integration tests not configured.");
        }

    }
    
    public function test_application_is_authorized()
    {
        $client = new Client(getenv('TEST_REGISTRY_URL'));

        $secret = getenv('TEST_REGISTRY_SECRET');
        $app_url = getenv('TEST_REGISTRY_APP_URL');

        $app = $client->retrieveApplication($secret, $app_url, ['data-add', 'data-remove-own']);
        
        $this->assertNotEmpty($app->getAuthIdentifier());
    }
}
