<?php

namespace tests\MediaMonks\RestApi\Exception;

use MediaMonks\RestApi\Exception\ErrorField;

class ErrorFieldTest extends \PHPUnit_Framework_TestCase
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
