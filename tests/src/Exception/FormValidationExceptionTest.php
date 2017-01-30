<?php

namespace tests\MediaMonks\RestApi\Exception;

use MediaMonks\RestApi\Exception\FormValidationException;
use Mockery as m;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class FormValidationExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyFormToArray()
    {
        $form = m::mock('Symfony\Component\Form\FormInterface');
        $form->shouldReceive('getErrors')->andReturn([]);
        $form->shouldReceive('all')->andReturn([]);

        $exception = new FormValidationException($form);

        $arrayException = $exception->toArray();

        $this->assertEquals('error.form.validation', $arrayException['code']);
        $this->assertEquals('Not all fields are filled in correctly.', $arrayException['message']);
    }
}
