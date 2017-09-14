<?php

namespace App\KlinkRegistry;

use App\KlinkRegistry\Contracts\Client as ClientContract;
use App\Application;
use OneOffTech\KLinkRegistryClient\Client as KRegistryClient;
use Exception;
use Log;

class Client implements ClientContract
{

    private $client = null;

    /**
     * Create a new K-Link Registry client bound to a specific Registry instance.
     *
     * @param string $url The URL of the K-Link Registry instance
     * @return \App\KlinkRegistry\Contracts\Client
     */
    public function __construct($url)
    {
        $this->client = (new KRegistryClient($url))->access();
    }


    public function retrieveApplication($token, $applicationUrl, $permissions)
    {   
        try
        {            
            $application = $this->client->getApplication($token, $applicationUrl, $permissions);
            
            return new Application([
                'application_id' => $application->getApplicationId(),
                'url' => $applicationUrl,
                'permissions' => $permissions
            ]);

        }
        catch(Exception $ex)
        {
            Log::error('K-Registry application retrieval error', ['error' => $ex, 'params' => compact('token', 'applicationUrl', 'permissions')]);

            return null;
        }
    }
}
