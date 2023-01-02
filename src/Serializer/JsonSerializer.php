<?php

namespace MediaMonks\RestApi\Serializer;

use MediaMonks\RestApi\Request\Format;

class JsonSerializer implements SerializerInterface
{
    use SerializerTrait;

    public function serialize(mixed $data, string $format): ?string
    {
        return json_encode($data);
    }

    public function getSupportedFormats(): array
    {
        return [Format::FORMAT_JSON];
    }

    public function getDefaultFormat(): string
    {
        return Format::FORMAT_JSON;
    }
}
