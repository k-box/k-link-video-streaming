<?php

namespace Tests\Feature\Fixtures;

use SplFileInfo;
use RuntimeException;
use App\VideoProcessing\Exceptions\VideoProcessingFailedException;
use App\VideoProcessing\Contracts\VideoProcessor as VideoProcessorContract;
use Illuminate\Support\Facades\Storage;

class FakeVideoProcessor implements VideoProcessorContract
{
    

    /**
     * Create a new job instance.
     *
     * @param \App\Video $video The video to convert
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Get the details/metadata of a video file
     *
     * @param string $file The path of the video file
     * @param array $options Additional parameters
     * @return mixed
     */
     public function details($file, $options = []) {

     }
     
    /**
    * Generate the thumbnail of video file
    *
    * @param string $file The path of the video file
    * @param array $options Additional parameters
    * @return mixed
    */
    public function thumbnail($file, $options = []) {
        $video_id = basename(dirname($file));
        
        Storage::disk('videos')->put('./' . $video_id . '/' . $video_id . '.jpg', 'Example JPG file');
    }

    /**
    * Prepare the video file for streaming purposes
    *
    * @param string $file The path of the video file
    * @param array $options Additional parameters
    * @return mixed
    */
    public function streamify($file, $options = []) {
        $video_id = basename(dirname($file));

        Storage::disk('videos')->put('./' . $video_id . '/' . $video_id . '.mdp', 'Example MDP file');
    }

    
}
