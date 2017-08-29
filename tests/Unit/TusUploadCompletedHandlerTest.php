<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Video;
use App\VideoRepository;
use Avvertix\TusUpload\TusUpload;
use Avvertix\TusUpload\Events\TusUploadCompleted;
use App\Listeners\TusUploadCompletedHandler;
use Carbon\Carbon;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ConvertVideo;

class TusUploadCompletedHandlerTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    private $video = null;

    public function setUp()
    {
        parent::setUp();

        config([
            'tusupload.storage' => storage_path('')
        ]);
    }

    private function createEvent($applicationId, $requestId = '14b1c4c77771671a8479bc0444bbc5ce')
    {
        $path = storage_path(str_slug($requestId).'.bin');

        file_put_contents($path, 'Test File Content');

        $size = filesize($path);

        $upload = TusUpload::forceCreate([
            'request_id' => $requestId,
            'tus_id' => str_slug($requestId),
            'user_id' => $applicationId,
            'filename' => basename($path).'.mp4',
            'size' => $size,
            'offset' => $size,
            'mimetype' => 'video/mp4',
            'upload_token' => 'AAAAAAAAAAAA',
            'upload_token_expires_at' => Carbon::now()->addHour(),
        ]);

        $upload->completed = true;
        $upload->save();
        
        $repository = app(VideoRepository::class);
        
        $this->video = $repository->create(1, $upload->id, basename($path).'.mp4', 'video/mp4');

        return new TusUploadCompleted($upload->fresh());
    }

    public function test_video_processing_is_queued_and_file_is_available()
    {
        // Queue::fake();

        $request_id = 'REQUEST';

        $completed_event = $this->createEvent(1, $request_id);

        $listener = app(TusUploadCompletedHandler::class);

        $listener->handle($completed_event);

        $video = $this->video = $this->video->fresh();
        
        $this->assertNotNull($this->video);
        $this->assertNotNull($this->video->queued_at);
        
        $this->assertEquals(Video::STATUS_QUEUED, $this->video->status);

        $videoFile = $this->video->file();

        $this->assertNotNull($videoFile);
        $this->assertTrue($videoFile->isFile());

        // Queue::assertPushed(ConvertVideo::class, function ($job) use ($video) {
        //     return $job->video->video_id === $video->id;
        // });
        
        // Queue::assertPushedOn('processing', ConvertVideo::class);
    }
}
