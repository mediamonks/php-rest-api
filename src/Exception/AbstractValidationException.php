<?php

namespace MediaMonks\RestApi\Exception;


abstract class AbstractValidationException extends AbstractException implements ExceptionInterface, FieldExceptionInterface
{
    public function __construct(protected $message, protected $code)
    {
        parent::__construct($this->message, $this->code);
    }

    public function toArray(): array
    {
        $return = [
            'code'    => $this->getCode(),
            'message' => $this->getMessage(),
        ];

        /** @var ExceptionInterface|array $field */
        foreach ($this->getFields() as $field) {
            $return['fields'][] = is_array($field) ? $field : $field->toArray();
        }

        return $return;
    }
}
