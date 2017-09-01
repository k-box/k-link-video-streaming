<?php

namespace App\KlinkRegistry\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use App\Application;
use Illuminate\Contracts\Auth\UserProvider;

class KlinkRegistryUserProvider implements UserProvider
{
    /**
     * @var \App\KlinkRegistry\Contracts\Client
     */
    private $client = null;

    /**
     * Create a new K-Link Registry user/application provider.
     *
     * @param  \App\KlinkRegistry\Contracts\Client  $client
     * @return \App\KlinkRegistry\Auth\KlinkRegistryUserProvider
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        // not storing anything locally, so not able to retrieve by identifier
        return null;
    }
     
    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed   $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        // not storing anything locally, so not able to retrieve by identifier
        return null;
    }
     
    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        // we don't manage remember tokens
    }
     
    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        return $this->client->retrieveApplication(
            $credentials['api_token'],
            $credentials['api_calling_url'],
            ['data-add', 'data-delete-own']
        );
    }
     
    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return false;
    }
}
