<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use Avvertix\TusUpload\Events\TusUploadCompleted;
use Avvertix\TusUpload\Events\TusUploadCancelled;

use App\Listeners\TusUploadCompletedHandler;
use App\Listeners\TusUploadCancelledHandler;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        TusUploadCancelled::class => [
            TusUploadCancelledHandler::class,
        ],
        TusUploadCompleted::class => [
            TusUploadCompletedHandler::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
