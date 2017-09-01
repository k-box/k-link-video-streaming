<?php 

namespace Tests;

trait ActingAsApplication
{
    /**
     * Set the currently logged in user for the application.
     *
     * @param  string  $id The application identifier
     * @param  array  $permissions The application supported permissions, default ['data-add', 'data-delete-own']
     * @return $this
     */
    public function actingAsApplication($id, $permissions = ['data-add', 'data-delete-own'])
    {
        $this->actingAs((new \App\Application([
            'id' => $id,
            'permissions' => collect($permissions)
        ])));

        return $this;
    }


}
