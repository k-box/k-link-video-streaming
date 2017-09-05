<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VideoAddTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions, WithoutMiddleware;

    public function test_video_add_request_is_processed()
    {
        Storage::fake('local');

        // Faking application authentication
        $this->actingAsApplication(1);

        $response = $this->json('POST', '/api/video.add', [
            'id' => 'sally',
            'params' => [
                'filename' => 'something.mp4',
                'filesize' => '1000',
                'filetype' => 'video/mp4',
                'title' => 'optional title of something',
            ]
        ]);
        
        $response
            ->assertStatus(202)
            ->assertJson(['request_id' => 'sally'])
            ->assertJsonStructure([
                "video_id",
                "request_id",
                "upload_token",
                "upload_location"
            ]);
    }
}
