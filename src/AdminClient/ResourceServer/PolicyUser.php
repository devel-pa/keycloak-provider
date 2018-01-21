<?php

namespace Keycloak\AdminClient\ResourceServer;

use Illuminate\Support\Collection;
use Keycloak\AdminClient\AdminClient;
use Keycloak\AdminClient\ClientEntity;
use Keycloak\AdminClient\Values\Value;
use Keycloak\AdminClient\Values\PolicyValue;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Exception\BadResponseException;
use Symfony\Component\HttpFoundation\Response;
use Keycloak\AdminClient\Values\Policy\PolicyUserValue;

class PolicyUser extends ClientEntity
{
    /**
     * @var Policy
     */
    protected $policy;

    /**
     * Factory to crate instance of policy user
     *
     * @param AdminClient $adminClient
     * @param Policy $policy
     * @return static
     */
    public static function factory(AdminClient $adminClient, Policy $policy)
    {
        $instance = new static($adminClient);
        $instance->setPolicy($policy);

        return $instance;
    }

    /**
     * Set policy object
     *
     * @param Policy $policy
     * @return $this
     */
    public function setPolicy(Policy $policy)
    {
        $this->policy = $policy;

        return $this;
    }

    /**
     * Find all or one item
     *
     * @param null|string $id
     * @return Collection|PolicyUserValue
     */
    public function find($id = null)
    {
        if (null === $id) {
            $url = $this->getUserPoliciesUrl();
        } else {
            $url = $this->getUserPolicyUrl($id);
        }

        $request = $this->getAuthenticatedRequest(Request::METHOD_GET, $url);

        $response = $this->adminClient->getParsedResponse($request);

        if (null === $id) {
            return Collection::make($response)->map(function ($item) {
                return PolicyUserValue::fromRaw($item);
            });
        }

        return PolicyUserValue::fromPolicy(PolicyValue::fromResponse($response));
    }

    /**
     * Get user policies url
     *
     * @return string
     */
    public function getUserPoliciesUrl()
    {
        return $this->policy->getPoliciesUrl() . '/user';
    }

    /**
     * Get user policy url
     *
     * @param string $uuid
     * @return string
     */
    public function getUserPolicyUrl($uuid)
    {
        return $this->getUserPoliciesUrl() . '/' . $uuid;
    }

    /**
     * Create new user policy
     *
     * @param Value $value
     * @return PolicyUserValue
     */
    public function create(Value $value)
    {
        $url = $this->getUserPoliciesUrl();

        $request = $this->getAuthenticatedRequest(Request::METHOD_POST, $url, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => $value->toJson()
        ]);

        $response = $this->adminClient->getParsedResponse($request);

        return PolicyUserValue::fromRaw($response);
    }

    /**
     * Update user policy
     *
     * @param Value $value
     * @return bool
     */
    public function update(Value $value)
    {
        $url = $this->getUserPolicyUrl($value->getId());

        $request = $this->getAuthenticatedRequest(Request::METHOD_PUT, $url, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => $value->toJson()
        ]);

        try {
            $responseCode = $this->adminClient->getResponse($request)->getStatusCode();
        } catch (BadResponseException $e) {
            $responseCode = $e->getResponse()->getStatusCode();
        }

        return $responseCode === Response::HTTP_CREATED;
    }

    /**
     * Delete user policy
     *
     * @param Value $value
     * @return bool
     */
    public function delete(Value $value)
    {
        $url = $this->getUserPolicyUrl($value->getId());

        $request = $this->getAuthenticatedRequest(Request::METHOD_DELETE, $url, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => $value->toJson()
        ]);

        try {
            $responseCode = $this->adminClient->getResponse($request)->getStatusCode();
        } catch (BadResponseException $e) {
            $responseCode = $e->getResponse()->getStatusCode();
        }

        return $responseCode === Response::HTTP_NO_CONTENT;
    }
}
