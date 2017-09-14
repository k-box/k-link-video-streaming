<?php

namespace App\KlinkRegistry;

use App\KlinkRegistry\Contracts\Client as ClientContract;
use App\Application;
use OneOffTech\KLinkRegistryClient\Client as KRegistryClient;
use Exception;
use Log;

class LocalClient implements ClientContract
{

    private $client = null;

    /**
     * Create a new Local K-Link Registry client
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
