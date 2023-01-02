<?php

namespace MediaMonks\RestApi\Exception;

interface ExceptionInterface
{
    public function toArray(): array;
}
