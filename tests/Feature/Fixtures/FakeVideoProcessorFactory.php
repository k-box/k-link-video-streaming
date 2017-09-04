<?php

namespace Tests\Feature\Fixtures;

/**
 * 
 */
class FakeVideoProcessorFactory
{
    /**
     * Create a new video processor instance.
     *
     * @return \App\VideoProcessing\Contracts\VideoProcessor
     */
     public function make()
     {
         return new FakeVideoProcessor();
     }
}
