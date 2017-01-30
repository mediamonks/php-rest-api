<?php

namespace MediaMonks\RestApi\Serializer;

interface SerializerInterface
{
    public function serialize($data, $format);

    public function getSupportedFormats();

    public function supportsFormat($format);

    public function getDefaultFormat();
}
