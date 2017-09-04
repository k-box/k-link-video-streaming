<?php

namespace App\VideoProcessing;

use App\VideoProcessing\LocalVideoProcessor;
use App\VideoProcessing\Contracts\VideoProcessor;

/**
 * @see \App\VideoProcessing\LocalVideoProcessor
 */
class VideoProcessorFactory
{
    /**
     * Create a new video processor instance.
     *
     * @return \App\VideoProcessing\Contracts\VideoProcessor
     */
     public function make()
     {
         return new LocalVideoProcessor();
     }
}
