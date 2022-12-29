<?php

namespace MediaMonks\RestApi\Model;

use JsonSerializable;
use MediaMonks\RestApi\Exception\ExceptionInterface;
use MediaMonks\RestApi\Response\Error;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ResponseModel extends AbstractResponseModel implements ResponseModelInterface
{

    const EXCEPTION_GENERAL = 'Exception';

    const EXCEPTION_HTTP = 'HttpException';

    public function toArray(): array
    {
        $return = [];
        if ($this->getReturnStatusCode()) {
            $return['statusCode'] = $this->getStatusCode();
        }

        if (isset($this->throwable)) {
            $return['error'] = $this->throwableToArray();
        } elseif (isset($this->response) && $this->response instanceof RedirectResponse) {
            $return['location'] = $this->response->headers->get('Location');
        } else {
            $return += $this->dataToArray();
        }

        return $return;
    }

    protected function dataToArray(): array
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

    protected function throwableToArray(): array
    {
        if ($this->throwable instanceof ExceptionInterface) {
            $error = $this->throwable->toArray();
        } elseif ($this->throwable instanceof HttpException) {
            $error = $this->httpExceptionToArray();
        } elseif ($this->throwable instanceof JsonSerializable) {
            $error = $this->throwable->jsonSerialize();
        } else {
            $error = $this->generalThrowableToArray();
        }

        if ($this->isReturnStackTrace()) {
            $error['stack_trace'] = $this->getThrowableStackTrace();
        }

        return $error;
    }

    protected function httpExceptionToArray(): array
    {
        return [
            'code' => $this->getThrowableErrorCode(
                Error::ERROR_KEY_HTTP,
                self::EXCEPTION_HTTP
            ),
            'message' => $this->throwable->getMessage(),
        ];
    }

    protected function generalThrowableToArray(): array
    {
        return [
            'code' => trim(
                $this->getThrowableErrorCode(
                    Error::ERROR_KEY_GENERAL,
                    self::EXCEPTION_GENERAL
                ),
                '.'
            ),
            'message' => $this->throwable->getMessage(),
        ];
    }
}
