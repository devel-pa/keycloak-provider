<?php

namespace Keycloak\AdminClient\Values;


class ResourceValue extends Value
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $uri;
    /**
     * @var string
     */
    protected $type;
    /**
     * @var OwnerValue
     */
    protected $owner;
    /**
     * @var string
     */
    protected $_id;
    /**
     * @var ScopesValue
     */
    protected $scopes;

    /**
     * ResourceValue constructor.
     * @param $_id
     * @param $name
     * @param $uri
     * @param $type
     * @param OwnerValue $owner
     * @param ScopesValue $scopes
     */
    public function __construct(
        $_id = null,
        $name = null,
        $uri = null,
        $type = null,
        OwnerValue $owner = null,
        ScopesValue $scopes = null
    ) {
        $this->_id = $_id;
        $this->name = $name;
        $this->uri = $uri;
        $this->type = $type;
        $this->owner = $owner;
        $this->scopes = $scopes;
    }

    /**
     * Factory for new value
     *
     * @param $name
     * @param null $uri
     * @param null $type
     * @param OwnerValue|null $owner
     * @param ScopesValue|null $scopes
     * @return static
     */
    public static function forCreate(
        $name,
        $uri = null,
        $type = null,
        OwnerValue $owner = null,
        ScopesValue $scopes = null
    ) {
        return new static(null, $name, $uri, $type, $owner, $scopes);
    }

    /**
     * Factory for update
     *
     * @param ResourceValue $prevValue
     * @param $name
     * @param null $uri
     * @param null $type
     * @param OwnerValue|null $owner
     * @param ScopesValue|null $scopes
     * @return static
     */
    public static function forUpdate(
        ResourceValue $prevValue,
        $name,
        $uri = null,
        $type = null,
        OwnerValue $owner = null,
        ScopesValue $scopes = null
    ) {
        if (is_null($uri)) {
            $uri = $prevValue->getUri();
        }

        if (is_null($uri)) {
            $uri = $prevValue->getUri();
        }

        if (is_null($type)) {
            $type = $prevValue->getType();
        }

        if (is_null($owner)) {
            $owner = $prevValue->getOwner();
        }

        if (is_null($scopes)) {
            $scopes = $prevValue->getScopes();
        }

        return new static($prevValue->getId(), $name, $uri, $type, $owner, $scopes);
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return OwnerValue
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return ScopesValue
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * Get value id
     * @return null|string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Factory for delete value
     *
     * @param $_id
     * @return static
     */
    public static function forDelete($_id)
    {
        return new static($_id);
    }

    /**
     * Create value from response
     *
     * @param $response
     * @return static
     */
    public static function fromResponse($response)
    {
        return new static($response['_id']);
    }

    /**
     * Convert to Json
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray()
    {
        return array_filter([
            '_id' => $this->_id,
            'name' => $this->name,
            'uri' => $this->uri,
            'type' => $this->type,
            'owner' => is_null($this->owner) ? null : $this->owner->toArray(),
            'scopes' => is_null($this->scopes) ? null : $this->scopes->toArray()
        ], function ($item) {
            return !is_null($item);
        });
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
