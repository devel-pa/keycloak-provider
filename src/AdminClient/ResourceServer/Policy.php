<?php

namespace Keycloak\AdminClient\ResourceServer;

use Illuminate\Support\Collection;
use Keycloak\AdminClient\AdminClient;
use Keycloak\AdminClient\ClientEntity;
use Keycloak\AdminClient\Values\Value;
use Keycloak\AdminClient\ResourceServer;
use Keycloak\AdminClient\Values\PolicyValue;
use Keycloak\Exception\NotSupportedException;
use Symfony\Component\HttpFoundation\Request;

class Policy extends ClientEntity
{
    /**
     * @var ResourceServer
     */
    protected $resourceServer;

    /**
     * Factory to create policy instance
     *
     * @param AdminClient $adminClient
     * @param ResourceServer $resourceServer
     * @return self
     */
    public static function factory(AdminClient $adminClient, ResourceServer $resourceServer)
    {
        $instance = new static($adminClient);
        $instance->setResourceServer($resourceServer);

        return $instance;
    }

    /**
     * Set resource server
     *
     * @param ResourceServer $resourceServer
     * @return self
     */
    public function setResourceServer(ResourceServer $resourceServer)
    {
        $this->resourceServer = $resourceServer;

        return $this;
    }

    /**
     * Policy type: user
     *
     * @return PolicyUser
     */
    public function user()
    {
        return PolicyUser::factory($this->getAdminClient(), $this);
    }

    /**
     * Find all or one
     *
     * @param null|int $id
     * @return Collection|PolicyValue
     */
    public function find($id = null)
    {
        if (null === $id) {
            $url = $this->getPoliciesUrl() . '?permission=false'; //filter to query only policies
        } else {
            $url = $this->getPolicyUrl($id);
        }

        $request = $this->getAuthenticatedRequest(Request::METHOD_GET, $url);

        $response = $this->adminClient->getParsedResponse($request);

        if (null === $id) {
            return Collection::make($response)->map(function ($item) {
                return PolicyValue::fromRaw($item);
            });
        }

        return PolicyValue::fromRaw($response);
    }

    /**
     * Get resources url
     *
     * @return string
     */
    public function getPoliciesUrl()
    {
        return $this->resourceServer->getResourceServerBaseUrl() . '/policy';
    }

    /**
     * Get policy url
     *
     * @param string $uuid
     * @return string
     */
    public function getPolicyUrl($uuid)
    {
        return $this->resourceServer->getResourceServerBaseUrl() . '/policy/' . $uuid;
    }

    /**
     * Create realm
     * @param Value $value
     * @throws NotSupportedException
     */
    public function create(Value $value)
    {
        throw new NotSupportedException();
    }

    /**
     * Update realm
     * @param Value $value
     * @throws NotSupportedException
     */
    public function update(Value $value)
    {
        throw new NotSupportedException();
    }

    /**
     * Delete realm
     * @param $value
     * @throws NotSupportedException
     */
    public function delete(Value $value)
    {
        throw new NotSupportedException();
    }
}
