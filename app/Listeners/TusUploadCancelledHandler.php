<?php

namespace App\Listeners;

use Avvertix\TusUpload\Events\TusUploadCancelled;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\VideoRepository;
use Exception;
use Log;

class TusUploadCancelledHandler
{
    private $videos = null;
    
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(VideoRepository $repository)
    {
        $this->videos = $repository;
    }

    /**
     * Handle the event.
     *
     * @param  TusUploadCancelled  $event
     * @return void
     */
    public function handle(TusUploadCancelled $event)
    {
        try
        {
            $video = $this->videos->findByUpload($event->upload->id);
            
            if(is_null($video)){
                return ;
            }

            $video->cancelled = true;
    
            $video->save();
        }
        catch(Exception $ex)
        {
            Log::error('Upload Cancelled Handler Error', compact('event', 'ex'));
        }
    }
}
