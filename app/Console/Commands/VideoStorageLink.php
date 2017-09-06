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
        if (file_exists(public_path('storage'))) {
            return $this->error('The "public/storage" directory already exists.');
        }

        $this->laravel->make('files')->link(
            storage_path('app/videos'), public_path('storage')
        );

        $this->info('The [public/storage] directory has been linked.');
    }
}
