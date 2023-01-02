<?php

namespace MediaMonks\RestApi\Tests\Request;

use MediaMonks\RestApi\Request\Format;
use PHPUnit\Framework\TestCase;

class FormatTest extends TestCase
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
