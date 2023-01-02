<?php

namespace MediaMonks\RestApi\Tests\Exception;

use MediaMonks\RestApi\Exception\ErrorField;
use MediaMonks\RestApi\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

class ValidationExceptionTest extends TestCase
{
    public function testSetFields()
    {
        $error = new ErrorField('foo', 'bar', 'foobar');
        $validationException = new ValidationException(
            [
                $error
            ]
        );

        $this->assertCount(1, $validationException->getFields());
        $this->assertEquals($error, $validationException->getFields()[0]);
    }

    public function testFailOnInvalidFields()
    {
        $this->expectException('InvalidArgumentException');
        new ValidationException(['foo']);
    }
}
