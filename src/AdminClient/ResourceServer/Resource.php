<?php

namespace Keycloak\AdminClient\ResourceServer;

use Illuminate\Support\Collection;
use Keycloak\AdminClient\Values\Value;
use Keycloak\AdminClient\AdminClient;
use Keycloak\AdminClient\ClientEntity;
use Keycloak\AdminClient\ResourceServer;
use Symfony\Component\HttpFoundation\Request;
use Keycloak\AdminClient\Values\ResourceValue;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Exception\BadResponseException;

class Resource extends ClientEntity
{
    /**
     * @var ResourceServer
     */
    protected $resourceServer;

    /**
     * Factory to create resource instance
     *
     * @param AdminClient $adminClient
     * @param ResourceServer $resourceServer
     * @return self
     */
    public static function factory(AdminClient $adminClient, ResourceServer $resourceServer)
    {
        $instance = new static($adminClient);
        $instance->setResourceServer($resourceServer);

        return $instance;
    }

    /**
     * Set resource server
     *
     * @param ResourceServer $resourceServer
     * @return self
     */
    public function setResourceServer(ResourceServer $resourceServer)
    {
        $this->resourceServer = $resourceServer;

        return $this;
    }

    /**
     * Find all or one resource
     *
     * @param null $id
     * @return Collection
     */
    public function find($id = null)
    {
        if (null === $id) {
            $url = $this->getResourcesUrl();
        } else {
            $url = $this->getResourceUrl($id);
        }

        echo $url;

        $request = $this->getAuthenticatedRequest(Request::METHOD_GET, $url);

        return new Collection($this->adminClient->getParsedResponse($request));
    }

    /**
     * Get resources url
     *
     * @return string
     */
    public function getResourcesUrl()
    {
        return $this->resourceServer->getResourceServerBaseUrl() . '/resource';
    }

    /**
     * Get resource url
     *
     * @param $id
     * @return string
     */
    public function getResourceUrl($id)
    {
        return $this->getResourcesUrl() . '/' . $id;
    }

    /**
     * Create resource
     *
     * @param Value $value
     * @return ResourceValue
     */
    public function create(Value $value)
    {
        $url = $this->getResourcesUrl();

        $request = $this->getAuthenticatedRequest(Request::METHOD_POST, $url, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => $value->toJson()
        ]);

        return ResourceValue::fromResponse($this->adminClient->getParsedResponse($request));
    }

    /**
     * update resource
     *
     * @param Value $value
     * @return bool
     */
    public function update(Value $value)
    {
        $url = $this->getResourceUrl($value->getId());

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

        return $responseCode === Response::HTTP_NO_CONTENT; //for some reason keycloak returns code 204 on update
    }

    /**
     * Delete resource
     *
     * @param Value $value
     * @return bool
     */
    public function delete(Value $value)
    {
        $url = $this->getResourceUrl($value->getId());

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
