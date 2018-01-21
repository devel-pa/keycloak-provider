<?php

namespace Keycloak\AdminClient;

use Illuminate\Support\Collection;
use Keycloak\AdminClient\Values\Value;
use Keycloak\Exception\NotSupportedException;
use Symfony\Component\HttpFoundation\Request;

class Realm extends ClientEntity
{
    /**
     * Realm
     * @var string
     */
    protected $realm;

    /**
     * Factory to create realm instance
     *
     * @param AdminClient $adminClient
     * @param null $realm
     * @return static
     */
    public static function factory(AdminClient $adminClient, $realm = null)
    {
        $instance = new static($adminClient);
        $instance->setRealm($realm);

        return $instance;
    }

    /**
     * Get realm
     *
     * @return string
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * Set realm
     *
     * @param $realm
     * @return $this
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;

        return $this;
    }

    /**
     * Find all realm or one realm
     * @param null $realm
     * @return Collection
     */
    public function find($realm = null)
    {
        $url = $this->getRealmUrl($realm);

        if (null === $realm) { // all realms
            $url = $this->getRealmsUrl();
        }

        $request = $this->adminClient->getAuthenticatedRequest(Request::METHOD_GET, $url);

        return new Collection($this->adminClient->getParsedResponse($request));
    }

    /**
     * Get realm url
     *
     * @param null $realm
     * @return string
     */
    public function getRealmUrl($realm = null)
    {
        return $this->getRealmsUrl() . '/' . (is_null($realm) ? $this->realm : $realm);
    }

    /**
     * Get realms url
     *
     * @return string
     */
    public function getRealmsUrl()
    {
        return $this->adminClient->getBaseUrl() . '/realms';
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
