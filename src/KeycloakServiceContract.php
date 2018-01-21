<?php

namespace Keycloak;

use Keycloak\Response\ProviderResponse;
use League\OAuth2\Client\Token\AccessToken;

interface KeycloakServiceContract
{

    /**
     * Renew token from Keycloak server
     *
     * @return ProviderResponse
     */
    public function requestNewToken();

    /**
     * Returns external link to auth server
     *
     * @return string
     */
    public function getAuthorizationUrl();

    /**
     * Get current token
     *
     * @return AccessToken
     */
    public function getToken();

    /**
     * Save token received from auth server
     *
     * @return bool
     */
    public function saveToken();

    /**
     * Check if current token is expired
     *
     * @return bool
     */
    public function isTokenExpired();

    /**
     * Has current token
     *
     * @return bool
     */
    public function hasToken();

    /**
     * Refresh token
     *
     * @return bool
     */
    public function refreshToken();

    /**
     * Request new resource
     *
     * @return ProviderResponse
     */
    public function requestResourceOwner();

    /**
     * Get token value string
     *
     * @return null|string
     */
    public function getTokenValue();

    /**
     * Check if has permission on specified resource ans scope
     *
     * @param $resourceName
     * @param $scopeName
     * @return bool
     * @throws \Exception
     */
    public function hasPermission($resourceName, $scopeName);

    /**
     * Get entitlement permissions
     * @return mixed
     */
    public function getPermissions();

    /**
     * Check if has a permission on specified resource
     *
     * @param $resourceName
     * @return bool
     * @throws \Exception
     */
    public function hasResourcePermission($resourceName);

    /**
     * Check if has a permission on specified scope
     *
     * @param $scopeName
     * @return bool
     * @throws \Exception
     */
    public function hasScopePermission($scopeName);

    /**
     * Common function to check role
     *
     * @param string|array $role
     * @return bool
     * @throws Exception\WrongResourceException
     */
    public function hasRole($role);

    /**
     * Check realm role
     *
     * @param string|array $role
     * @return bool
     * @throws Exception\WrongResourceException
     */
    public function hasRealmRole($role);

    /**
     * Check resource role
     * if $resource is null when it checks clientId access
     *
     * @param string|array $role
     * @param null $resource
     * @return bool
     * @throws Exception\WrongResourceException
     */
    public function hasResourceRole($role, $resource = null);
}
