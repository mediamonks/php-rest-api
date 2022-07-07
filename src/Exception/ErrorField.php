<?php

namespace MediaMonks\RestApi\Exception;

class ErrorField
{
    public function __construct(private string $field, private string $code, private string $message)
    {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function toArray(): array
    {
        return [
            'field' => $this->getField(),
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
        ];
    }
}
