<?php

namespace Tests;

use Faker\Factory;
use Firebase\JWT\JWT;
use Mockery\Mock;

trait KeycloakFakeFactoryTrait
{

    protected $fakeClientId;

    protected $fakeAnotherClientId;

    protected $fakeRealm;

    protected $fakeRealmAccess;

    protected $fakeResourceAccess;

    protected function encodeJWT($payload)
    {
        return JWT::encode($payload, 'key', 'HS256');
    }

    protected function decodeJWT($jwt)
    {
        return JWT::decode($jwt, 'key', 'HS256');
    }

    protected function setupKeycloak()
    {
        $faker = Factory::create();
        $clients = $faker->randomElements(['api-server', 'ui', 'service-account', 'service-document'], 2);
        $this->fakeClientId = $clients[0];
        $this->fakeAnotherClientId = $clients[1];

        $this->fakeRealm = $faker->randomElement(['api-realm', 'ui-realm', 'service-realm']);

        $this->fakeRealmAccess = [
            "roles" => [
                0 => "admin",
                1 => "uma_authorization",
            ]
        ];

        $this->fakeResourceAccess = [
            $this->fakeClientId => [
                "roles" => [
                    0 => "uma_protection",
                ],
            ],
            $this->fakeAnotherClientId => [
                "roles" => [
                    0 => "admin"
                ],
            ],
            "account" => [ //system account client
                "roles" => [
                    0 => "manage-account",
                    1 => "manage-account-links",
                    2 => "view-profile",
                ]
            ]
        ];

    }

    protected function factoryTokenResponse()
    {
        $faker = Factory::create();
        $time = time();

        $expires_in = 300;
        $refresh_expires_in = 1800;

        $access_token = $this->encodeJWT([
            "jti" => "bdca5bf6-d9b5-4dc9-8e1b-4ed372e71f0d",
            "exp" => $time + $expires_in,
            "nbf" => 0,
            "iat" => $time,
            "iss" => $faker->url,
            "aud" => $this->fakeClientId,
            "sub" => "0c027b9f-b3d6-4103-8ac1-a0ee08b00bb7",
            "typ" => "Bearer",
            "azp" => $this->fakeClientId,
            "auth_time" => 0,
            "session_state" => "5f035552-fe89-4c25-8b0e-ac79ca18308f",
            "acr" => "1",
            "allowed-origins" => [
                0 => "http://localhost:8060",
                1 => "http://127.0.0.1:8060",
            ],
            "realm_access" => $this->fakeRealmAccess,
            "resource_access" => $this->fakeResourceAccess,
            "preferred_username" => "admin_user",
        ]);

        $refresh_token = $this->encodeJWT([
            "jti" => "d1a32bb2-2ddd-44cc-b53f-269643830f06",
            "exp" => $time + $refresh_expires_in,
            "nbf" => 0,
            "iat" => $time,
            "iss" => $faker->url,
            "aud" => $this->fakeClientId,
            "sub" => "0c027b9f-b3d6-4103-8ac1-a0ee08b00bb7",
            "typ" => "Refresh",
            "azp" => $this->fakeClientId,
            "auth_time" => 0,
            "session_state" => "5f035552-fe89-4c25-8b0e-ac79ca18308f",
            "realm_access" => $this->fakeRealmAccess,
            "resource_access" => $this->fakeResourceAccess,
        ]);

        return [
            "access_token" => $access_token,
            "expires_in" => $expires_in,
            "refresh_expires_in" => $refresh_expires_in,
            "refresh_token" => $refresh_token,
            "token_type" => "bearer",
            "not-before-policy" => 0,
            "session_state" => $faker->uuid
        ];
    }

