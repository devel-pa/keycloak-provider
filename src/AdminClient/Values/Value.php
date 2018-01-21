<?php

namespace Keycloak\AdminClient\Values;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

abstract class Value implements Arrayable, Jsonable
{
    /**
     * Get id of value
     *
     * @return mixed
     */
    public abstract function getId();
}