<?php

namespace App\VideoProcessing;

use SplFileInfo;
use RuntimeException;
use App\VideoProcessing\Drivers\VideoCli;
use App\VideoProcessing\Drivers\VideoCliOptions;
use App\VideoProcessing\Exceptions\VideoProcessingFailedException;
use App\VideoProcessing\Contracts\VideoProcessor as VideoProcessorContract;
use Symfony\Component\Process\Exception\ProcessFailedException;

class LocalVideoProcessor implements VideoProcessorContract
{
    /**
     * Get the details/metadata of a video file
     *
     * @param string $file The path of the video file
     * @param array $options Additional parameters
     * @return mixed
     */
     public function details($file, $options = [])
     {
        $options = new VideoCliOptions('details', $file);
        
        $cli = tap(new VideoCli($options))->run();

        $out = $cli->output();
        
        return $out;
     }
     
    /**
    * Generate the thumbnail of video file
    *
    * @param string $file The path of the video file
    * @param array $options Additional parameters
    * @return mixed
    */
    public function thumbnail($file, $options = [])
    {
        $options = new VideoCliOptions('thumbnail', $file, [dirname($file)]);
        
        $cli = tap(new VideoCli($options))->run();

        $out = $cli->output();
        
        return $out;
    }

    /**
    * Prepare the video file for streaming purposes
    *
    * @param string $file The path of the video file
    * @param array $options Additional parameters
    * @return mixed
    */
    public function streamify($file, $options = []) {
        $options = new VideoCliOptions('process', $file, [dirname($file)]);
        
        $cli = tap(new VideoCli($options))->run();

        $out = $cli->output();
        
        return $out;
    }

    
}
