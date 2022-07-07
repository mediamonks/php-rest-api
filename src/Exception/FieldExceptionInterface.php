<?php

namespace MediaMonks\RestApi\Exception;

interface FieldExceptionInterface
{
    public function getFields(): array;
}
