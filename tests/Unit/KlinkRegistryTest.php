<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use App\KlinkRegistry\Registry;

class KlinkRegistryTest extends TestCase
{
    public function test_authentication_driver_and_user_provider_are_registered()
    {
        Auth::shouldReceive('provider')->once();
        Auth::shouldReceive('extend')->once();

        Registry::auth();
    }
}
