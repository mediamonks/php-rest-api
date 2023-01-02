<?php

namespace MediaMonks\RestApi\Serializer;

interface SerializerInterface
{
    public function serialize(mixed $data, string $format): ?string;

    public function getSupportedFormats(): array;

    public function supportsFormat(string $format): bool;

    public function getDefaultFormat(): string;
}
