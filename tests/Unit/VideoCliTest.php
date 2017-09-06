<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\VideoProcessing\Drivers\VideoCli;
use App\VideoProcessing\Drivers\VideoCliOptions;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * @group cli-required
 */
class VideoCliTest extends TestCase
{
    public function test_video_cli_runs_command()
    {
        $options = new VideoCliOptions('details', 'C:/Users/Alessio/Documents/GitHub/video-streaming-service/storage/example/20170813.mp4');
        $cli = new VideoCli($options);

        try
        {
            // Don't care if fails or no, I just want to make sure the command line arguments are properly passed
            $cli->run();
        }
        catch(\Exception $ex){}


        $this->assertEquals(
            realpath(base_path('/bin/video-processing-cli-win.exe')) . ' details "C:/Users/Alessio/Documents/GitHub/video-streaming-service/storage/example/20170813.mp4"',
            $cli->process()->getCommandLine()
        );

    }

    public function test_video_cli_runs_command_with_optional_parameters()
    {

        $file = 'C:/Users/Alessio/Documents/GitHub/video-streaming-service/storage/example/20170813.mp4';

        $options = new VideoCliOptions('thumbnail', $file, [dirname($file)],['--format jpg']);
        $cli = new VideoCli($options);

        try
        {
            // Don't care if fails or no, I just want to make sure the command line arguments are properly passed
            $cli->run();
        }
        catch(\Exception $ex){}


        $this->assertEquals(
            realpath(base_path('/bin/video-processing-cli-win.exe')) . ' thumbnail "--format jpg" "'.$file.'" "'.dirname($file).'"',
            $cli->process()->getCommandLine()
        );

    }
}
