<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VideoStorageLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videostorage:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a symbolic link from "public/videos" to "storage/app/videos"';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (file_exists(public_path('videos'))) {
            return $this->error('The "public/videos" directory already exists.');
        }

        $this->laravel->make('files')->link(
            storage_path('app/videos'), public_path('videos')
        );

        $this->info('The [public/videos] directory has been linked.');
    }
}
