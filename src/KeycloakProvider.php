<?php

namespace Keycloak;

use Exception;
use Firebase\JWT\JWT;
use Keycloak\Exception\WrongResourceException;
use Keycloak\Values\PermissionsValue;
use Keycloak\Values\RealmAccessesValue;
use Keycloak\Values\ResourceAccessesValue;
use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Stevenmaguire\OAuth2\Client\Provider\KeycloakResourceOwner;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Stevenmaguire\OAuth2\Client\Provider\Exception\EncryptionConfigurationException;

class KeycloakProvider extends AbstractProvider
{

    use BearerAuthorizationTrait;

    /**
     * Keycloak URL, eg. http://localhost:8080/auth.
     *
     * @var string
     */
    public $authServerUrl = null;

    /**
     * Realm name, eg. demo.
     *
     * @var string
     */
    public $realm = null;

    /**
     * Encryption algorithm.
     *
     * You must specify supported algorithms for your application. See
     * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
     * for a list of spec-compliant algorithms.
     *
     * @var string
     */
    public $encryptionAlgorithm = null;

    /**
     * Encryption key.
     *
     * @var string
     */
    public $encryptionKey = null;

    /**
     * Constructs an OAuth 2.0 service provider.
     *
     * @param array $options An array of options to set on this provider.
     *     Options include `clientId`, `clientSecret`, `redirectUri`, and `state`.
     *     Individual providers may introduce more options, as needed.
     * @param array $collaborators An array of collaborators that may be used to
     *     override this provider's default behavior. Collaborators include
     *     `grantFactory`, `requestFactory`, `httpClient`, and `randomFactory`.
     *     Individual providers may introduce more collaborators, as needed.
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        if (isset($options['encryptionKeyPath'])) {
            $this->setEncryptionKeyPath($options['encryptionKeyPath']);
            unset($options['encryptionKeyPath']);
        }
        parent::__construct($options, $collaborators);
    }

    /**
     * Updates expected encryption key of Keycloak instance to content of given
     * file path.
     *
     * @param string $encryptionKeyPath
     *
     * @return self
     */
    public function setEncryptionKeyPath($encryptionKeyPath)
    {
        try {
            $this->encryptionKey = file_get_contents($encryptionKeyPath);
        } catch (Exception $e) {
            // Not sure how to handle this yet.
        }

        return $this;
    }

    /**
     * Get client id of current connection
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->getBaseUrlWithRealm() . '/protocol/openid-connect/auth';
    }

    /**
     * Creates base url from provider configuration.
     *
     * @return string
     */
    protected function getBaseUrlWithRealm()
    {
        return $this->authServerUrl . '/realms/' . $this->realm;
    }

    /**
     * Get access token url to retrieve token
     *
     * @param  array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->getBaseUrlWithRealm() . '/protocol/openid-connect/token';
    }

    /**
     * Get provider url to fetch user details
     *
     * @param  AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->getBaseUrlWithRealm() . '/protocol/openid-connect/userinfo';
    }

    /**
     * Requests and returns the resource owner of given access token.
     *
     * @param  AccessToken $token
     * @return KeycloakResourceOwner
     * @throws EncryptionConfigurationException
     */
    public function getResourceOwner(AccessToken $token)
    {
        $response = $this->fetchResourceOwnerDetails($token);

        $response = $this->decryptResponse($response);

        return $this->createResourceOwner($response, $token);
    }

    /**
     * Attempts to decrypt the given response.
     *
     * @param  string|array|null $response
     *
     * @return string|array|null
     */
    public function decryptResponse($response)
    {
        if (is_string($response)) {
            if ($this->encryptionAlgorithm && $this->encryptionKey) {
                $response =
                    json_decode(json_encode(JWT::decode($response, $this->encryptionKey, [$this->encryptionAlgorithm])),
                        true);
            } else {
                list($header, $payload, $signature) = explode(".", $response);
                $jwt = json_decode(base64_decode($payload));
                $response = json_decode(json_encode($jwt), true);
            }
        }

        return $response;
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return KeycloakResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new KeycloakResourceOwner($response);
    }

    /**
     * Updates expected encryption algorithm of Keycloak instance.
     *
     * @param string $encryptionAlgorithm
     *
     * @return self
     */
    public function setEncryptionAlgorithm($encryptionAlgorithm)
    {
        $this->encryptionAlgorithm = $encryptionAlgorithm;

        return $this;
    }

    /**
     * Updates expected encryption key of Keycloak instance.
     *
     * @param string $encryptionKey
     *
     * @return self
     */
    public function setEncryptionKey($encryptionKey)
    {
        $this->encryptionKey = $encryptionKey;

        return $this;
    }

    /**
     * Get permissions
     *
     * @param AccessToken $token
     * @return PermissionsValue
     * @throws \Exception
     */
    public function getPermissions(AccessToken $token)
    {
        $response = $this->fetchUserPermissions($token);

        $response['rpt'] = $this->decryptResponse($response['rpt']);

        return $this->createPermissions($response, $token);
    }

    /**
     * Requests resource owner details.
     *
     * @param  AccessToken $token
     * @return mixed
     */
    protected function fetchUserPermissions(AccessToken $token)
    {
        $url = $this->getEntitlementsUrl($token);

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);

        return $this->getParsedResponse($request);
    }

    /**
     * Get entitlement url
     * @param AccessToken $token
     * @return string
     */
    protected function getEntitlementsUrl(AccessToken $token)
    {
        return $this->getBaseUrlWithRealm() . '/authz/entitlement/' . $this->clientId;
    }

    /**
     * Generate a permissions object
     *
     * @param array $response
     * @param AccessToken $token
     * @return PermissionsValue
     * @throws \Exception
     */
    protected function createPermissions(array $response, AccessToken $token)
    {
        return PermissionsValue::fromResponse($response);
    }

    /**
     * @param AccessToken $token
     * @return ResourceAccessesValue
     * @throws WrongResourceException
     */
    public function getResourceAccess(AccessToken $token)
    {
        $response = $token->getToken();

        $response = $this->decryptResponse($response);

        return ResourceAccessesValue::fromResponse($response);
    }

    /**
     * @param AccessToken $token
     * @return RealmAccessesValue
     * @throws WrongResourceException
     */
    public function getRealmAccess(AccessToken $token)
    {
        $response = $token->getToken();

        $response = $this->decryptResponse($response);

        return RealmAccessesValue::fromResponse($response);
    }

    /**
     * Getter for auth server url
     *
     * @return string
     */
    public function getAuthServerUrl()
    {
        return $this->authServerUrl;
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return string[]
     */
    protected function getDefaultScopes()
    {
        return ['name', 'email'];
    }

    /**
     * Check a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  string $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            $error = $data['error'] . ': ' . $data['error_description'];
            throw new IdentityProviderException($error, 0, $data);
        }
    }

    /**
     * getAllowedClientOptions
     * @param array $options
     * @return array
     */
    protected function getAllowedClientOptions(array $options)
    {
        $client_options = [
            'timeout',
            'proxy',
            'verify'
        ];

        return $client_options;
    }
}