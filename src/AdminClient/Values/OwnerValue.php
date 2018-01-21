<?php

namespace Keycloak\AdminClient\Values;

use Keycloak\Exception\NotSupportedException;

class OwnerValue extends Value
{
    /**
     * @var
     */
    protected $id;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var ScopesValue
     */
    protected $scopes;

    /**
     * OwnerValue constructor.
     * @param $id
     * @param $name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray()
    {
        return []; //TODO not implemented yet
    }

    /**
     * Get json of value
     *
     * @throws NotSupportedException
     */
    public function jsonSerialize()
    {
        throw new NotSupportedException();
    }

    /**
     * Get json of value
     *
     * @param int $options
     * @return string|void
     * @throws NotSupportedException
     */
    public function toJson($options = 0)
    {
        throw new NotSupportedException();
    }

    /**
     * Get value id
     * @throws NotSupportedException
     */
    public function getId()
    {
        throw new NotSupportedException();
    }
}
