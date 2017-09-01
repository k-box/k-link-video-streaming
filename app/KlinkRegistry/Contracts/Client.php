<?php 

namespace App\KlinkRegistry\Contracts;

interface Client
{
    /**
     * Retrieve an application with the given token, domain and permission.
     *
     * It contextually checks if the Application have the required permission and exists
     *
     * @param string $token The token assigned to the application
     * @param string $applicationUrl The URL/domain of the application
     * @param array|string[] $permissions The permissions that the Application requires to access the service
     * @return Application|null Return the Application in case is valid and granted to access, null otherwise
     */
    public function retrieveApplication($token, $applicationUrl, $permissions);
}
