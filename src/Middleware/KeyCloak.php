<?php

namespace Keycloak\Middleware;

use Closure;
use Keycloak\KeycloakService;
use Keycloak\KeycloakServiceContract;

class Keycloak
{

    /**
     * @var KeycloakService
     */
    protected $keycloak;

    /**
     * Keycloak constructor.
     * @param KeycloakServiceContract $keycloak
     */
    public function __construct(KeycloakServiceContract $keycloak)
    {
        $this->keycloak = $keycloak;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        // if key is expired do refresh
        if ($this->keycloak->isTokenExpired()) {
            $this->keycloak->refreshToken();
        }
        // if we do not have a token in session,
        // make a request and save it
        if (!$this->keycloak->hasToken()) {
            $this->keycloak->saveToken();
        }

        // if we still do not have
        // a token we need request a new.
        if (!$this->keycloak->hasToken()) {

            $response = $this->keycloak->requestNewToken();

            if ($response->isNeedToRedirect()) {
                return redirect()->to($response->getRedirectTo()); // redirect to auth page
            }
        }

        return $next($request);
    }
}
