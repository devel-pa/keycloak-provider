<?php

namespace Keycloak\Model;

use Illuminate\Auth\Authenticatable;
use Keycloak\KeycloakServiceContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * @var KeycloakServiceContract
     */
    protected $keycloak;

    /**
     * @var array
     */
    protected $owner;

    /**
     * User constructor.
     * @param KeycloakServiceContract $keycloak
     */
    public function __construct(KeycloakServiceContract $keycloak)
    {
        /** @var KeycloakServiceContract $keycloak */
        $this->keycloak = $keycloak;

        $this->owner = $this->keycloak->requestResourceOwner()->getResourceOwner();
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if (isset($this->owner[$name])) {
            return $this->owner[$name];
        }

        throw new \Exception('Unknown parameter [' . $name . ']');
    }

    /**
     * Check if has permission
     *
     * @param $resourceName
     * @param $scopeName
     * @return mixed
     * @throws \Exception
     */
    public function hasPermission($resourceName, $scopeName)
    {
        return $this->keycloak->hasPermission($resourceName, $scopeName);
    }

    /**
     * Check has resource permission
     *
     * @param $resourceName
     * @return mixed
     * @throws \Exception
     */
    public function hasResourcePermission($resourceName)
    {
        return $this->keycloak->hasResourcePermission($resourceName);
    }

    /**
     * Check has scope permission
     *
     * @param $scopeName
     * @return mixed
     * @throws \Exception
     */
    public function hasScopePermission($scopeName)
    {
        return $this->keycloak->hasScopePermission($scopeName);
    }

    /**
     * Get all permissions
     *
     * @return mixed
     */
    public function getPermissions()
    {
        return $this->keycloak->getPermissions();
    }

    /**
     * Check if has a specified role
     *
     * @param $role
     * @return mixed
     * @throws \Keycloak\Exception\WrongResourceException
     */
    public function hasRole($role)
    {
        return $this->keycloak->hasRole($role);
    }
}
