<?php

namespace MediaMonks\RestApi\Serializer;

trait SerializerTrait
{
    public function supportsFormat(string $format): bool
    {
        return in_array($format, $this->getSupportedFormats());
    }
}
