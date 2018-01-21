<?php

namespace Tests;

use Illuminate\Http\Request;
use Keycloak\KeycloakService;
use Mockery as m;
use Keycloak\KeycloakProvider;
use Keycloak\KeycloakServiceContract;
use Keycloak\Values\PermissionValue;
use Keycloak\Values\PermissionsValue;
use Keycloak\Values\RealmAccessValue;
use Keycloak\Values\RealmAccessesValue;
use Keycloak\Values\ResourceAccessValue;
use Keycloak\Values\ResourceAccessesValue;
use Illuminate\Contracts\Support\Arrayable;
use PHPUnit\Framework\TestCase;

class KeycloakProviderTest extends TestCase
{
    use KeycloakFakeFactoryTrait;
    /**
     * @var KeycloakProvider
     */
    protected $provider;
    /**
     * @var KeycloakServiceContract
     */
    protected $service;
    /**
     * @var 
     */
    protected $realm;
    /**
     * @var
     */
    protected $clientId;
    /**
     * @var
     */
    protected $fakeTokenResponse;
    /**
     * @var
     */
    protected $fakeEntitlementResponse;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        $this->setupKeycloak();

        $this->fakeTokenResponse = $this->factoryTokenResponse();

        $this->fakeEntitlementResponse = $this->factoryPartyToken();

        // for fake keycloak
        $this->provider = new KeycloakProvider([
            'authServerUrl' => 'http://mock.url/auth',
            'realm'         => 'mock_realm',
            'clientId'      => $this->fakeClientId,
            'clientSecret'  => 'mock_secret',
            'redirectUri'   => 'none',
        ]);

//        $this->provider = new KeycloakProvider([
//            'authServerUrl' => config('keycloak.authServerUrl'),
//            'realm'         => config('keycloak.realm'),
//            'clientId'      => 'CAMPAIGN_CLIENT',
//            'clientSecret'  => '093702f6-fb7f-4efc-86f7-7a9015c3a97b',
//            'redirectUri'   => config('keycloak.redirectUri'),
//            'proxy'         => config('keycloak.proxy'),
//            'verify'        => config('keycloak.verify'),
//            'curl'          => [
//                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2
//            ]
//        ]);
//        // real keycloak
//        $this->provider = new KeycloakProvider([
//            'authServerUrl' => config('keycloak.authServerUrl'),
//            'realm'         => config('keycloak.realm'),
//            'clientId'      => config('keycloak.clientId'),
//            'clientSecret'  => config('keycloak.clientSecret'),
//            'redirectUri'   => config('keycloak.redirectUri'),
//            'proxy'         => config('keycloak.proxy'),
//            'verify'        => config('keycloak.verify'),
//            'curl'          => [
//                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2
//            ]
//        ]);

        $this->provider->setEncryptionKey('key');
        $this->provider->setEncryptionAlgorithm('HS256');

        $session = m::mock('Illuminate\Contracts\Session\Session');
        $session->shouldReceive('put')->andReturn(true);
        $session->shouldReceive('get')->andReturn([]);

        $logger = m::mock('Psr\Log\LoggerInterface');

