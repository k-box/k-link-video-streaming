<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\VideoRepository;
use Illuminate\Support\Facades\Storage;

class VideoGetTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions, WithoutMiddleware;

    public function test_video_is_retrieved_in_json_form()
    {
        Storage::fake('videos');

        $repository = app(VideoRepository::class);
        
        $video = $repository->create('1', '1', 'test.mp4', 'video/mp4');

        $this->actingAsApplication(1);

        $response = $this->json('POST', '/api/video.get', [
            'id' => 'sally',
            'params' => [
                'video_id' => $video->video_id,
            ]
        ]);
        
        $response
            ->assertStatus(200)
            ->assertJson([
                'request_id' => 'sally',
            ])
            ->assertJson([
                "response" => [
                    'video_id' => $video->video_id,
                    'status' => $video->status,
                ]
            ])
            ->assertJsonStructure([
                "request_id",
                "response" => [
                    "video_id",
                    "status",
                    "created_at"
                ]
            ]);
        
    }

    public function test_video_not_found_exception_thrown()
    {
        Storage::fake('videos');

        $this->actingAsApplication(1);

        $response = $this->json('POST', '/api/video.get', [
            'id' => 'sally',
            'params' => [
                'video_id' => '123456',
            ]
        ]);
        
        $response->assertStatus(422);
        
    }
}
