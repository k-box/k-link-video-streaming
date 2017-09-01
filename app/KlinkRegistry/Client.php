<?php

namespace App\KlinkRegistry;

use App\KlinkRegistry\Contracts\Client as ClientContract;
use App\Application;

class Client implements ClientContract
{


    /**
     * Create a new K-Link Registry client bound to a specific Registry instance.
     *
     * @param string $url The URL of the K-Link Registry instance
     * @return \App\KlinkRegistry\Contracts\Client
     */
    public function __construct($url)
    {

    }


    public function retrieveApplication($token, $applicationUrl, $permissions)
    {
        return new Application([
            'application_id' => 1,
            'url' => $applicationUrl,
            'permissions' => $permissions
        ]);
    }
}
