<?php

namespace
{
    if (!function_exists('msgpack_pack')) {
        function msgpack_pack($value)
        {
            // From https://github.com/rybakit/msgpack.php/blob/master/tests/Unit/MessagePackTest.php:24
            return "\x91\x01";
        }
    }
}

namespace MediaMonks\RestApi\Tests\Serializer
{

    use MediaMonks\RestApi\Serializer\MsgpackSerializer;
    use PHPUnit\Framework\TestCase;

    class MsgpackSerializerTest extends TestCase
    {
        public function test_formats()
        {
            $serializer = new MsgpackSerializer();
            $this->assertIsArray($serializer->getSupportedFormats());
            $this->assertEquals(['msgpack'], $serializer->getSupportedFormats());
            $this->assertIsString($serializer->getDefaultFormat());
            $this->assertEquals('msgpack', $serializer->getDefaultFormat());
        }

        public function test_supports()
        {
            $serializer = new MsgpackSerializer();
            $this->assertFalse($serializer->supportsFormat('json'));
            $this->assertFalse($serializer->supportsFormat('xml'));
            $this->assertTrue($serializer->supportsFormat('msgpack'));
        }

        public function test_serialize()
        {
            $serializer = new MsgpackSerializer();
            $output = $serializer->serialize([0 => 1], 'msgpack');
            $this->assertEquals("\x91\x01", $output);
        }
    }
}