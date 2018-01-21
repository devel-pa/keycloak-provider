<?php

namespace Keycloak\Auth;

use Keycloak\Model\User;
use Keycloak\Exception\NotSupportedException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;

class UserProvider implements UserProviderContract
{
    /**
     * @var User
     */
    protected $user;

    /**
     * UserProvider constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->user;
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return $this->user;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     * @throws \Exception
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        throw new NotSupportedException();
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return void
     * @throws NotSupportedException
     */
    public function retrieveByCredentials(array $credentials)
    {
        throw new NotSupportedException();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return void
     * @throws NotSupportedException
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        throw new NotSupportedException();
    }
}