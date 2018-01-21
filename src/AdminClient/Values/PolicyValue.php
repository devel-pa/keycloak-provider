<?php

namespace Keycloak\AdminClient\Values;

use Keycloak\AdminClient\Values\Policy\PolicyValueFactory;

class PolicyValue extends Value
{
    const DECISION_STRATEGY_UNANIMOUS = 'UNANIMOUS';

    const LOGIC_POSITIVE = 'POSITIVE';
    const LOGIC_NEGATIVE = 'NEGATIVE';

    const TYPE_USER = 'user';
    const TYPE_ROLE = 'role';
    /**
     * Policy uuid
     * @var string
     */
    protected $id;
    /**
     * Policy name
     * @var string
     */
    protected $name;
    /**
     * Policy description
     * @var string
     */
    protected $description;
    /**
     * Policy type (user, role, aggregate, etc)
     * @var string
     */
    protected $type;
    /**
     * Type logic of policy [POSITIVE, NEGATIVE]
     * @var null
     */
    protected $logic;
    /**
     * Decision strategy. For policy always UNANIMOUS
     * @var null
     */
    protected $decisionStrategy;
    /**
     * Config, contain data depends on type of policy
     * @var null
     */
    protected $config;

    /**
     * PolicyValue constructor.
     * @param $id
     * @param $name
     * @param $description
     * @param $type
     * @param $logic
     * @param $decisionStrategy
     * @param $config
     */
    public function __construct(
        $id = null,
        $name = null,
        $description = null,
        $type = null,
        $logic = null,
        $decisionStrategy = null,
        $config = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->logic = $logic;
        $this->decisionStrategy = $decisionStrategy;
        $this->config = $config;
    }

    /**
     * Factory for new value
     * @param $name
     * @param null $description
     * @param null $type
     * @param string $logic
     * @param null $config
     * @return static
     */
    public static function forCreate(
        $name,
        $description = null,
        $type = null,
        $logic = self::LOGIC_POSITIVE,
        $config = null
    ) {
        return new static(null, $name, $description, $type, $logic, self::DECISION_STRATEGY_UNANIMOUS, $config);
    }

    /**
     * Create value from response
     *
     * @param $response
     * @return static
     */
    public static function fromResponse($response)
    {
        return self::fromRaw($response);
    }

    /**
     * Create instance from raw data which comes from server
     *
     * @param $attributes
     * @return static
     */
    public static function fromRaw($attributes)
    {
        $list = ['id', 'name', 'description', 'type', 'logic', 'decisionStrategy', 'config'];

        $parameters = [];

        foreach ($list as $key) {
            $parameter = null;

            if (isset($attributes[$key])) {
                $parameter = $attributes[$key];
            }

            $parameters[] = $parameter;
        }
        //TODO refactor that, because of bad readability
        return new static(...$parameters);
    }

    /**
     * Convert value to json
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert value to array
     *
     * @return array
     */
    public function toArray()
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'logic' => $this->logic,
            'decisionStrategy' => $this->decisionStrategy,
            'config' => $this->config
        ], function ($item) {
            return !is_null($item); // return only not null
        });
    }

    /**
     * Get the id of value
     * @return null|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get type
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get logic
     *
     * @return string
     */
    public function getLogic()
    {
        return $this->logic;
    }

    /**
     * Get decision strategy
     *
     * @return string
     */
    public function getDecisionStrategy()
    {
        return $this->decisionStrategy;
    }

    /**
     * Config data of policy
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the policy instance based on config
     *
     * @return Policy\PolicyUserValue
     */
    public function getPolicy()
    {
        return PolicyValueFactory::make($this);
    }
}
