<?php

namespace Keycloak\AdminClient;

use Keycloak\AdminClient\Values\Value;
use Keycloak\AdminClient\ResourceServer\Policy;
use Keycloak\AdminClient\ResourceServer\Resource;

class ResourceServer extends ClientEntity
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param AdminClient $adminClient
     * @param Client $client
     * @return static
     */
    public static function factory(AdminClient $adminClient, Client $client)
    {
        $instance = new static($adminClient);
        $instance->setClient($client);

        return $instance;
    }

    /**
     * @param Client $client
     * @return $this
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return string
     */
    public function getResourceServerBaseUrl()
    {
        $clientId = $this->client->getClientId();

        $allClients = $this->client->find()->filter(function ($item) use ($clientId) {
            return $item['clientId'] == $clientId;
        });

        $id = null;

        if ($allClients->isNotEmpty()) {
            $id = $allClients->first()['id']; // client UUID
        }

        return $this->client->getClientUrl($id) . '/authz/resource-server';
    }

    /**
     * Resources of server
     *
     * @return Resource
     */
    public function resources()
    {
        return Resource::factory($this->getAdminClient(), $this);
    }

    /**
     * Policies of server
     *
     * @return Policy
     */
    public function policies()
    {
        return Policy::factory($this->getAdminClient(), $this);
    }

    /**
     * @param null $name
     */
    public function find($name = null)
    {
        // TODO: Implement find() method.
    }

    public function create(Value $value)
    {
        // TODO: Implement create() method.
    }

    public function update(Value $value)
    {
        // TODO: Implement update() method.
    }

    public function delete(Value $value)
    {
        // TODO: Implement delete() method.
    }
}
