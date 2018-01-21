<?php

namespace Keycloak\Factory;

use Psr\Log\LoggerInterface;
use Keycloak\KeycloakProvider;
use Keycloak\KeycloakService;

class ProviderFactory
{
    /**
     * Factory to create keycloak service
     *
     * @return KeycloakService
     */
    public static function create()
    {
        $session = session()->driver();

        $vendorProvider = new KeycloakProvider([
            'authServerUrl' => config('keycloak.authServerUrl'),
            'realm'         => config('keycloak.realm'),
            'clientId'      => config('keycloak.clientId'),
            'clientSecret'  => config('keycloak.clientSecret'),
            'redirectUri'   => config('keycloak.redirectUri'),
            'proxy'         => config('keycloak.proxy'),
            'verify'        => config('keycloak.verify'),
            'curl'          => [
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2
            ]
        ]);

        $request = request();

        $logger = app(LoggerInterface::class);

        return new KeycloakService($vendorProvider, $request, $session, $logger);
    }
}