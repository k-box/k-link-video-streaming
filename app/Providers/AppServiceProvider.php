<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // dinamically configure the deployment option
        // this serve to identify if deployment is done in sub-folder instead of on domain root
        $sub_folder = trim(trim(parse_url(config('app.url'), PHP_URL_PATH), '/'));
        config([
            'deployment.sub_folder' => $sub_folder,
            'tusupload.public_url' => rtrim(config('app.url'), '/') . '/video.uploads/',
            'tusupload.base_path' => empty($sub_folder) ? "/video.uploads/" : "/$sub_folder/video.uploads/"
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
