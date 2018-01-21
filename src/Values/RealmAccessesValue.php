<?php

namespace Keycloak\Values;

use ArrayIterator;
use IteratorAggregate;
use Keycloak\Exception\WrongResourceException;

class RealmAccessesValue implements IteratorAggregate
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
            if ($item instanceof RealmAccessValue) {
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
        if (!isset($response['realm_access'])) {
            throw new WrongResourceException();
        }

        $items[] = new RealmAccessValue($response['realm_access']['roles']);

        return new static($items);
    }

    /**
     * Iterator to support foreach
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
}
