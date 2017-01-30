<?php

namespace tests\MediaMonks\RestApi\Request;

use MediaMonks\RestApi\Request\Format;

class FormatTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultFormatIsJson()
    {
        $this->assertEquals('json', Format::getDefault());
    }

    public function testAvailableFormats()
    {
        $availableFormats = Format::getAvailable();
        $this->assertCount(2, $availableFormats);
        $this->assertContains('json', $availableFormats);
        $this->assertContains('xml', $availableFormats);
    }
}
