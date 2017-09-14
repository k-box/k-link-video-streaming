<?php

namespace App\KlinkRegistry;

use Illuminate\Support\Facades\Auth;
use App\KlinkRegistry\Auth\KlinkRegistryUserProvider;
use App\KlinkRegistry\Auth\KlinkRegistryGuard;
use App\KlinkRegistry\Client;
use App\KlinkRegistry\LocalClient;

class Registry
{


    /**
     * Register the "kregistry" authentication user provider and guard driver
     *
     * @return void
     */
    public static function auth()
    {
        /*
         * Registering the K-Registry guard and user provider.
         *
         * In this way we could authenticate requests against applications on the K-Registry
         */
        Auth::provider('kregistry', function ($app, array $config) {
            return new KlinkRegistryUserProvider(
                static::getRegistryClient($config['url']));
        });

        Auth::extend('kregistry', function ($app, $name, array $config) {
            return new KlinkRegistryGuard(
                Auth::createUserProvider($config['provider']), 
                $app['request']);
        });
    }


    private static function getRegistryClient($url)
    {
        if(app()->environment('local') && empty($url)){
            return new LocalClient($url);
        }

        return new Client($url);
    }

}