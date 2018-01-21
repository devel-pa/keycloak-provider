<?php

namespace Keycloak\AdminClient\Values;

class ScopesValue extends Value
{
    /**
     * Scopes array
     *
     * @var array
     */
    protected $items;

    /**
     * ScopesValue constructor.
     * @param array $items
     */
    public function __construct(array $items)
    {
        foreach ($items as $item) {
            if ($item instanceof ScopeValue) {
                $this->items[] = $item;
            }
        }
    }

    /**
     * Convert to array
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
        return "";
    }

    /**
     * Get id of value
     *
     * @return int|string
     */
    public function getId()
    {
        // TODO: Implement getId() method.
        return 0;
    }
}
