<?php

namespace Tests\Feature;

use App\Video;
use Tests\TestCase;
use App\VideoRepository;
use App\Jobs\ConvertVideo;
use Illuminate\Support\Facades\Storage;
use App\VideoProcessing\VideoProcessorFactory;
use Tests\Feature\Fixtures\FakeVideoProcessorFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConvertVideoTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function test_video_is_converted_and_mdp_file_is_generated()
    {
        Storage::fake('local');

        $this->app->instance(VideoProcessorFactory::class, $factory = new FakeVideoProcessorFactory);

        // generate a video
        $repository = app(VideoRepository::class);
        
        $video = $repository->create('1', '1', '20170813.mp4', 'video/mp4');
    
        $video_file = $video->path . $video->video_id.'.mp4';
        $expected_mdp_file = $video->path . $video->video_id.'.mdp';
        
        Storage::disk('local')->put($video_file, 'Test Content');

        // start the ConvertVideo job

        $job = (new ConvertVideo($video))->onQueue('video-processing');
        
        dispatch($job);

        $video = $video->fresh();

        // make sure in the storage there is the mdp file
        Storage::disk('local')->assertExists($expected_mdp_file);
        
        // make sure video status is set to COMPLETED
        $this->assertEquals(Video::STATUS_COMPLETED, $video->status);
    }

    public function test_video_thumbnail_is_generated()
    {
        Storage::fake('local');

        $this->app->instance(VideoProcessorFactory::class, $factory = new FakeVideoProcessorFactory);

        // generate a video
        $repository = app(VideoRepository::class);
        
        $video = $repository->create('1', '1', '20170813.mp4', 'video/mp4');
    
        $video_file = $video->path . $video->video_id.'.mp4';
        $expected_thumbnail_file = $video->path . $video->video_id.'.jpg';
        
        Storage::disk('local')->put($video_file, 'Test Content');

        // start the ConvertVideo job

        $job = (new ConvertVideo($video))->onQueue('video-processing');
        
        dispatch($job);

        $video = $video->fresh();

        // make sure in the storage there is the mdp file
        Storage::disk('local')->assertExists($expected_thumbnail_file);
        
        // make sure video status is set to COMPLETED
        $this->assertEquals(Video::STATUS_COMPLETED, $video->status);
    }
    
    public function test_video_conversion_error_is_handled()
    {
        Storage::fake('local');

        // generate a video
        $repository = app(VideoRepository::class);
        
        $video = $repository->create('1', '1', 'test.mp4', 'video/mp4');
    
        $video_file = $video->path . '/'.$video->video_id.'11.mp4';
        $expected_mdp_file = $video->path . '/'.$video->video_id.'.mdp';
        
        Storage::disk('local')->put($video_file, 'Test Content');

        // start the ConvertVideo job

        $job = (new ConvertVideo($video))->onQueue('video-processing');
        
        dispatch($job);

        $video = $video->fresh();

        // make sure in the storage there is the mdp file
        Storage::disk('local')->assertMissing($expected_mdp_file);
        
        // make sure video status is set to COMPLETED
        $this->assertEquals(Video::STATUS_FAILED, $video->status);
        $this->assertNotEmpty($video->fail_reason);
    }
}
