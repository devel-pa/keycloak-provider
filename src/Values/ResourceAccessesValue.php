<?php

namespace Keycloak\Values;

use ArrayIterator;
use IteratorAggregate;
use Illuminate\Contracts\Support\Arrayable;
use Keycloak\Exception\WrongResourceException;

class ResourceAccessesValue implements Arrayable, IteratorAggregate
{
    /**
     * @var array
     */
    protected $items;

    /**
     * ResourceAccessesValue constructor.
     * @param array $items
     */
    public function __construct(array $items)
    {
        foreach ($items as $item) {
            if ($item instanceof ResourceAccessValue) {
                $this->items[] = $item;
            }
        }
    }

    /**
     * Factory to create a value from response
     *
     * @param array $response
     * @return static
     * @throws WrongResourceException
     */
    public static function fromResponse(array $response)
    {
        if (!isset($response['resource_access'])) {
            throw new WrongResourceException();
        }

        $items = [];

        foreach ($response['resource_access'] as $clientId => $values) {
            $items[] = new ResourceAccessValue($clientId, $values['roles']);
        }

        return new static($items);
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
     * Iterator to support foreach
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
}
