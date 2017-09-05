<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Video;
use Avvertix\TusUpload\TusUpload;

class VideoTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function test_video_completed_attribute_set()
    {
        $video = (new Video())->forceFill(['id' => 1, 'video_id' => 'aaa']);

        $this->assertFalse($video->completed);
        $this->assertFalse($video->completed());
        $this->assertNull($video->completed_at);
        $this->assertNotEquals(Video::STATUS_COMPLETED, $video->status);
        
        $video->completed = true;
        
        $this->assertTrue($video->completed);
        $this->assertTrue($video->completed());
        $this->assertNotNull($video->completed_at);
        $this->assertNotNull($video->poster);
        $this->assertNotNull($video->url);
        $this->assertNotNull($video->dash_stream);
        $this->assertEquals(Video::STATUS_COMPLETED, $video->status);
    }

    public function test_video_cancelled_attribute_set()
    {
        $video = (new Video())->forceFill(['id' => 1, 'video_id' => 'aaa']);
        
        $this->assertFalse($video->cancelled);
        $this->assertFalse($video->cancelled());
        $this->assertNull($video->cancelled_at);
        $this->assertNotEquals(Video::STATUS_CANCELLED, $video->status);
        
        $video->cancelled = true;
        
        $this->assertTrue($video->cancelled);
        $this->assertTrue($video->cancelled());
        $this->assertNotNull($video->cancelled_at);
        $this->assertNull($video->poster);
        $this->assertNull($video->dash_stream);
        $this->assertNotNull($video->url);
        $this->assertEquals(Video::STATUS_CANCELLED, $video->status);
    }

    public function test_video_failed_at_attribute_set()
    {
        $video = (new Video())->forceFill(['id' => 1, 'video_id' => 'aaa']);
        
        $this->assertFalse($video->failed);
        $this->assertFalse($video->failed());
        $this->assertNull($video->failed_at);
        $this->assertNotEquals(Video::STATUS_FAILED, $video->status);
        
        $video->fail_reason = "An example failure";
        
        $this->assertTrue($video->failed);
        $this->assertTrue($video->failed());
        $this->assertNotNull($video->failed_at);
        $this->assertNull($video->poster);
        $this->assertNull($video->dash_stream);
        $this->assertNotNull($video->url);
        $this->assertEquals(Video::STATUS_FAILED, $video->status);
    }

    public function test_video_queued_at_attribute_set()
    {
        $video = (new Video())->forceFill(['id' => 1, 'video_id' => 'aaa']);
        
        $this->assertFalse($video->queued);
        $this->assertFalse($video->queued());
        $this->assertNull($video->queued_at);
        $this->assertNotEquals(Video::STATUS_QUEUED, $video->status);
        
        $video->queued = true;
        
        $this->assertTrue($video->queued);
        $this->assertTrue($video->queued());
        $this->assertNotNull($video->queued_at);
        $this->assertNull($video->poster);
        $this->assertNull($video->dash_stream);
        $this->assertNotNull($video->url);
        $this->assertEquals(Video::STATUS_QUEUED, $video->status);
    }

    public function test_video_has_pending_status()
    {
        $video = (new Video())->forceFill(['id' => 1, 'video_id' => 'aaa']);

        $this->assertEquals(Video::STATUS_PENDING, $video->status);
        $this->assertNull($video->poster);
        $this->assertNull($video->dash_stream);
        $this->assertNotNull($video->url);
    }

    public function test_video_has_uploading_status()
    {

        $tusId = str_random(10);
        
        $upload = TusUpload::forceCreate([
            'id' => 1,
            'user_id' => 1,
            'request_id' => 'A1',
            'tus_id' => $tusId,
            'filename' => 'test.pdf',
            'size' => 100,
            'offset' => 10,
            'mimetype' => null,
            'metadata' => null,
            'upload_token' => str_random(60),
            'upload_token_expires_at' => \Carbon\Carbon::now()->addHour(),
        ]);

        $video = (new Video())->forceFill([
            'id' => 1, 
            'application_id' => $upload->user_id, 
            'upload_id' => $upload->id, 
            'video_id' => 'aaa'
        ]);

        $this->assertEquals(Video::STATUS_UPLOADING, $video->status);
    }
}
