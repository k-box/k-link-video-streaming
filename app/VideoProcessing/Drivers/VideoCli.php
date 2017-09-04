<?php

namespace App\VideoProcessing\Drivers;

use SplFileInfo;
use RuntimeException;
use App\VideoProcessing\Exceptions\VideoProcessingFailedException;
use Symfony\Component\Process\Process as SymfonyProcess;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Exception\ProcessFailedException;

class VideoCli
{

    const VIDEO_PROCESSING_CLI_EXECUTABLE_NAME = 'video-processing-cli';
    const VIDEO_PROCESSING_CLI_FOLDER = 'bin';

    /**
     * @var \App\VideoProcessing\Drivers\VideoCliOptions
     */
    private $options = null;

    /**
     * @var Symfony\Component\Process\Process
     */
    private $process = null;

    /**
     * Create a Video CLI worker instance.
     *
     * @param \App\VideoProcessing\Drivers\VideoCliOptions $options
     * @return void
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * Get the underlying command line process
     *
     * @return Symfony\Component\Process\Process
     */
    public function process()
    {
        return $this->process;
    }

    public function run()
    {
        $driver = $this->getVideoCliExecutable();

        if (is_bool($driver) || realpath($driver) === false) {
            throw new RuntimeException("Invalid Video CLI path [{$driver}].");
        }

        $builder = (new ProcessBuilder())
                ->setPrefix(realpath($driver))
                ->setWorkingDirectory(realpath(base_path(self::VIDEO_PROCESSING_CLI_FOLDER)));

        $arguments = $this->options->toWorkerArguments();

        foreach ($arguments as $argument) {
            $builder->add($argument);
        }

        $this->process = $process = $builder->getProcess();
        
        // pass the inputs
        
        $process->setTimeout(null);
        $process->setIdleTimeout(null);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new VideoProcessingFailedException((new ProcessFailedException($process))->getMessage());
        }
    }


    /**
     * Return the command output
     *
     * @return string
     */
    public function output()
    {
        return $this->process ? $this->process->getOutput() : null;
    }

    /**
     * Return the command error output
     *
     * @return string
     */
    public function error()
    {
        return $this->process ? $this->process->getErrorOutput() : null;
    }


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

}
