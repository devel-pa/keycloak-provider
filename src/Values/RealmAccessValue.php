<?php

namespace Keycloak\Values;

class RealmAccessValue
{
    /**
     * @var array
     */
    protected $roles;

    /**
     * RealmAccessValue constructor.
     * @param $roles
     */
    public function __construct($roles)
    {
        $this->roles = $roles;
    }

    /**
     * Get roles
     *
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
