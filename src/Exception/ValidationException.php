<?php

namespace MediaMonks\RestApi\Exception;

use MediaMonks\RestApi\Response\Error;

class ValidationException extends AbstractValidationException
{
    protected array $fields;

    public function __construct(
        array $fields,
        protected $message = Error::MESSAGE_FORM_VALIDATION,
        protected $code = Error::CODE_FORM_VALIDATION
    ) {
        $this->setFields($fields);
        parent::__construct($message, $code);
    }

    public function setFields(array $fields)
    {
        foreach ($fields as $field) {
            if (!$field instanceof ErrorField) {
                throw new \InvalidArgumentException('Every field must be an instance of ErrorField');
            }
            $this->fields[] = $field;
        }
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
