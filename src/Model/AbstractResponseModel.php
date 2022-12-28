<?php

namespace MediaMonks\RestApi\Model;

use MediaMonks\RestApi\Exception\AbstractValidationException;
use MediaMonks\RestApi\Response\ExtendedResponseInterface;
use MediaMonks\RestApi\Response\PaginatedResponseInterface;
use MediaMonks\RestApi\Util\StringUtil;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class AbstractResponseModel
{
    protected int $statusCode = Response::HTTP_OK;

    protected bool $returnStatusCode = false;

    protected bool $returnStackTrace = false;

    protected mixed $data = null;

    protected ?Response $response = null;

    protected ?ExtendedResponseInterface $extendedResponse = null;

    protected ?\Throwable $throwable = null;

    protected ?PaginatedResponseInterface $pagination = null;

    public function isReturnStackTrace(): bool
    {
        return $this->returnStackTrace;
    }

    public function setReturnStackTrace(bool $returnStackTrace): ResponseModelInterface
    {
        $this->returnStackTrace = $returnStackTrace;

        return $this;
    }

    public function getStatusCode(): int
    {
        if (isset($this->throwable)) {
            return $this->getExceptionStatusCode();
        }

        if ($this->isEmpty()) {
            return Response::HTTP_NO_CONTENT;
        }

        return $this->statusCode;
    }

    protected function getExceptionStatusCode(): int
    {
        if ($this->throwable instanceof HttpException) {
            return $this->throwable->getStatusCode();
        }
        if ($this->throwable instanceof AbstractValidationException) {
            return Response::HTTP_BAD_REQUEST;
        }
        if ($this->isValidHttpStatusCode($this->throwable->getCode())) {
            return $this->throwable->getCode();
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    protected function isValidHttpStatusCode(int $code): bool
    {
        return array_key_exists($code, Response::$statusTexts) && $code >= Response::HTTP_BAD_REQUEST;
    }

    protected function getThrowableErrorCode(string $errorCode, ?string $trim = null): string
    {
        return sprintf($errorCode, StringUtil::classToSnakeCase($this->throwable, $trim));
    }

    protected function getThrowableStackTrace(): array
    {
        $traces = [];
        foreach ($this->throwable->getTrace() as $trace) {
            // Since PHP 7.4 the args key got disabled, to enable it again:
            // zend.exception_ignore_args = On
            if (array_key_exists('args', $trace)) {
                $trace['args'] = json_decode(json_encode($trace['args']), true);
            }

            $traces[] = $trace;
        }

        return $traces;
    }

    public function setStatusCode(int $statusCode): ResponseModelInterface
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getReturnStatusCode(): bool
    {
        return $this->returnStatusCode;
    }

    public function setReturnStatusCode(bool $returnStatusCode): ResponseModelInterface
    {
        $this->returnStatusCode = $returnStatusCode;

        return $this;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData(mixed $data): ResponseModelInterface
    {
        $this->data = $data;

        return $this;
    }

    public function getThrowable(): ?\Throwable
    {
        return $this->throwable;
    }

    public function setThrowable(\Throwable $throwable): ResponseModelInterface
    {
        $this->throwable = $throwable;

        return $this;
    }

    public function getPagination(): ?PaginatedResponseInterface
    {
        return $this->pagination;
    }

    public function setPagination(PaginatedResponseInterface $pagination): ResponseModelInterface
    {
        $this->pagination = $pagination;
        $this->setData($pagination->getData());

        return $this;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): ResponseModelInterface
    {
        $this->response = $response;
        $this->setStatusCode($response->getStatusCode());
        $this->setData($response->getContent());

        return $this;
    }

    /**
     * @return ExtendedResponseInterface
     */
    public function getExtendedResponse()
    {
        return $this->extendedResponse;
    }

    /**
     * @param ExtendedResponseInterface $response
     * @return $this
     */
    public function setExtendedResponse(ExtendedResponseInterface $response): ResponseModelInterface
    {
        $this->extendedResponse = $response;
        $this->setStatusCode($response->getStatusCode());
        $this->setData($response->getCustomContent());

        return $this;
    }

    public function isEmpty(): bool
    {
        return (
            !isset($this->throwable)
            && is_null($this->data)
            && !isset($this->pagination)
            && $this->isEmptyResponse()
            && $this->isEmptyExtendedResponse()
        );
    }

    protected function isEmptyResponse(): bool
    {
        return (!isset($this->response) || $this->response->isEmpty());
    }

    protected function isEmptyExtendedResponse(): bool
    {
        return (!isset($this->extendedResponse) || $this->extendedResponse->isEmpty());
    }

    // @codeCoverageIgnoreStart

    /**
     * This is called when an exception is thrown during the response transformation
     */
    public function __toString(): string
    {
        return json_encode(get_object_vars($this));
    }
    // @codeCoverageIgnoreEnd
}
