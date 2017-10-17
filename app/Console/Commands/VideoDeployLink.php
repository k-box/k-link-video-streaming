<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VideoDeployLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videodeploy:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a symbolic link to the "public" based on the sub-folder deployment';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $sub_folder = config('deployment.sub_folder');

        if(empty($sub_folder)){
            $this->info('Not in a sub-folder deployment, skipping.');
            return 0;
        }

        if (file_exists(public_path($sub_folder))) {
            return $this->error("The [public/$sub_folder] directory already exists.");
        }

        $this->laravel->make('files')->link(
            public_path(''), public_path($sub_folder)
        );

        $this->info("The [public] directory has been linked to the [$sub_folder] sub-folder.");
    }
}
