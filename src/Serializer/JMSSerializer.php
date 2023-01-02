<?php

namespace MediaMonks\RestApi\Serializer;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use MediaMonks\RestApi\Request\Format;

class JMSSerializer implements SerializerInterface
{
    use SerializerTrait;

    public function __construct(private Serializer $serializer, private ?SerializationContext $context = null)
    {

    }

    public function serialize(mixed $data, string $format): ?string
    {
        return $this->serializer->serialize($data, $format, $this->context);
    }

    public function getSupportedFormats(): array
    {
        return [Format::FORMAT_JSON, Format::FORMAT_XML];
    }

    public function getDefaultFormat(): string
    {
        return Format::FORMAT_JSON;
    }
}
