<?php

namespace Keycloak\AdminClient;

use Keycloak\AdminClient\Values\Value;

interface Entity
{

    public function find($id = null);

    public function create(Value $value);

    public function update(Value $value);

    public function delete(Value $value);
}
