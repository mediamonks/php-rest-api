<?php

namespace MediaMonks\RestApi\Request;

class Format
{
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';
    const FORMAT_MSGPACK = 'msgpack';

    public static function getDefault(): string
    {
        return self::FORMAT_JSON;
    }

    public static function getAvailable(): array
    {
        return [self::FORMAT_JSON, self::FORMAT_XML];
    }
}
