<?php

namespace Keycloak\AdminClient\Values\Policy;

use Keycloak\AdminClient\Values\PolicyValue;

class PolicyValueFactory
{
    /**
     * Factory to create policy types instances
     *
     * @param PolicyValue $policyValue
     * @return PolicyUserValue
     */
    public static function make(PolicyValue $policyValue)
    {
        if ($policyValue->getType() === PolicyValue::TYPE_USER) {
            return PolicyUserValue::fromPolicy($policyValue);
        }

        return null;
    }
}
