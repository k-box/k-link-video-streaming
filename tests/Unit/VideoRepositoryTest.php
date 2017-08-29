<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\VideoRepository;
use App\Video;
use Illuminate\Support\Facades\Storage;

class VideoRepositoryTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;
    
    public function test_video_is_created()
    {
        $repository = app(VideoRepository::class);

        $video = $repository->create(1, 1, 'test.mp4', 'video/mp4');

        $this->assertNotNull($video);
        $this->assertInstanceOf(Video::class, $video);
        $this->assertNotNull($video->id);
        $this->assertNotNull($video->video_id);
        $this->assertNotEmpty($video->video_id);

        $this->assertEquals($video->upload_id, 1);
        $this->assertEquals($video->application_id, 1);
        $this->assertEquals($video->original_video_filename, 'test.mp4');
        $this->assertEquals($video->original_video_mimetype, 'video/mp4');

        $this->assertTrue(Storage::disk('local')->exists($video->path));
    }
    
    public function test_video_is_retrieved_by_video_id()
    {
        $repository = app(VideoRepository::class);

        $video = $repository->create('1', '1', 'test.mp4', 'video/mp4');

        $retrieved = $repository->find($video->video_id);

        $this->assertEquals($video->toArray(), $retrieved->toArray());
    }
}
