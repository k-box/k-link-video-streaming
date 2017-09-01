<?php

namespace App\KlinkRegistry\Auth;

use Illuminate\Auth\TokenGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class KlinkRegistryGuard extends TokenGuard
{
    /**
     * The name of the request header used to obtain the caller source.
     *
     * @var string
     */
    protected $sourceHeader;
     
    /**
     * The name of the caller source field to be used in credential validation.
     *
     * @var string
     */
    protected $sourceKey;

    /**
     * Create a new authentication guard.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider  $provider
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(UserProvider $provider, Request $request)
    {
        parent::__construct($provider, $request);
        $this->sourceHeader = 'origin';
        $this->sourceKey = 'api_calling_url';
    }

    /**
     * Get the application source url for the current request.
     *
     * @return string
     */
    public function getSourceForRequest()
    {
       return $this->request->header($this->sourceHeader);
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
     public function user()
     {
         // If we've already retrieved the user for the current request we can just
         // return it back immediately. We do not want to fetch the user data on
         // every call to this method because that would be tremendously slow.
         if (! is_null($this->user)) {
             return $this->user;
         }
 
         $user = null;
 
         $token = $this->getTokenForRequest();
         $source = $this->getSourceForRequest();
 
         if (! empty($token)) {
             $user = $this->provider->retrieveByCredentials(
                 [$this->storageKey => $token,
                  $this->sourceKey => $source]
             );
         }
 
         return $this->user = $user;
     }
    
    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if (empty($credentials[$this->inputKey])) {
            return false;
        }

        $credentials = [
            $this->storageKey => $credentials[$this->inputKey],
            $this->sourceKey => $this->getSourceForRequest(),
        ];
        
        if ($this->provider->retrieveByCredentials($credentials)) {
            return true;
        }
        
        return false;
    }
}
