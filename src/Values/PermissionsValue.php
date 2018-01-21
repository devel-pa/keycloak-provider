<?php

namespace Keycloak\Values;

use ArrayIterator;
use IteratorAggregate;
use Illuminate\Contracts\Support\Arrayable;

class PermissionsValue implements Arrayable, IteratorAggregate
{
    /**
     * List of permissions
     *
     * @var array [KeycloakPermission]
     */
    protected $permissions;

    /**
     * KeycloakPermissions constructor.
     * @param $rpt
     */
    public function __construct(array $rpt)
    {
        if (isset($rpt['authorization']['permissions'])) {
            $this->permissions = array_map(function ($item) {
                return PermissionValue::fromRaw($item);
            }, $rpt['authorization']['permissions']);
        }
    }

    /**
     * Factory from response
     *
     * @param $response
     * @return static
     * @throws \Exception
     */
    public static function fromResponse($response)
    {
        if (!isset($response['rpt'])) {
            throw new \Exception("Response does't have [rpt] key.");
        }

        return new static($response['rpt']);
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($item) {
            /** @var PermissionValue $item */
            return $item->toArray();
        }, $this->permissions);
    }

    /**
     * Iterator to support foreach
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->permissions);
    }
}