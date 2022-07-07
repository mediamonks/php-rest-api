<?php

namespace MediaMonks\RestApi\Model;

use MediaMonks\RestApi\Response\PaginatedResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class ResponseModelFactory
{
    public function __construct(private ResponseModelInterface $responseModel)
    {

    }

    public function createFromContent(mixed $content): ResponseModelInterface
    {
        if ($content instanceof Response) {
            return $this->createFromResponse($content);
        }
        if ($content instanceof PaginatedResponseInterface) {
            return $this->createFromPaginatedResponse($content);
        }
        if ($content instanceof \Throwable) {
            return $this->createFromThrowable($content);
        }

        return $this->create()->setData($content);
    }

    public function createFromResponse(Response $response): ResponseModelInterface
    {
        return $this->create()->setResponse($response);
    }

    public function createFromPaginatedResponse(PaginatedResponseInterface $response): ResponseModelInterface
    {
        return $this->create()->setPagination($response);
    }

    public function createFromThrowable(\Throwable $throwable): ResponseModelInterface
    {
        return $this->create()->setThrowable($throwable);
    }

    private function create(): ResponseModelInterface
    {
        return clone $this->responseModel;
    }
}
