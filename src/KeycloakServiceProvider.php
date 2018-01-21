<?php

namespace Keycloak;

use Auth;
use Keycloak\Auth\UserProvider;
use Keycloak\Factory\ProviderFactory;
use Keycloak\Guard\SessionGuard;
use Keycloak\Middleware\Keycloak as KeycloakMiddleware;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class KeycloakServiceProvider extends ServiceProvider
{

    protected $keyCloakMiddlewareGroup = [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
        KeycloakMiddleware::class
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(KeycloakServiceContract::class, function () {
            return ProviderFactory::create();
        });

        // register new middleware group for application
        $this->middlewareGroup('keycloak', $this->keyCloakMiddlewareGroup);

        // register auth provider
        Auth::provider('keycloak.auth', function ($app, array $config) {
            return $app->make(UserProvider::class);
        });

        // register guard
        Auth::extend('keycloak.guard', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);

            $sessionGuard = new SessionGuard($name, $provider, $this->app['session.store'], request());
        });
    }
}
