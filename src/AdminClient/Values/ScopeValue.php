<?php

namespace Keycloak\AdminClient\Values;

class ScopeValue extends Value
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $name;

    /**
     * ScopeValue constructor.
     * @param $id
     * @param $name
     */
    public function __construct($id, $name)
    {
        $this->name = $name;
        $this->id = $id;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray()
    {
        // TODO: Implement toArray() method.
        return [];
    }

    /**
     * Convert to json
     *
     * @return string
     */
    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
        return "";
    }

    /**
     * Convert to json
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        // TODO: Implement toJson() method.
        return "";
    }

    /**
     * Get value id
     * @return string|int
     */
    public function getId()
    {
        // TODO: Implement getId() method.
        return 0;
    }
}