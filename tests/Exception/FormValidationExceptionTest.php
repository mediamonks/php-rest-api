<?php

namespace MediaMonks\RestApi\Tests\Exception;

use MediaMonks\RestApi\Exception\FormValidationException;
use MediaMonks\RestApi\Response\Error;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;

class FormValidationExceptionTest extends TestCase
{
    public function testEmptyFormToArray()
    {
        $form = m::mock('Symfony\Component\Form\FormInterface');
        $form->shouldReceive('getErrors')->andReturn(new FormErrorIterator($form, []));
        $form->shouldReceive('all')->andReturn([]);

        $exception = new FormValidationException($form);

        $arrayException = $exception->toArray();

        $this->assertEquals(400, $arrayException['code']);
        $this->assertEquals('Not all fields are filled in correctly.', $arrayException['message']);
    }

    public function testEmptyFormToArrayCustom()
    {
        $form = m::mock('Symfony\Component\Form\FormInterface');
        $form->shouldReceive('getErrors')->andReturn(new FormErrorIterator($form, []));
        $form->shouldReceive('all')->andReturn([]);

        $exception = new FormValidationException($form, 'my_message', 500);

        $arrayException = $exception->toArray();

        $this->assertEquals(500, $arrayException['code']);
        $this->assertEquals('my_message', $arrayException['message']);
    }

    public function testToArray()
    {
        $form = m::mock('Symfony\Component\Form\FormInterface');
        $form->shouldReceive('isRoot')->andReturn(true);
        $form->shouldReceive('getErrors')->andReturn(new FormErrorIterator($form, [
            new FormError('General Error'),
            new FormError('CSRF Error'),
        ]));

        $childForm = m::mock('Symfony\Component\Form\FormInterface');
        $childForm->shouldReceive('isRoot')->andReturn(false);
        $childForm->shouldReceive('isValid')->andReturn(false);
        $childForm->shouldReceive('getName')->andReturn('name');
        $childForm->shouldReceive('all')->andReturn([]);
        $childForm->shouldReceive('getErrors')->andReturn(new FormErrorIterator($form, [
            new FormError('Constraint Failed Error', '', [], null, new ConstraintViolation(
                'Foo', '', [], false, 'name', null, null, null, new NotBlank()
            )),
            new FormError('Other Error'),
        ]));

        $form->shouldReceive('all')->andReturn([
            $childForm,
        ]);

        $exception = new FormValidationException($form);

        $arrayException = $exception->toArray();

        $this->assertEquals('#', $arrayException['fields'][0]['field']);
        $this->assertEquals(Error::ERROR_KEY_FORM_VALIDATION . '.general', $arrayException['fields'][0]['code']);
        $this->assertEquals('General Error', $arrayException['fields'][0]['message']);

        $this->assertEquals('#', $arrayException['fields'][1]['field']);
        $this->assertEquals('validation.csrf', $arrayException['fields'][1]['code']);
        $this->assertEquals('CSRF Error', $arrayException['fields'][1]['message']);

        $this->assertEquals('name', $arrayException['fields'][2]['field']);
        $this->assertEquals('validation.not_blank', $arrayException['fields'][2]['code']);
        $this->assertEquals('Constraint Failed Error', $arrayException['fields'][2]['message']);

        $this->assertEquals('name', $arrayException['fields'][3]['field']);
        $this->assertEquals('validation.general', $arrayException['fields'][3]['code']);
        $this->assertEquals('Other Error', $arrayException['fields'][3]['message']);
    }
}
