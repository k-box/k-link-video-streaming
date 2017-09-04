<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\KlinkRegistry\Registry;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Registry::auth();

        Gate::define('add-video', function ($application, $video = null) {
            return isset($application->permissions) ? collect($application->permissions)->contains('data-add') : false;
        });

        Gate::define('delete-video', function ($application, $video) {
            $hasPermission = isset($application->permissions) ? collect($application->permissions)->contains('data-delete-own') : false;
            return $hasPermission &&
                   $application->id == $video->application_id;
        });
    }
}
