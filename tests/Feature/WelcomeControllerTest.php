<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WelcomeControllerTest extends TestCase
{
    public function test_welcome_page_loads()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
