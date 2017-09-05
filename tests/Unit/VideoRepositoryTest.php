<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Avvertix\TusUpload\TusUpload;
use App\VideoRepository;
use App\Video;
use Illuminate\Support\Facades\Storage;

class VideoRepositoryTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;
    
    public function test_video_is_created()
    {
        Storage::fake('videos');

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

        $this->assertTrue(Storage::disk('videos')->exists($video->path));
    }
    
    public function test_video_is_retrieved_by_video_id()
    {
        Storage::fake('videos');

        $repository = app(VideoRepository::class);

        $video = $repository->create('1', '1', 'test.mp4', 'video/mp4');

        $retrieved = $repository->find($video->video_id);

        $this->assertEquals($video->toArray(), $retrieved->toArray());
    }


    public function test_video_is_deleted()
    {
        Storage::fake('videos');

        $repository = app(VideoRepository::class);
        
        $video = $repository->create('1', '1', 'test.mp4', 'video/mp4');

        $video_file = $video->path . '/'.$video->video_id.'.mp4';
        
        Storage::disk('videos')->put($video_file, 'Test Content');

        $deletedVideo = $repository->delete($video->video_id);

        $this->assertInstanceOf(\App\Video::class, $deletedVideo);

        $this->assertNull($repository->find($video->video_id));

        Storage::disk('videos')->assertMissing($video_file);

    }
    
    public function test_video_and_tusupload_are_deleted()
    {
        Storage::fake('videos');
        
        $repository = app(VideoRepository::class);

        $upload = TusUpload::forceCreate([
            'request_id' => 'A',
            'tus_id' => 'A',
            'user_id' => 1,
            'filename' => 'test.mp4',
            'size' => 10,
            'offset' => 10,
            'mimetype' => 'video/mp4',
            'upload_token' => 'AAAAAAAAAAAA',
            'upload_token_expires_at' => \Carbon\Carbon::now()->addHour(),
        ]);
        $upload->completed = true;
        $upload->save();
        
        $video = $repository->create('1', $upload->id, 'test.mp4', 'video/mp4');

        $video_file = $video->path . '/'.$video->video_id.'.mp4';
        
        Storage::disk('videos')->put($video_file, 'Test Content');

        $deletedVideo = $repository->delete($video->video_id);

        $this->assertInstanceOf(\App\Video::class, $deletedVideo);

        $this->assertNull($repository->find($video->video_id));

        Storage::disk('videos')->assertMissing($video_file);
    }
}
