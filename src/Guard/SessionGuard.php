<?php

namespace Keycloak\Guard;

use Keycloak\Exception\NotSupportedException;
use Illuminate\Auth\SessionGuard as BaseSessionGuard;

class SessionGuard extends BaseSessionGuard
{
    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        return $this->user = $this->provider->retrieveById(null);
    }

    /**
     * Log the given user ID into the application without sessions or cookies.
     *
     * @param  mixed $id
     * @return void
     * @throws NotSupportedException
     */
    public function onceUsingId($id)
    {
        throw new NotSupportedException();
    }

    /**
     * Log the given user ID into the application.
     *
     * @param  mixed $id
     * @param  bool $remember
     * @return void
     * @throws NotSupportedException
     */
    public function loginUsingId($id, $remember = false)
    {
        throw new NotSupportedException();
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     * @throws NotSupportedException
     */
    public function logout()
    {
        throw new NotSupportedException();
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return void
     * @throws NotSupportedException
     */
    public function id()
    {
        throw new NotSupportedException();
    }
}