        $this->service = new KeycloakService($this->provider, Request::capture(),  $session, $logger);
    }

    /**
     * Get mocked response for token
     *
     * @return m\MockInterface
     */
    protected function getFakeTokenResponse()
    {
        $tokenResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $tokenResponse->shouldReceive('getBody')->andReturn(json_encode($this->fakeTokenResponse));
        $tokenResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);

        return $tokenResponse;
    }

    /** @test */
    public function get_obtain_a_token_by_given_login_and_password()
    {
        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($this->getFakeTokenResponse());
        $this->provider->setHttpClient($client); //set mocked http client to fake responses

        // auth like a user
        $token = $this->getToken();

        $this->assertEquals($this->fakeTokenResponse['access_token'], $token->getToken());
    }

    /** @test */
    public function can_get_resource_access_values()
    {
        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn(
            $this->getFakeTokenResponse() // token response
        );

        $this->provider->setHttpClient($client); //set mocked http client to fake responses

        $resourceAccesses = $this->provider->getResourceAccess($this->getToken());

        $this->assertInstanceOf(\IteratorAggregate::class, $resourceAccesses);
        $this->assertInstanceOf(Arrayable::class, $resourceAccesses);

        $resourceAccess = array_first($resourceAccesses);

        $this->assertInstanceOf(ResourceAccessesValue::class, $resourceAccesses);
        $this->assertInstanceOf(ResourceAccessValue::class, $resourceAccess);

    }

    /** @test */
    public function can_get_realm_access_values()
    {
        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn(
            $this->getFakeTokenResponse() // token response
        );

        $this->provider->setHttpClient($client); //set mocked http client to fake responses

        $realmAccesses = $this->provider->getRealmAccess($this->getToken());

        $this->assertInstanceOf(\IteratorAggregate::class, $realmAccesses);
//        $this->assertInstanceOf(Arrayable::class, $realmAccesses);

        $realmAccess = array_first($realmAccesses);

        $this->assertInstanceOf(RealmAccessesValue::class, $realmAccesses);
        $this->assertInstanceOf(RealmAccessValue::class, $realmAccess);

    }
    /** @test */
    public function can_get_permissions()
    {
        $permissionsResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $permissionsResponse->shouldReceive('getBody')->andReturn(json_encode($this->fakeEntitlementResponse));
        $permissionsResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(2)->andReturn(
            $this->getFakeTokenResponse(), // token response
            $permissionsResponse // permissions response
        );

        $this->provider->setHttpClient($client); //set mocked http client to fake responses

        // auth like a user
        $token = $this->getToken();

        $permissions = $this->provider->getPermissions($token);

        $this->assertInstanceOf(\IteratorAggregate::class, $permissions);
        $this->assertInstanceOf(Arrayable::class, $permissions);

        $permission = array_first($permissions);

        $this->assertInstanceOf(PermissionValue::class, $permission);
        $this->assertInstanceOf(PermissionsValue::class, $permissions);
    }

    /** @test */
    public function can_check_user_permissions_using_service()
    {
        $permissionsResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $permissionsResponse->shouldReceive('getBody')->andReturn(json_encode($this->fakeEntitlementResponse));
        $permissionsResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(4)->andReturn(
            $this->getFakeTokenResponse(), // token response
            $permissionsResponse, // permissions response
            $permissionsResponse, // permissions response
            $permissionsResponse // permissions response
        );

        $this->provider->setHttpClient($client); //set mocked http client to fake responses

        $this->service->setToken($this->getToken()); // set the fake token

        $hasPermissionByScopeName = $this->service->hasScopePermission("scopes:view");
        $this->assertTrue($hasPermissionByScopeName);

        $hasPermissionByResourceAndScopeName = $this->service->hasPermission("res:campaign", "scopes:view");
        $this->assertTrue($hasPermissionByResourceAndScopeName);

        $hasPermissionByResourceName = $this->service->hasResourcePermission("res:campaign");
        $this->assertTrue($hasPermissionByResourceName);
    }

    /** @test */
    public function can_check_user_has_role_using_service()
    {
        $permissionsResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $permissionsResponse->shouldReceive('getBody')->andReturn(json_encode($this->fakeEntitlementResponse));
        $permissionsResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn(
            $this->getFakeTokenResponse() // token response
        );

        $this->provider->setHttpClient($client); //set mocked http client to fake responses

        $this->service->setToken($this->getToken()); // set the fake token

        $hasRealRole = $this->service->hasRealmRole("admin");
        $this->assertTrue($hasRealRole);

        $hasRealRole = $this->service->hasRealmRole("not_existed_realm_role");
        $this->assertFalse($hasRealRole);

        $hasResourceRole = $this->service->hasResourceRole("uma_protection");
        $this->assertTrue($hasResourceRole);

        $hasResourceRole = $this->service->hasResourceRole("not_existed_resource_role");
        $this->assertFalse($hasResourceRole);


        $hasRole = $this->service->hasRole("uma_protection");
        $this->assertTrue($hasRole);

        $hasRole = $this->service->hasRole("not_existed_role");
        $this->assertFalse($hasRole);

    }
}
