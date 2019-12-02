<?php

namespace MediaMonks\RestApi\Model;

use MediaMonks\RestApi\Response\PaginatedResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class ResponseModelFactory
{
    /**
     * @var ResponseModelInterface
     */
    private $responseModel;

    /**
     * @param ResponseModelInterface $responseModel
     */
    public function __construct(ResponseModelInterface $responseModel)
    {
        $this->responseModel = $responseModel;
    }

    /**
     * @param mixed $content
     * @return ResponseModelInterface
     */
    public function createFromContent($content)
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

    /**
     * @param Response $response
     * @return ResponseModelInterface
     */
    public function createFromResponse(Response $response)
    {
        return $this->create()->setResponse($response);
    }

    /**
     * @param PaginatedResponseInterface $response
     * @return ResponseModelInterface
     */
    public function createFromPaginatedResponse(PaginatedResponseInterface $response)
    {
        return $this->create()->setPagination($response);
    }

    /**
     * @param \Throwable $throwable
     *
     * @return ResponseModelInterface
     */
    public function createFromThrowable(\Throwable $throwable)
    {
        return $this->create()->setThrowable($throwable);
    }

    /**
     * @return ResponseModelInterface
     */
    private function create()
    {
        return clone $this->responseModel;
    }
}
