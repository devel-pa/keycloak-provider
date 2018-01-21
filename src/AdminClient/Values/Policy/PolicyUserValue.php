<?php

namespace Keycloak\AdminClient\Values\Policy;

use Keycloak\AdminClient\Values\Value;
use Keycloak\AdminClient\Values\PolicyValue;

class PolicyUserValue extends Value
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $description;
    /**
     * @var string
     */
    protected $logic;
    /**
     * @var array
     */
    protected $users;

    /**
     * PolicyUserValue constructor.
     * @param null $id
     * @param $name
     * @param null $description
     * @param string $logic
     * @param $users
     */
    public function __construct(
        $id = null,
        $name,
        $description = null,
        $logic = PolicyValue::LOGIC_POSITIVE,
        $users
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->logic = $logic;
        $this->users = $users;
    }

    /**
     * Factory from raw data
     *
     * @param $attributes
     * @return static
     */
    public static function fromRaw($attributes)
    {
        $id = $attributes['id'] ?? null;
        $name = $attributes['name'] ?? null;
        $description = $attributes['description'] ?? null;
        $logic = $attributes['logic'] ?? null;
        $users = $attributes['users'] ?? null;

        return new static($id, $name, $description, $logic, $users);
    }

    /**
     * Factory fo create
     *
     * @param $name
     * @param null $description
     * @param string $logic
     * @param $users
     * @return static
     */
    public static function forCreate(
        $name,
        $description = null,
        $logic = PolicyValue::LOGIC_POSITIVE,
        $users
    ) {
        return new static(null, $name, $description, $logic, $users);
    }

    /**
     * Factory for new user policy
     *
     * @param PolicyUserValue $prevValue
     * @param null $name
     * @param null $description
     * @param null $logic
     * @param null $users
     * @return static
     */
    public static function forUpdate(
        PolicyUserValue $prevValue,
        $name = null,
        $description = null,
        $logic = null,
        $users = null
    ) {
        if (is_null($name)) {
            $name = $prevValue->getName();
        }

        if (is_null($description)) {
            $description = $prevValue->getDescription();
        }

        if (is_null($logic)) {
            $logic = $prevValue->getLogic();
        }

        if (is_null($users)) {
            $users = $prevValue->getUsers();
        }

        return new static($prevValue->getId(), $name, $description, $logic, $users);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getLogic()
    {
        return $this->logic;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Get id (not used here)
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Factory to create from policy object
     *
     * @param PolicyValue $policyValue
     * @return static
     */
    public static function fromPolicy(PolicyValue $policyValue)
    {
        $config = $policyValue->getConfig();

        $users = [];

        if (isset($config['users'])) {
            $users = json_decode($config['users']);
        }

        return new static($policyValue->getId(), $policyValue->getName(), $policyValue->getDescription(),
            $policyValue->getLogic(), $users);
    }

    /**
     * Convert to json
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
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => PolicyValue::TYPE_USER,
            'logic' => $this->logic,
            'users' => $this->users
        ], function ($item) {
            return !is_null($item);
        });
    }
}
