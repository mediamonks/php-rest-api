<?php

namespace MediaMonks\RestApi\Serializer;

use MediaMonks\RestApi\Request\Format;

class MsgpackSerializer implements SerializerInterface
{
    use SerializerTrait;

    /**
     * @param $data
     * @param $format
     * @return mixed|string
     */
    public function serialize($data, $format)
    {
        return msgpack_pack($data);
    }

    /**
     * @return array
     */
    public function getSupportedFormats()
    {
        return [Format::FORMAT_MSGPACK];
    }

    /**
     * @return string
     */
    public function getDefaultFormat()
    {
        return Format::FORMAT_MSGPACK;
    }
}
