<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Application;
use App\KlinkRegistry\Client;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\KlinkRegistry\Auth\KlinkRegistryUserProvider;


class KlinkRegistryUserProviderTest extends TestCase
{
    public function test_provider_retrieves_by_credential()
    {
        $client = Mockery::mock(Client::class);
        
        $client->shouldReceive('retrieveApplication')->once()->andReturn(new Application([
            'application_id' => 1,
            'url' => 'http://some.domain',
            'permissions' => ['data-add', 'data-delete-own']
        ]));

        $userProvider = new KlinkRegistryUserProvider($client);

        $application = $userProvider->retrieveByCredentials([
            'api_token' => '123456A',
            'api_calling_url' => 'http://some.domain']);

        $this->assertNotNull($application);
        $this->assertEquals(1, $application->getAuthIdentifier());
    }
}
