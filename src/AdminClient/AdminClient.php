<?php

namespace Keycloak\AdminClient;

use Keycloak\KeycloakProvider;

class AdminClient
{
    /**
     * Keycloak provider
     *
     * @var KeycloakProvider
     */
    protected $provider;
    /**
     * Admin user name
     * @var string
     */
    protected $adminUserName;
    /**
     * Admin password
     * @var string
     */
    protected $adminPassword;

    /**
     * AdminClient constructor.
     * @param KeycloakProvider $provider
     * @param $adminUserName
     * @param $adminPassword
     */
    public function __construct(KeycloakProvider $provider, $adminUserName, $adminPassword)
    {
        $this->provider = $provider;
        $this->adminUserName = $adminUserName;
        $this->adminPassword = $adminPassword;
    }

    /**
     * Get realms handler
     */
    public function realms()
    {
        return Realm::factory($this);
    }

    /**
     * get resource server
     *
     * @param $realm
     * @param $clientId
     * @return ResourceServer
     */
    public function resourceServer($realm, $clientId)
    {
        return ResourceServer::factory($this, $this->clients($realm, $clientId));
    }

    /**
     * Get clients handler
     * @param string $realm
     * @param null $clientId
     * @return Client
     */
    public function clients($realm, $clientId = null)
    {
        return Client::factory($this, Realm::factory($this, $realm), $clientId);
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
        return $this->provider->getAuthenticatedRequest($method, $url, $this->getToken(), $options);
    }

    /**
     * Get access token
     *
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    protected function getToken()
    {
        /** @var  $token */
        $token = $this->provider->getAccessToken('password', [
            'username' => $this->adminUserName,
            'password' => $this->adminPassword
        ]);

        return $token;
    }

    /**
     * Get parsed response from server
     *
     * @param $request
     * @return mixed
     */
    public function getParsedResponse($request)
    {
        return $this->provider->getParsedResponse($request);
    }

    /**
     * @param $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse($request)
    {
        return $this->provider->getResponse($request);
    }

    /**
     * Create url for realms
     *
     * @return string
     */
    protected function getRealmsUrl()
    {
        return $this->getBaseUrl() . '/realms';
    }

    /**
     * Base admin url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->provider->getAuthServerUrl() . '/admin';
    }
}
