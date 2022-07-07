<?php

namespace MediaMonks\RestApi\Model;

use MediaMonks\RestApi\Response\PaginatedResponseInterface;
use Symfony\Component\HttpFoundation\Response;

interface ResponseModelInterface
{
    public function setResponse(Response $response): ResponseModelInterface;

    public function setPagination(PaginatedResponseInterface $paginatedResponse): ResponseModelInterface;

    public function setThrowable(\Throwable $throwable): ResponseModelInterface;

    public function setData(mixed $data): ResponseModelInterface;

    public function setReturnStatusCode(bool $returnStatusCode): ResponseModelInterface;

    public function setReturnStackTrace(bool $returnStackTrace): ResponseModelInterface;

    public function getStatusCode(): int;

    public function toArray(): array;
}
