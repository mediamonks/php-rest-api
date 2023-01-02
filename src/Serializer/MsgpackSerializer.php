<?php

namespace MediaMonks\RestApi\Serializer;

use MediaMonks\RestApi\Request\Format;

class MsgpackSerializer implements SerializerInterface
{
    use SerializerTrait;

    public function serialize($data, $format): string
    {
        return msgpack_pack($data);
    }

    public function getSupportedFormats(): array
    {
        return [Format::FORMAT_MSGPACK];
    }

    public function getDefaultFormat(): string
    {
        return Format::FORMAT_MSGPACK;
    }
}
