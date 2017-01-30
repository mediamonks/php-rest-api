<?php

namespace tests\MediaMonks\RestApi\Exception;

use MediaMonks\RestApi\Exception\FormValidationException;
use Mockery as m;
use Symfony\Component\Form\FormInterface;

class FormValidationExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyForm()
    {
        $form = m::mock(FormInterface::class);
        $form->shouldReceive('getErrors')->andReturn([]);
        $form->shouldReceive('all')->andReturn([]);

        $exception = new FormValidationException($form);

        $arrayException = $exception->toArray();

        $this->assertEquals('error.form.validation', $arrayException['code']);
        $this->assertEquals('Not all fields are filled in correctly.', $arrayException['message']);
    }
}
