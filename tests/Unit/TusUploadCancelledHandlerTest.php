<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Video;
use App\VideoRepository;
use OneOffTech\TusUpload\TusUpload;
use Illuminate\Support\Facades\Storage;
use OneOffTech\TusUpload\Events\TusUploadCancelled;
use App\Listeners\TusUploadCancelledHandler;
use Carbon\Carbon;

class TusUploadCancelledHandlerTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    private $video = null;

    public function setUp()
    {
        parent::setUp();

        config([
            'tusupload.storage' => storage_path('app/uploads')
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

        $upload->cancelled = true;
        $upload->save();

        $repository = app(VideoRepository::class);
        
        $this->video = $repository->create(1, $upload->id, basename($path).'.mp4', 'video/mp4');

        return new TusUploadCancelled($upload->fresh());
    }

    public function test_video_is_marked_as_cancelled()
    {
        Storage::fake('videos');

        $request_id = 'REQUEST';

        $cancelled_event = $this->createEvent(1, $request_id);

        $listener = app(TusUploadCancelledHandler::class);

        $listener->handle($cancelled_event);

        $this->video = $this->video->fresh();

        $this->assertNotNull($this->video);
        $this->assertNotNull($this->video->cancelled_at);
        
        $this->assertEquals(Video::STATUS_CANCELLED, $this->video->status);
    }
}
