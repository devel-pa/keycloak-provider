<?php

namespace Keycloak\Values;

use Illuminate\Contracts\Support\Arrayable;

class PermissionValue implements Arrayable
{
    /**
     * Resource UUID
     * @var string
     */
    protected $resourceId;
    /**
     * Resource name
     * @var
     */
    protected $resourceName;
    /**
     * Array of scopes
     * @var array[string]
     */
    protected $scopes;

    /**
     * KeycloakPermission constructor.
     * @param $resourceId
     * @param $resourceName
     * @param $scopes
     */
    public function __construct($resourceId, $resourceName, $scopes)
    {
        $this->resourceId = $resourceId;

        $this->resourceName = $resourceName;

        $this->scopes = $scopes;
    }

    /**
     * Factory to create permission from raw item
     *
     * @param $item
     * @return static
     * @throws \Exception
     */
    public static function fromRaw($item)
    {
        if (!isset($item['resource_set_id'])) {
            throw new \Exception("Field [resource_set_id] is mandatory.");
        }

        if (!isset($item['resource_set_name'])) {
            throw new \Exception("Field [resource_set_name] is mandatory.");
        }

        return new static($item['resource_set_id'], $item['resource_set_name'], $item['scopes'] ?? []);
    }

    /**
     * Get scopes
     *
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * Get resource name
     *
     * @return mixed
     */
    public function getResourceName()
    {
        return $this->resourceName;
    }

    /**
     * Get resource Id
     *
     * @return string
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'resource_set_id' => $this->resourceId,
            'resource_set_name' => $this->resourceName,
            'scopes' => $this->scopes
        ];
    }
}
