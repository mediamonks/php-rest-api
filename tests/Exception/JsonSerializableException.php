<?php

namespace MediaMonks\RestApi\Tests\Exception;

use Exception;
use JsonSerializable;

class JsonSerializableException extends Exception implements JsonSerializable
{
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return ['code' => 0, 'message' => 'json_serialized_message', 'fields' => []];
    }
}