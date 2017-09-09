<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\VideoRepository;
use Illuminate\Support\Facades\Storage;

class VideoDeleteTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions, WithoutMiddleware;

    public function test_video_is_deleted()
    {
        Storage::fake('videos');
        
        $repository = app(VideoRepository::class);
        
        $video = $repository->create('1', '1', 'test.mp4', 'video/mp4');

        $video_file = $video->path . '/'.$video->video_id.'.mp4';
        
        Storage::disk('videos')->put($video_file, 'Test Content');

        $this->actingAsApplication(1);

        $response = $this->json('POST', '/api/video.delete', [
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
                    'status' => 'deleted',
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
        
        Storage::disk('videos')->assertMissing($video_file);
    }


}
