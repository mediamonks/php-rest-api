<?php

namespace MediaMonks\RestApi\Serializer;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use MediaMonks\RestApi\Exception\SerializerException;

class ChainSerializer implements SerializerInterface
{
    use SerializerTrait;

    /**
     * @var SerializerInterface[]
     */
    private array $serializers = [];

    private array $formats = [];

    /**
     * @param SerializerInterface $serializer
     */
    public function addSerializer(SerializerInterface $serializer)
    {
        $this->serializers[] = $serializer;
        $this->formats = array_merge($this->formats, $serializer->getSupportedFormats());
    }

    /**
     * @throws SerializerException
     */
    public function serialize(mixed $data, string $format): string
    {
        $this->assertHasSerializer();

        foreach ($this->serializers as $serializer) {
            if ($serializer->supportsFormat($format)) {
                return $serializer->serialize($data, $format);
            }
        }

        throw new SerializerException(sprintf('No serializer found to support format "%s"', $format));
    }

    /**
     * @throws SerializerException
     */
    public function getSupportedFormats(): array
    {
        $this->assertHasSerializer();

        return $this->formats;
    }

    /**
     * @throws SerializerException
     */
    public function getDefaultFormat(): string
    {
        $this->assertHasSerializer();

        return $this->serializers[0]->getDefaultFormat();
    }

    /**
     * @throws SerializerException
     */
    private function assertHasSerializer()
    {
        if (count($this->serializers) === 0) {
            throw new SerializerException('No serializer was configured');
        }
    }
}
