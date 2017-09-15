<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\VideoRepository;
use Illuminate\Support\Facades\Storage;

class VideoEmbedControllerTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions, WithoutMiddleware;

    public function test_embed_page_loads()
    {
        Storage::fake('videos');
        
        $repository = app(VideoRepository::class);
        
        $video = $repository->create('1', '1', 'test.mp4', 'video/mp4');

        // Fake that video upload and processing has been completed
        $video->completed = true;
        $video->save();

        $this->actingAsApplication(1);

        $response = $this->get('/embed/' . $video->video_id);
        
        $response->assertViewIs('embed');
        $response->assertViewHas('video');
        $response->assertSee('data-dash="'. $video->dash_stream .'"');
        
    }

    public function test_embed_page_loads_if_video_is_processing()
    {
        Storage::fake('videos');
        
        $repository = app(VideoRepository::class);
        
        $video = $repository->create('1', '1', 'test.mp4', 'video/mp4');

        $this->actingAsApplication(1);

        $response = $this->get('/embed/' . $video->video_id);
        
        $response->assertViewIs('embed');
        $response->assertViewHas('video');
        $response->assertDontSee('data-dash');
        
        
    }
}
