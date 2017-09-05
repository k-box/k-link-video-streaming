<?php

namespace App\Listeners;

use Avvertix\TusUpload\Events\TusUploadCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use App\VideoRepository;
use App\Jobs\ConvertVideo;
use Exception;
use Log;

class TusUploadCompletedHandler
{
    private $videos = null;

    /**
     * The local storage where videos are saved
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem 
     */
     private $storage = null;
    
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Storage $storage, VideoRepository $repository)
    {
        $this->videos = $repository;
        $this->storage = $storage->disk('videos');
    }

    /**
     * Handle the event.
     *
     * @param  TusUploadCompleted  $event
     * @return void
     */
    public function handle(TusUploadCompleted $event)
    {
        try
        {
            $video = $this->videos->findByUpload($event->upload->id);
            
            if(is_null($video)){
                return ;
            }

            // Move the uploaded video to the final location

            $source = $event->upload->path();

            $filename = $video->video_id.'.mp4';

            $destination = $this->storage->path($video->path .'/' . $filename);

            Log::info("Moving from {$source} to {$destination}");
            rename($source, $destination);

            // mark as queued and add the video to the processing queue

            $video->queued = true;
            
            $video->save();

            $job = (new ConvertVideo($video))->onQueue('video-processing');
            
            dispatch($job);
    
        }
        catch(Exception $ex)
        {
            Log::error('Upload Completed Handler Error', compact('event', 'ex'));
        }
    }
}
