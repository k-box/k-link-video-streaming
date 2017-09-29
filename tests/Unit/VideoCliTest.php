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

    const VIDEO_PROCESSING_CLI_EXECUTABLE_NAME = 'video-processing-cli';
    const VIDEO_PROCESSING_CLI_FOLDER = 'bin';

    private function getVideoCliExecutable()
    {
        $suffixes = [
            '',
            '.exe',
            '-win.exe',
            '-linux',
            '-macos',
        ];

        $dir = realpath(base_path(self::VIDEO_PROCESSING_CLI_FOLDER));

        foreach ($suffixes as $suffix) {
            if (@is_file($file = $dir.DIRECTORY_SEPARATOR.self::VIDEO_PROCESSING_CLI_EXECUTABLE_NAME.$suffix) && ('\\' === DIRECTORY_SEPARATOR || is_executable($file))) {
                return $file;
            }
            
        }

        throw new RuntimeException("No Video CLI executable found in [{$dir}].");
    }

    public function test_video_cli_runs_command()
    {
        $file = base_path('tests/data/video.mp4');

        $options = new VideoCliOptions('details', $file);
        $cli = new VideoCli($options);

        try
        {
            // Don't care if fails or no, I just want to make sure the command line arguments are properly passed
            $cli->run();
        }
        catch(\Exception $ex){ }

        $this->assertEquals(
            count(explode(' ', $this->getVideoCliExecutable() . ' details "'.$file.'"')),
            count(explode(' ', $cli->process()->getCommandLine()))
        );

    }

    public function test_video_cli_runs_command_with_optional_parameters()
    {
        $file = base_path('tests/data/video.mp4');

        $options = new VideoCliOptions('thumbnail', $file, [dirname($file)],['--format jpg']);
        $cli = new VideoCli($options);

        try
        {
            // Don't care if fails or no, I just want to make sure the command line arguments are properly passed
            $cli->run();
        }
        catch(\Exception $ex){}
        
        // seems that on linux the escape is different than on Windows, so we cannot check exact equality
        $this->assertEquals(
            count(explode(' ', $this->getVideoCliExecutable() . ' thumbnail "--format jpg" "'.$file.'" "'.dirname($file).'"')),
            count(explode(' ', $cli->process()->getCommandLine()))
        );

    }
}
