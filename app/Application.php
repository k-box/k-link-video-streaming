<?php

namespace App;

use Illuminate\Auth\GenericUser as AuthenticatableGenericImplementation;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

/**
 * A generic application, as defined in the K-Registry
 */
class Application extends AuthenticatableGenericImplementation
                  implements AuthorizableContract
{

    use Authorizable;

    /**
     * Create a new Application object.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get the name of the unique identifier for the application.
     *
     * @return string
     */
     public function getAuthIdentifierName()
     {
         return 'application_id';
     }
}
