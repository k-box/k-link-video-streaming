<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Application;
use Illuminate\Http\Request;
use App\KlinkRegistry\Client;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\KlinkRegistry\Auth\KlinkRegistryUserProvider;
use App\KlinkRegistry\Auth\KlinkRegistryGuard;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class KlinkRegistryGuardTest extends TestCase
{

    public function test_token_and_origin_are_extracted()
    {

        $token = '123456A';
        $domain = 'http://some.domain';

        $expectedCredentialsArray = [
            "api_token" => $token,
            "api_calling_url" => $domain
        ];

        $userProvider = Mockery::mock(KlinkRegistryUserProvider::class);
        
        $userProvider->shouldReceive('retrieveByCredentials')
            ->twice()
            ->with(Mockery::on(function ($argument) use ($expectedCredentialsArray) {
                if( is_array($argument) && count($argument) == 2){
                    return empty(array_diff($argument, $expectedCredentialsArray));
                }

                return false;
            }))
            ->andReturn(new Application([
            'application_id' => 1,
            'url' => $domain,
            'permissions' => ['data-add', 'data-delete-own']
        ]));

        $request = Request::createFromBase(
            SymfonyRequest::create(
                '/endpoint', 
                'POST', 
                ['api_token' => $token], 
                [], 
                [], 
                [ 
                    'HTTP_ORIGIN' => $domain]));

        $guard = new KlinkRegistryGuard($userProvider, $request);

        $isValid = $guard->validate([
            'api_token' => $token,
            'api_calling_url' => $domain]);
        
        $application = $guard->user();

        $this->assertTrue($isValid);
        $this->assertNotNull($application);
        $this->assertEquals(1, $application->getAuthIdentifier());
    }
}
