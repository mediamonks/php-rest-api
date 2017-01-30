<?php

namespace tests\MediaMonks\RestApi\Exception;

use MediaMonks\RestApi\Exception\FormValidationException;
use Mockery as m;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use tests\MediaMonks\RestApi\Form\Type\TestType;

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

    public function testEmptyFormToArrayCustom()
    {
        $form = m::mock('Symfony\Component\Form\FormInterface');
        $form->shouldReceive('getErrors')->andReturn([]);
        $form->shouldReceive('all')->andReturn([]);

        $exception = new FormValidationException($form, 'my_message', 'my_code');

        $arrayException = $exception->toArray();

        $this->assertEquals('my_code', $arrayException['code']);
        $this->assertEquals('my_message', $arrayException['message']);
    }

    public function testToArray()
    {
        $form = m::mock(FormInterface::class);
        $form->shouldReceive('isRoot')->andReturn(true);
        $form->shouldReceive('getErrors')->andReturn(new FormErrorIterator($form, [
            new FormError('Some General Error', '', [])
        ]));

        $childForm = m::mock(FormInterface::class);
        $childForm->shouldReceive('isRoot')->andReturn(false);
        $childForm->shouldReceive('isValid')->andReturn(false);
        $childForm->shouldReceive('getName')->andReturn('name');
        $childForm->shouldReceive('all')->andReturn([]);
        $childForm->shouldReceive('getErrors')->andReturn(new FormErrorIterator($form, [
            new FormError('Some Field Error', '', [])
        ]));

        $form->shouldReceive('all')->andReturn([
            $childForm
        ]);

        $exception = new FormValidationException($form);

        $arrayException = $exception->toArray();

        $this->assertEquals('#', $arrayException['fields'][0]['field']);
        $this->assertEquals('error.form.validation.general', $arrayException['fields'][0]['code']);
        $this->assertEquals('Some General Error', $arrayException['fields'][0]['message']);

        $this->assertEquals('name', $arrayException['fields'][1]['field']);
        $this->assertEquals('error.form.validation.general', $arrayException['fields'][1]['code']);
        $this->assertEquals('Some Field Error', $arrayException['fields'][1]['message']);
    }
}
