<?php

namespace MediaMonks\RestApi\Tests\Exception;

use Exception;
use JsonSerializable;

class JsonSerializableException extends Exception implements JsonSerializable
{
    public function jsonSerialize(): mixed
    {
        return ['code' => 0, 'message' => 'json_serialized_message', 'fields' => []];
    }
}