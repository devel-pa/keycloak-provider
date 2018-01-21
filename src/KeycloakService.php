<?php

namespace Keycloak;

use Illuminate\Http\Request;
use Keycloak\Values\PermissionValue;
use Psr\Log\LoggerInterface;
use Keycloak\Response\ProviderResponse;
use Illuminate\Contracts\Session\Session;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;

class KeycloakService implements KeycloakServiceContract
{

    /**
     * @var KeycloakProvider
     */
    protected $provider;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var AccessToken
     */
    protected $token = null;

    /**
     * KeycloakProvider constructor.
     * @param KeycloakProvider $provider
     * @param Request $request
     * @param Session $session
     * @param LoggerInterface $logger
     */
    public function __construct(
        KeycloakProvider $provider,
        Request $request,
        Session $session,
        LoggerInterface $logger
    ) {
        $this->provider = $provider;

        $this->session = $session;

        $this->request = $request;

        $this->logger = $logger;
    }

    /**
     * Renew token from Keycloak server
     *
     * @return ProviderResponse
     */
    public function requestNewToken()
    {
        /** @var KeycloakProvider $provider */
        $provider = $this->provider;

        $response = new ProviderResponse();

        // be careful, you need trigger getAuthorizationUrl before access to getState
        $authUrl = $this->getAuthorizationUrl();

        if (!$this->request->exists('code')) {

            $state = $provider->getState();

            $this->session->put('oauth2state', $state);

            $response->redirectTo($authUrl);

            return $response;

        } elseif (empty($this->request->get('state')) || ($this->session->exists('oauth2state') && !empty($this->session->exists('oauth2state')) && ($this->request->get('state') !== $this->session->get('oauth2state')))) {

            $this->forgetToken();

            $response->withError("Invalid state, make sure HTTP sessions are enabled.", 500);

            $response->redirectTo($authUrl);

            $this->logger->error('Invalid state, make sure HTTP sessions are enabled' . ' line ' . __LINE__ . ' [ ' . __FILE__ . ' ]');

            return $response;
        }

        try {

            $token = $this->request->bearerToken();

            if (!empty($token)) {
                $keyCloakAccessToken = $provider->getAccessToken('client_credentials');
            } else {
                /** @var \League\OAuth2\Client\Token\AccessToken $keyCloakAccessToken */
                $keyCloakAccessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $this->request->get('code'),
                ]);
            }

        } catch (\Exception $e) {

            $this->forgetToken();

            $response->withError('Failed to get access token: ' . $e->getMessage(), 500);

            $response->redirectTo($authUrl);

            $this->logger->error('Failed to get access token: ' . $e->getMessage() . ' line ' . $e->getLine() . ' [ ' . $e->getFile() . ' ]');

            return $response;
        }

        $this->setToken($keyCloakAccessToken);

        $response->redirectTo(config('keycloak.redirectUri'))->withToken($this->getToken());

        return $response;
    }

    /**
     * Returns external link to auth server
     *
     * @return string
     */
    public function getAuthorizationUrl()
    {
        return $this->provider->getAuthorizationUrl();
    }

    /**
     * Forget token data
     *
     * @return void
     */
    protected function forgetToken()
    {
        $this->session->forget('oauth2state');
        $this->session->forget('oauth2token');
    }

    /**
     * Get current token
     *
     * @return mixed|null
     */
    public function getToken()
    {
        $token = $this->session->get('oauth2token');

        if ($token instanceof AccessToken) {
            return $token;
        }

        return $this->token;
    }

    /**
     * Set token
     *
     * @param AccessToken $token
     * @return AccessToken
     */
    public function setToken(AccessToken $token)
    {
        $this->session->put('oauth2token', $token);

        $this->token = $token;

        return $token;
    }

    /**
     * Save token received from auth server
     *
     * @return bool
     */
    public function saveToken()
    {
        $keyCloakAccessToken = null;

        try {
            // try to get token from header Bearer
            $extractor = new AuthorizationHeaderTokenExtractor('Bearer', 'Authorization');

            $keyCloakAccessTokenPreAuth = $extractor->extract($this->request);

            /** @var AccessToken $keyCloakAccessToken */
            // has BearerToken
            if ($keyCloakAccessTokenPreAuth != false) {
                $keyCloakAccessToken = new AccessToken(['access_token' => $keyCloakAccessTokenPreAuth]);

                // try to get owner resource, if no exception the token is ok
                $this->provider->getResourceOwner($keyCloakAccessToken);

            } else {
                $keyCloakAccessToken = $this->provider->getAccessToken('authorization_code', [
                    'code' => $this->request->get('code'),
                ]);
            }

        } catch (\Exception $e) {

            $this->forgetToken();

            $this->logger->error('Failed to save a token: ' . $e->getMessage() . ' line ' . $e->getLine() . ' [ ' . $e->getFile() . ' ]');

            return false;
        }

        $this->setToken($keyCloakAccessToken);

        return true;
    }

    /**
     * Check if current token is expired
     *
     * @return bool
     */
    public function isTokenExpired()
    {
        if ($this->hasToken()) {
            /** @var AccessToken $token */
            $token = $this->getToken();

            return time() >= $token->getExpires();
        }

        return true;
    }

    /**
     * Check if current session has a token
     *
     * @return bool
     */
    public function hasToken()
    {
        return ($this->session->exists('oauth2token') && $this->session->get('oauth2token') instanceof AccessToken);
    }

    /**
     * Refresh token
     *
     * @return bool
     */
    public function refreshToken()
    {
        if (!$this->hasToken()) { // if no current token

            $this->forgetToken();

            return false;
        }

        $this->getAuthorizationUrl();

        $this->session->put('oauth2state', $this->provider->getState());

        try {
            /** @var \League\OAuth2\Client\Token\AccessToken $keyCloakAccessToken */
            $keyCloakAccessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $this->getToken()->getRefreshToken(),
            ]);

            $this->setToken($keyCloakAccessToken);

        } catch (\Exception $e) {

            $this->forgetToken();

            $this->logger->error('Failed to refresh access token: ' . $e->getMessage() . ' line ' . $e->getLine() . ' [ ' . $e->getFile() . ' ]');

            return false;
        }

        return true;
    }

    /**
     * Request new resource
     *
     * @return $this|ProviderResponse
     * @throws \Stevenmaguire\OAuth2\Client\Provider\Exception\EncryptionConfigurationException
     */
    public function requestResourceOwner()
    {
        $response = new ProviderResponse();

        $authUrl = $this->getAuthorizationUrl();

        $token = $this->getToken();

        if ($token instanceof AccessToken) {
            try {

                return $response->withResourceOwner($this->provider->getResourceOwner($token)->toArray());

            } catch (IdentityProviderException $e) {

                $response->redirectTo($authUrl)->withError($e->getMessage(), $e->getCode());

                $this->forgetToken();

                $this->logger->error('Failed to get resource owner: ' . $e->getMessage() . ' line ' . $e->getLine() . ' [ ' . $e->getFile() . ' ]');

                return $response;
            }
        }

        return $response;
    }

    /**
     * Get token value string
     *
     * @return null|string
     */
    public function getTokenValue()
    {
        $token = $this->getToken();

        if ($token instanceof AccessToken) {
            return $token->getToken();
        }

        return null;
    }

    /**
     * Check if has permission on specified resource ans scope
     *
     * @param $resourceName
     * @param $scopeName
     * @return bool
     * @throws \Exception
     */
    public function hasPermission($resourceName, $scopeName)
    {
        $hasPermission = false;

        foreach ($this->getPermissions() as $permission) {
            /** @var PermissionValue $permission */
            if ($resourceName === $permission->getResourceName()) {
                $hasScopePermission = false;
                foreach ($permission->getScopes() as $scope) {
                    if ($scopeName === $scope) {
                        $hasScopePermission = true;
                        break;
                    }
                }
                $hasPermission = $hasScopePermission;
                break;
            }
        }

        return $hasPermission;
    }

    /**
     * {@inheritDoc}
     */
    public function getPermissions()
    {
        return $this->provider->getPermissions($this->getToken());
    }

    /**
     * Check if has a permission on specified resource
     *
     * @param $resourceName
     * @return bool
     * @throws \Exception
     */
    public function hasResourcePermission($resourceName)
    {
        $hasPermission = false;

        foreach ($this->getPermissions() as $permission) {
            /** @var PermissionValue $permission */
            if ($resourceName === $permission->getResourceName()) {
                $hasPermission = true;
                break;
            }
        }

        return $hasPermission;
    }

    /**
     * Check if has a permission on specified scope
     *
     * @param $scopeName
     * @return bool
     * @throws \Exception
     */
    public function hasScopePermission($scopeName)
    {
        $hasPermission = false;

        foreach ($this->getPermissions() as $permission) {
            /** @var PermissionValue $permission */
            $hasScopePermission = false;

            foreach ($permission->getScopes() as $scope) {
                if ($scopeName === $scope) {
                    $hasScopePermission = true;
                    break;
                }
            }

            if ($hasScopePermission) {
                $hasPermission = true;
                break;
            }
        }

        return $hasPermission;
    }

    /**
     * Common function to check role
     *
     * @param string|array $role
     * @return bool
     * @throws Exception\WrongResourceException
     */
    public function hasRole($role)
    {
        return ($this->hasRealmRole($role) || $this->hasResourceRole($role));
    }

    /**
     * Check realm role
     *
     * @param string|array $role
     * @return bool
     * @throws Exception\WrongResourceException
     */
    public function hasRealmRole($role)
    {
        $hasRole = false;

        if (!is_array($role)) {
            $role = (array)$role;
        }

        $accesses = $this->provider->getRealmAccess($this->getToken());

        foreach ($accesses as $access) {
            $roles = $access->getRoles();

            foreach ($role as $r) {
                if (in_array($r, $roles)) {
                    $hasRole = true;
                    break;
                }
            }

        }

        return $hasRole;
    }

    /**
     * Check resource role
     * if $resource is null when it checks clientId access
     *
     * @param string|array $role
     * @param null $resource
     * @return bool
     * @throws Exception\WrongResourceException
     */
    public function hasResourceRole($role, $resource = null)
    {
        $hasRole = false;

        if (!is_array($role)) {
            $role = (array)$role;
        }

        if (null === $resource) {
            $resource = $this->provider->getClientId();
        }

        $accesses = $this->provider->getResourceAccess($this->getToken());

        foreach ($accesses as $access) {
            if ($access->getResource() === $resource) {
                $roles = $access->getRoles();

                foreach ($role as $r) {
                    if (in_array($r, $roles)) {
                        $hasRole = true;
                        break;
                    }
                }

            }
        }

        return $hasRole;
    }
}