<?php

namespace MediaMonks\RestApi\Model;

use MediaMonks\RestApi\Exception\AbstractValidationException;
use MediaMonks\RestApi\Response\PaginatedResponseInterface;
use MediaMonks\RestApi\Util\StringUtil;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class AbstractResponseModel
{
    /**
     * @var int
     */
    protected $statusCode = Response::HTTP_OK;

    /**
     * @var bool
     */
    protected $returnStatusCode = false;

    /**
     * @var bool
     */
    protected $returnStackTrace = false;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var \Throwable
     */
    protected $throwable;

    /**
     * @var PaginatedResponseInterface
     */
    protected $pagination;

    /**
     * @return boolean
     */
    public function isReturnStackTrace()
    {
        return $this->returnStackTrace;
    }

    /**
     * @param boolean $returnStackTrace
     * @return $this
     */
    public function setReturnStackTrace($returnStackTrace)
    {
        $this->returnStackTrace = $returnStackTrace;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        if (isset($this->throwable)) {
            return $this->getExceptionStatusCode();
        }
        if ($this->isEmpty()) {
            return Response::HTTP_NO_CONTENT;
        }

        return $this->statusCode;
    }

    /**
     * @return int
     */
    protected function getExceptionStatusCode()
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

    /**
     * @param int $code
     * @return bool
     */
    protected function isValidHttpStatusCode($code)
    {
        return array_key_exists($code, Response::$statusTexts) && $code >= Response::HTTP_BAD_REQUEST;
    }

    /**
     * @param string $errorCode
     * @param string $trim
     * @return string
     */
    protected function getThrowableErrorCode($errorCode, $trim = null)
    {
        return sprintf($errorCode, StringUtil::classToSnakeCase($this->throwable, $trim));
    }

    /**
     * @return string
     */
    protected function getThrowableStackTrace()
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

    /**
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return bool
     */
    public function getReturnStatusCode()
    {
        return $this->returnStatusCode;
    }

    /**
     * @param bool $returnStatusCode
     * @return $this
     */
    public function setReturnStatusCode($returnStatusCode)
    {
        $this->returnStatusCode = $returnStatusCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return \Exception
     */
    public function getThrowable()
    {
        return $this->throwable;
    }

    /**
     * @param \Throwable $throwable
     *
     * @return $this
     */
    public function setThrowable(\Throwable $throwable)
    {
        $this->throwable = $throwable;

        return $this;
    }

    /**
     * @return PaginatedResponseInterface
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @param PaginatedResponseInterface $pagination
     * @return $this
     */
    public function setPagination(PaginatedResponseInterface $pagination)
    {
        $this->pagination = $pagination;
        $this->setData($pagination->getData());

        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return $this
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        $this->setStatusCode($response->getStatusCode());
        $this->setData($response->getContent());

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return (
            !isset($this->throwable)
            && is_null($this->data)
            && !isset($this->pagination)
            && $this->isEmptyResponse()
        );
    }

    /**
     * @return bool
     */
    protected function isEmptyResponse()
    {
        return !isset($this->response) || $this->response->isEmpty();
    }

    // @codeCoverageIgnoreStart

    /**
     * This is called when an exception is thrown during the response transformation
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(get_object_vars($this));
    }
    // @codeCoverageIgnoreEnd
}
