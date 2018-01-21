<?php

namespace Keycloak\AdminClient;

abstract class ClientEntity implements Entity
{
    /**
     * @var AdminClient
     */
    protected $adminClient;

    /**
     * ClientEntity constructor.
     * @param AdminClient $adminClient
     */
    public function __construct(AdminClient $adminClient)
    {
        $this->adminClient = $adminClient;
    }

    /**
     * Get base url admin cli
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->adminClient->getBaseUrl();
    }

    /**
     * get instance of keycloak
     *
     * @return AdminClient
     */
    public function getAdminClient()
    {
        return $this->adminClient;
    }

    /**
     * Send request to keycloak server
     *
     * @param $method
     * @param $url
     * @param array $options
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getAuthenticatedRequest($method, $url, array $options = [])
    {
        return $this->adminClient->getAuthenticatedRequest($method, $url, $options);
    }
}
