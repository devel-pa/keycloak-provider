<?php

namespace Keycloak\Response;

use League\OAuth2\Client\Token\AccessToken;

class ProviderResponse
{
    /**
     * @var AccessToken
     */
    protected $token;
    /**
     * @var array
     */
    protected $resourceOwner;
    /**
     * @var string
     */
    protected $redirectTo;
    /**
     * @var string
     */
    protected $errorMessage;
    /**
     * @var string|integer
     */
    protected $errorCode;

    /**
     * Set resource of owner (user information)
     *
     * @param $resourceOwner
     * @return $this
     */
    public function withResourceOwner($resourceOwner)
    {
        $this->resourceOwner = $resourceOwner;

        return $this;
    }

    /**
     * Get resource of owner
     *
     * @return array
     */
    public function getResourceOwner()
    {
        return $this->resourceOwner;
    }

    /**
     * Set the session token
     *
     * @param AccessToken $token
     * @return $this
     */
    public function withToken(AccessToken $token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get current token
     *
     * @return AccessToken
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set redirect uri
     *
     * @param $uri
     * @return $this
     */
    public function redirectTo($uri)
    {
        $this->redirectTo = $uri;

        return $this;
    }

    /**
     * Check if redirect is needed
     *
     * @return bool
     */
    public function isNeedToRedirect()
    {
        return !empty($this->redirectTo);
    }

    /**
     * Get redirect string
     *
     * @return string
     */
    public function getRedirectTo()
    {
        return $this->redirectTo;
    }

    /**
     * Check if error has
     *
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->errorMessage);
    }

    /**
     * Get error bag
     *
     * @return array
     */
    public function getErrorBag()
    {
        return [
            'message' => $this->errorMessage,
            'code' => $this->errorCode
        ];
    }

    /**
     * Set error bag
     *
     * @param $message
     * @param null $code
     * @return $this
     */
    public function withError($message, $code = null)
    {
        $this->errorMessage = $message;

        $this->errorCode = $code;

        return $this;
    }
}