    /**
     * Generate fake Request Party Token
     *
     * @see http://www.keycloak.org/docs/latest/authorization_services/index.html#_service_rpt_overview
     */
    protected function factoryPartyToken()
    {
        $time = time();

        $faker = Factory::create();

        return [
            'rpt' => $this->encodeJWT([
                "jti" => "ea3ffa3d-9d6e-464a-956f-4b98c964572a",
                "exp" => $time + 300,
                "nbf" => 0,
                "iat" => $time,
                "iss" => $faker->url,
                "aud" => "CAMPAIGN_CLIENT",
                "sub" => "0c027b9f-b3d6-4103-8ac1-a0ee08b00bb7",
                "typ" => "Bearer",
                "azp" => "CAMPAIGN_CLIENT",
                "auth_time" => 0,
                "session_state" => "62d3bbf3-7849-4827-ba26-987ed6ac5349",
                "preferred_username" => "admin_user",
                "acr" => "1",
                "allowed-origins" => [
                    0 => "http://localhost:8060",
                    1 => "http://127.0.0.1:8060",
                ],
                "realm_access" => $this->fakeRealmAccess,
                "resource_access" => $this->fakeResourceAccess,
                "authorization" => [
                    "permissions" => [
                        0 => [
                            "scopes" => [
                                0 => "scopes:create"
                            ],
                            "resource_set_id" => "d581b664-9d0e-421c-b5d6-dcb8e922befa",
                            "resource_set_name" => "res:people"
                        ],
                        1 => [
                            "scopes" => [
                                0 => "scopes:create",
                                1 => "scopes:view"
                            ],
                            "resource_set_id" => "30fab3b2-1a34-47d3-bcbd-ec09ebe0e6c8",
                            "resource_set_name" => "res:customer"
                        ],
                        2 => [
                            "scopes" => [
                                0 => "scopes:view"
                            ],
                            "resource_set_id" => "52ea0539-3549-4909-b5a5-319df046cdf3",
                            "resource_set_name" => "res:report",
                        ],
                        3 => [
                            "resource_set_id" => "de93268b-853e-4a66-b550-764b0f01b8a2",
                            "resource_set_name" => "Default Resource"
                        ],
                        4 => [
                            "scopes" => [
                                0 => "scopes:create",
                                1 => "scopes:view"
                            ],
                            "resource_set_id" => "340d2399-b92f-4f22-91cb-58ee489d8342",
                            "resource_set_name" => "res:campaign",
                        ]
                    ]
                ]
            ])
        ];
    }

    protected function getToken($grant = 'password', $username = 'username', $password = 'password')
    {
        return $this->provider->getAccessToken($grant, [
            'username' => $username,
            'password' => $password
        ]);
    }

    protected function factoryRealmsResponse()
    {
        $faked = Factory::create();
        $count = $faked->numberBetween(2, 5);
        $items = [];

        for ($i = 0; $i < $count; $i++) {
            $items[] = $this->factoryRealmResponse();
        }

        return $items;
    }

    /**
     * Response factory
     * just for reduce a code amount
     *
     * @param null $data
     * @param null $responseCode
     * @return mixed
     */
    protected function responseFactory($data = null, $responseCode = null)
    {

        $response = \Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->andReturn(json_encode($data));
        $response->shouldReceive('getStatusCode')->andReturn($responseCode);
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);

        return $response;
    }

    /**
     * Get mocked response for token
     */
    protected function getFakeTokenResponse()
    {
        return $this->responseFactory($this->factoryTokenResponse());
    }

    protected function factoryClientsPoliciesResponse()
    {
        $items = [];
        $items[] = $this->factoryUserPolicy();

        return $items;
    }

    protected function factoryRole($count = 1)
    {
        $faker = Factory::create();

        $items = [];

        for ($i = 0; $i < $count; $i++) {
            $items[] = [
                'id' => $faker->uuid,
                'required' => $faker->boolean
            ];
        }

        return count($items) === 1?$items[0]:$items;
    }

    protected function factoryUser($count = 1, $fields = ['id'])
    {
        $faker = Factory::create();

        $items = collect();

        for ($i = 0; $i < $count; $i++) {
            $items[] = [
                'id' => $faker->uuid,
                'name' => $faker->name
            ];
        }

        return $items->pluck($fields)->toArray();
    }

    protected function factoryUserPolicy()
    {
        $faker = Factory::create();
        return [
        "id" => $faker->uuid,
        "name" => $faker->word,
        "type" => "user",
        "logic" => "POSITIVE",
        "decisionStrategy" => "UNANIMOUS",
        "config" => [
            "users" => json_encode($this->factoryUser(5))
            ]
        ];
    }

    protected function factoryRolePolicy()
    {
        $faker = Factory::create();
        return [
            "id" => $faker->uuid,
            "name" => $faker->word,
            "type" => "role",
            "logic" => "POSITIVE",
            "decisionStrategy" => "UNANIMOUS",
            "config" => [
                "roles" => json_encode($this->factoryUser(5))
            ]
        ];
    }

