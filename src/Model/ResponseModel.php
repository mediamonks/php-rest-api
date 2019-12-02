<?php

namespace MediaMonks\RestApi\Model;

use MediaMonks\RestApi\Exception\ExceptionInterface;
use MediaMonks\RestApi\Response\Error;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ResponseModel extends AbstractResponseModel
    implements ResponseModelInterface
{

    const EXCEPTION_GENERAL = 'Exception';

    const EXCEPTION_HTTP = 'HttpException';

    /**
     * @return array
     */
    public function toArray()
    {
        $return = [];
        if ($this->getReturnStatusCode()) {
            $return['statusCode'] = $this->getStatusCode();
        }
        if (isset($this->throwable)) {
            $return['error'] = $this->throwableToArray();
        } elseif (isset($this->response)
            && $this->response instanceof RedirectResponse
        ) {
            $return['location'] = $this->response->headers->get('Location');
        } else {
            $return += $this->dataToArray();
        }

        return $return;
    }

    /**
     * @return array
     */
    protected function dataToArray()
    {
        $return = [];
        if (isset($this->data)) {
            $return['data'] = $this->data;
            if (isset($this->pagination)) {
                $return['pagination'] = $this->pagination->toArray();
            }
        }

        return $return;
    }

    /**
     * @return array
     */
    protected function throwableToArray()
    {
        if ($this->throwable instanceof ExceptionInterface) {
            $error = $this->throwable->toArray();
        } elseif ($this->throwable instanceof HttpException) {
            $error = $this->httpExceptionToArray();
        } else {
            $error = $this->generalThrowableToArray();
        }
        if ($this->isReturnStackTrace()) {
            $error['stack_trace'] = $this->getThrowableStackTrace();
        }

        return $error;
    }

    /**
     * @return array
     */
    protected function httpExceptionToArray()
    {
        return [
            'code' => $this->getThrowableErrorCode(
                Error::CODE_HTTP,
                self::EXCEPTION_HTTP
            ),
            'message' => $this->throwable->getMessage(),
        ];
    }

    /**
     * @return array
     */
    protected function generalThrowableToArray()
    {
        return [
            'code' => trim(
                $this->getThrowableErrorCode(
                    Error::CODE_GENERAL,
                    self::EXCEPTION_GENERAL
                ),
                '.'
            ),
            'message' => $this->throwable->getMessage(),
        ];
    }
}
