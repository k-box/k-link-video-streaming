<?php

namespace App\Jobs;

use Log;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Video;
use App\VideoProcessing\VideoProcessorFactory;
use App\VideoProcessing\Process;
use App\Exceptions\VideoNotFoundException;

class ConvertVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The video that needs to be converted
     *
     * @var \App\Video
     */
    public $video = null;

    /**
     * Create a new job instance.
     *
     * @param \App\Video $video The video to convert
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // grab the video file

        try{

            $originalVideoFile = $this->video->file;

            if(!$originalVideoFile->isFile()){
                throw new VideoNotFoundException('Video file not existing');
            }

            // pass it to the video-processing-cli

            $videoProcessor = app()->make(VideoProcessorFactory::class)->make();

            $videoProcessor->streamify($originalVideoFile->getRealPath());
            
            $videoProcessor->thumbnail($originalVideoFile->getRealPath());

            // monitor its status

            $this->video->completed = true;
            
            $this->video->save();

        }catch(Exception $ex){

            Log::error('Video conversion error', ['video' => $this->video, 'error' => $ex]);

            $this->video->fail_reason = $ex->getMessage();
            $this->video->save();
        }

    }
}