//{
//"id": "5fceb479-6b95-4559-8ff7-4f90c8143a8d",
//"name": "Admin",
//"type": "role",
//"logic": "POSITIVE",
//"decisionStrategy": "UNANIMOUS",
//"config": {
//"roles": "[{\"id\":\"d02b1988-b623-4d3c-83a8-76e2145d1b51\",\"required\":true}]"
//}
//},
//{
//    "id": "b7b3b7db-7fe1-4b85-a01b-699512e74c4e",
//        "name": "Admin or Advertiser or Analyst",
//        "type": "aggregate",
//        "logic": "POSITIVE",
//        "decisionStrategy": "AFFIRMATIVE",
//        "config": {}
//    },
//{
//    "id": "9a11392b-7e54-4e40-826e-96811d0115db",
//        "name": "Advertiser",
//        "type": "role",
//        "logic": "POSITIVE",
//        "decisionStrategy": "UNANIMOUS",
//        "config": {
//    "roles": "[{\"id\":\"93015617-93b2-4a72-b0ff-d20c3ad60c1f\",\"required\":true}]"
//        }
//    },

    protected function factoryClientsResponse()
    {
        $faked = Factory::create();
        $count = $faked->numberBetween(2, 5);
        $items = [];

        for ($i = 0; $i < $count; $i++) {
            $items[] = $this->factoryClientResponse();
        }

        return $items;
    }

    protected function setProviderClient($client)
    {
        $this->provider->setHttpClient($client); //set mocked http client to fake responses
    }

    protected function factoryNewClientResourceResponse()
    {
        $faked = Factory::create();
        return [
            '_id' => $faked->uuid
        ];
    }

    protected function factoryClientResourcesResponse()
    {
        $faked = Factory::create();
        $count = $faked->numberBetween(2, 5);
        $items = [];

        for ($i = 0; $i < $count; $i++) {
            $items[] = $this->factoryClientResourceResponse();
        }

        return $items;
    }

    protected function factoryClientResourceResponse()
    {
        $faked = Factory::create();

        return [
            "name" => $faked->word,
            "scopes" => [
                [
                    "id" => $faked->uuid,
                    "name" => $faked->word
                ],
                [
                    "id" => $faked->uuid,
                    "name" => $faked->word
                ]
            ],
            "owner" => [
                "id" => $faked->uuid,
                "name" => $faked->word
            ],
            "_id" => $faked->uuid
        ];
    }

    protected function factoryClientResponse()
    {
        $faked = Factory::create();

        return [
            "id" => $faked->uuid,
            "clientId" => $faked->word,
            "name" => '${client_admin-cli}',
            "surrogateAuthRequired" => false,
            "enabled" => true,
            "clientAuthenticatorType" => "client-secret",
            "redirectUris" => [],
            "webOrigins" => [],
            "notBefore" => 0,
            "bearerOnly" => false,
            "consentRequired" => false,
            "standardFlowEnabled" => false,
            "implicitFlowEnabled" => false,
            "directAccessGrantsEnabled" => true,
            "serviceAccountsEnabled" => false,
            "publicClient" => true,
            "frontchannelLogout" => false,
            "protocol" => "openid-connect",
            "attributes" => [],
            "fullScopeAllowed" => false,
            "nodeReRegistrationTimeout" => 0,
            "protocolMappers" => [
                [
                    "id" => "79dd1d29-95f2-4ed5-80e7-c1c4f13039e6",
                    "name" => "given name",
                    "protocol" => "openid-connect",
                    "protocolMapper" => "oidc-usermodel-property-mapper",
                    "consentRequired" => true,
                    "consentText" => "$[givenName]",
                    "config" => [
                        "userinfo.token.claim" => "true",
                        "user.attribute" => "firstName",
                        "id.token.claim" => "true",
                        "access.token.claim" => "true",
                        "claim.name" => "given_name",
                        "jsonType.label" => "String"
                    ]
                ],
                [
                    "id" => "fdc1c2c7-56f2-4c60-9e37-a1d42ed00e44",
                    "name" => "email",
                    "protocol" => "openid-connect",
                    "protocolMapper" => "oidc-usermodel-property-mapper",
                    "consentRequired" => true,
                    "consentText" => "$[email]",
                    "config" => [
                        "userinfo.token.claim" => "true",
                        "user.attribute" => "email",
                        "id.token.claim" => "true",
                        "access.token.claim" => "true",
                        "claim.name" => "email",
                        "jsonType.label" => "String"
                    ]
                ],
                [
                    "id" => "dd51a2df-c841-4b23-892c-7230227ed10c",
                    "name" => "username",
                    "protocol" => "openid-connect",
                    "protocolMapper" => "oidc-usermodel-property-mapper",
                    "consentRequired" => true,
                    "consentText" => "$[username]",
                    "config" => [
                        "userinfo.token.claim" => "true",
                        "user.attribute" => "username",
                        "id.token.claim" => "true",
                        "access.token.claim" => "true",
                        "claim.name" => "preferred_username",
                        "jsonType.label" => "String"
                    ]
                ],
                [
                    "id" => "9633f538-ae40-4a82-a85b-d5186584335b",
                    "name" => "full name",
                    "protocol" => "openid-connect",
                    "protocolMapper" => "oidc-full-name-mapper",
                    "consentRequired" => true,
                    "consentText" => "$[fullName]",
                    "config" => [
                        "id.token.claim" => "true",
                        "access.token.claim" => "true"
                    ]
                ],
                [
                    "id" => "c3df6114-6069-4d78-8c6a-5b8a4bce2de0",
                    "name" => "family name",
                    "protocol" => "openid-connect",
                    "protocolMapper" => "oidc-usermodel-property-mapper",
                    "consentRequired" => true,
                    "consentText" => "$[familyName]",
                    "config" => [
                        "userinfo.token.claim" => "true",
                        "user.attribute" => "lastName",
                        "id.token.claim" => "true",
                        "access.token.claim" => "true",
                        "claim.name" => "family_name",
                        "jsonType.label" => "String"
                    ]
                ],
                [
                    "id" => "c4ab62a9-8b74-4095-a8c1-8ba505db10cb",
                    "name" => "role list",
                    "protocol" => "saml",
                    "protocolMapper" => "saml-role-list-mapper",
                    "consentRequired" => false,
                    "config" => [
                        "single" => "false",
                        "attribute.nameformat" => "Basic",
                        "attribute.name" => "Role"
                    ]
                ]
            ],
            "useTemplateConfig" => false,
            "useTemplateScope" => false,
            "useTemplateMappers" => false,
            "access" => [
                "view" => true,
                "configure" => true,
                "manage" => true
            ]
        ];
    }

    protected function factoryRealmResponse()
    {
        $faked = Factory::create();
        $realm = $faked->word;
        return [
            "id" => $realm,
            "realm" => $realm,
            "notBefore" => 0,
            "revokeRefreshToken" => false,
            "accessTokenLifespan" => 300,
            "accessTokenLifespanForImplicitFlow" => 900,
            "ssoSessionIdleTimeout" => 1800,
            "ssoSessionMaxLifespan" => 36000,
            "offlineSessionIdleTimeout" => 2592000,
            "accessCodeLifespan" => 60,
            "accessCodeLifespanUserAction" => 300,
            "accessCodeLifespanLogin" => 1800,
            "actionTokenGeneratedByAdminLifespan" => 43200,
            "actionTokenGeneratedByUserLifespan" => 300,
            "enabled" => true,
            "sslRequired" => "external",
            "registrationAllowed" => false,
            "registrationEmailAsUsername" => false,
            "rememberMe" => false,
            "verifyEmail" => false,
            "loginWithEmailAllowed" => true,
            "duplicateEmailsAllowed" => false,
            "resetPasswordAllowed" => false,
            "editUsernameAllowed" => false,
            "bruteForceProtected" => false,
            "permanentLockout" => false,
            "maxFailureWaitSeconds" => 900,
            "minimumQuickLoginWaitSeconds" => 60,
            "waitIncrementSeconds" => 60,
            "quickLoginCheckMilliSeconds" => 1000,
            "maxDeltaTimeSeconds" => 43200,
            "failureFactor" => 30,
            "requiredCredentials" => [
                "password"
            ],
            "otpPolicyType" => "totp",
            "otpPolicyAlgorithm" => "HmacSHA1",
            "otpPolicyInitialCounter" => 0,
            "otpPolicyDigits" => 6,
            "otpPolicyLookAheadWindow" => 1,
            "otpPolicyPeriod" => 30,
            "browserSecurityHeaders" => [
                "xContentTypeOptions" => "nosniff",
                "xRobotsTag" => "none",
                "xFrameOptions" => "SAMEORIGIN",
                "xXSSProtection" => "1; mode=block",
                "contentSecurityPolicy" => "frame-src 'self'"
            ],
            "smtpServer" => [],
            "eventsEnabled" => false,
            "eventsListeners" => [
                "jboss-logging"
            ],
            "enabledEventTypes" => [],
            "adminEventsEnabled" => false,
            "adminEventsDetailsEnabled" => false,
            "internationalizationEnabled" => false,
            "supportedLocales" => [],
            "browserFlow" => "browser",
            "registrationFlow" => "registration",
            "directGrantFlow" => "direct grant",
            "resetCredentialsFlow" => "reset credentials",
            "clientAuthenticationFlow" => "clients",
            "dockerAuthenticationFlow" => "docker auth",
            "attributes" => [
                "_browser_header.xXSSProtection" => "1; mode=block",
                "_browser_header.xFrameOptions" => "SAMEORIGIN",
                "permanentLockout" => "false",
                "quickLoginCheckMilliSeconds" => "1000",
                "_browser_header.xRobotsTag" => "none",
                "maxFailureWaitSeconds" => "900",
                "minimumQuickLoginWaitSeconds" => "60",
                "failureFactor" => "30",
                "actionTokenGeneratedByUserLifespan" => "300",
                "maxDeltaTimeSeconds" => "43200",
                "_browser_header.xContentTypeOptions" => "nosniff",
                "actionTokenGeneratedByAdminLifespan" => "43200",
                "bruteForceProtected" => "false",
                "_browser_header.contentSecurityPolicy" => "frame-src 'self'",
                "waitIncrementSeconds" => "60"
            ]
        ];
    }
}