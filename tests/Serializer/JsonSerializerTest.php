<?php

namespace MediaMonks\RestApi\Tests\Serializer;

use MediaMonks\RestApi\Serializer\JsonSerializer;
use PHPUnit\Framework\TestCase;

class JsonSerializerTest extends TestCase
{
    public function test_formats()
    {
        $serializer = new JsonSerializer();
        $this->assertIsArray($serializer->getSupportedFormats());
        $this->assertEquals(['json'], $serializer->getSupportedFormats());
        $this->assertIsString($serializer->getDefaultFormat());
        $this->assertEquals('json', $serializer->getDefaultFormat());
    }

    public function test_supports()
    {
        $serializer = new JsonSerializer();
        $this->assertTrue($serializer->supportsFormat('json'));
        $this->assertFalse($serializer->supportsFormat('xml'));
        $this->assertFalse($serializer->supportsFormat('msgpack'));
    }

    public function test_serialize()
    {
        $serializer = new JsonSerializer();
        $output = $serializer->serialize('foo', 'json');
        $this->assertEquals('"foo"', $output);
    }
}
