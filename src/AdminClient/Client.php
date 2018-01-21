<?php

namespace Keycloak\AdminClient;

use Illuminate\Support\Collection;
use Keycloak\AdminClient\Values\Value;
use Keycloak\Exception\NotSupportedException;
use Symfony\Component\HttpFoundation\Request;

class Client extends ClientEntity
{
    /**
     * @var string
     */
    protected $clientId;
    /**
     * @var Realm
     */
    protected $realm;

    /**
     * Factory to create a client instance
     *
     * @param AdminClient $adminClient
     * @param Realm $realm
     * @param null $clientId
     * @return static
     */
    public static function factory(AdminClient $adminClient, Realm $realm, $clientId = null)
    {
        $instance = new static($adminClient);
        $instance->setClientId($clientId);
        $instance->setRealm($realm);

        return $instance;
    }

    /**
     * Setter for realm instance
     *
     * @param Realm $realm
     */
    public function setRealm(Realm $realm)
    {
        $this->realm = $realm;
    }

    /**
     * Getter for client id
     *
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Setter for client id
     *
     * @param $clientId
     * @return $this
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Find all or one item
     *
     * @param null $id
     * @return Collection
     */
    public function find($id = null)
    {
        $url = $this->getClientUrl($id); // id

        if (null === $id) { // all
            $url = $this->getClientUrl();
        }

        $request = $this->getAuthenticatedRequest(Request::METHOD_GET, $url);

        return new Collection($this->adminClient->getParsedResponse($request));
    }

    /**
     * Get client url
     *
     * @param null $clientId
     * @return string
     */
    public function getClientUrl($clientId = null)
    {
        return $this->getClientsUrl() . '/' . (is_null($clientId) ? $this->clientId : $clientId);
    }

    /**
     * Get clients url
     *
     * @return string
     */
    public function getClientsUrl()
    {
        return $this->realm->getRealmsUrl() . '/' . $this->realm->getRealm() . '/clients';
    }

    /**
     * Create client
     * @param Value $value
     * @throws NotSupportedException
     */
    public function create(Value $value)
    {
        throw new NotSupportedException();
    }

    /**
     * Update client
     * @param Value $value
     * @throws NotSupportedException
     */
    public function update(Value $value)
    {
        throw new NotSupportedException();
    }

    /**
     * Delete item
     * @param $value
     * @throws NotSupportedException
     */
    public function delete(Value $value)
    {
        throw new NotSupportedException();
    }
}