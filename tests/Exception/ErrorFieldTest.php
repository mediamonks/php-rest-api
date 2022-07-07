<?php

namespace MediaMonks\RestApi\Tests\Exception;

use MediaMonks\RestApi\Exception\ErrorField;
use PHPUnit\Framework\TestCase;

class ErrorFieldTest extends TestCase
{
    public function testSetFields()
    {
        $errorField = new ErrorField('field', 'code', 'message');
        $this->assertEquals('field', $errorField->getField());
        $this->assertEquals('code', $errorField->getCode());
        $this->assertEquals('message', $errorField->getMessage());
        $this->assertEquals([
            'field' => 'field',
            'code' => 'code',
            'message' => 'message'
        ], $errorField->toArray());
    }
}
