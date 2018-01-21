<?php

namespace Keycloak\Values;

use Illuminate\Contracts\Support\Arrayable;

class ResourceAccessValue implements Arrayable
{
    /**
     * @var string
     */
    protected $resource;
    /**
     * @var array
     */
    protected $roles;

    /**
     * ResourceAccessValue constructor.
     * @param $resource
     * @param $roles
     */
    public function __construct($resource, $roles)
    {
        $this->resource = $resource;
        $this->roles = $roles;
    }

    /**
     * get resource
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
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

    /**
     * Convert to array
     * @return array
     */
    public function toArray()
    {
        return [
            $this->resource => [
                'roles' => $this->roles
            ]
        ];
    }
}
