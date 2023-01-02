<?php

namespace MediaMonks\RestApi\Tests\Model;

use MediaMonks\RestApi\Model\ResponseModel;
use MediaMonks\RestApi\Response\OffsetPaginatedResponse;
use MediaMonks\RestApi\Response\Response as ExtendedResponse;
use MediaMonks\RestApi\Tests\Exception\JsonSerializableException;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseModelTest extends TestCase
{
    public function testDataGettersSetter()
    {
        $data = ['foo', 'bar'];
        $responseContainer = new ResponseModel();
        $responseContainer->setData($data);
        $this->assertEquals($data, $responseContainer->getData());
    }

    public function testExceptionGettersSetter()
    {
        $exception = new \Exception;
        $responseContainer = new ResponseModel();
        $responseContainer->setThrowable($exception);
        $this->assertEquals($exception, $responseContainer->getThrowable());
    }

    public function testLocationGettersSetter()
    {
        $location = 'http://www.mediamonks.com';
        $redirect = new RedirectResponse($location);
        $responseContainer = new ResponseModel();
        $responseContainer->setResponse($redirect);
        $this->assertEquals($redirect, $responseContainer->getResponse());
    }

    public function testExtendedResponsGetterSetter()
    {
        $response = new ExtendedResponse('OK');
        $responseContainer = new ResponseModel();
        $responseContainer->setExtendedResponse($response);
        $this->assertEquals($response, $responseContainer->getExtendedResponse());
    }

    public function testPaginationGettersSetter()
    {
        $pagination = new OffsetPaginatedResponse('foo', 1, 2, 3);
        $responseContainer = new ResponseModel();
        $responseContainer->setPagination($pagination);
        $this->assertEquals($pagination, $responseContainer->getPagination());
    }

    public function testReturnStatusCodeGetterSetter()
    {
        $statusCode = Response::HTTP_NOT_MODIFIED;
        $responseContainer = new ResponseModel();
        $responseContainer->setReturnStatusCode($statusCode);
        $this->assertEquals($statusCode, $responseContainer->getReturnStatusCode());
    }

    public function testStatusCodeGetterSetter()
    {
        $statusCode = Response::HTTP_OK;
        $responseContainer = new ResponseModel();
        $responseContainer->setData('OK');
        $responseContainer->setStatusCode($statusCode);
        $this->assertEquals($statusCode, $responseContainer->getStatusCode());
    }

    public function testGetCodeFromStatusCode()
    {
        $statusCode = Response::HTTP_BAD_REQUEST;
        $code = 400;
        $exception = new \Exception('', $code);

        $responseContainer = new ResponseModel();
        $responseContainer->setStatusCode($statusCode);
        $responseContainer->setThrowable($exception);

        $this->assertEquals($code, $responseContainer->getStatusCode());
    }

    public function testToArrayStatusCode()
    {
        $responseContainer = new ResponseModel();
        $responseContainer->setData('foo');
        $responseContainer->setReturnStatusCode(true);

        $result = $responseContainer->toArray();
        $this->assertEquals(Response::HTTP_OK, $result['statusCode']);
    }

    public function testJsonSerializableException()
    {
        $error = ['code' => 0, 'message' => 'json_serialized_message', 'fields' => []];

        $responseContainer = new ResponseModel();
        $responseContainer->setThrowable(new JsonSerializableException());

        $this->assertEquals(['error' => $error], $responseContainer->toArray());
    }

    public function testValidationExceptionToArrayFormValidationException()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('This test fails on HHVM, see issue #8');
        }

        $error = ['code' => 0, 'message' => '', 'fields' => null];

        $mockException = m::mock('\MediaMonks\RestApi\Exception\ValidationException, \MediaMonks\RestApi\Exception\ExceptionInterface');
        $mockException->shouldReceive('toArray')->andReturn($error);
        $mockException->shouldReceive('getFields');

        $responseContainer = new ResponseModel();
        $responseContainer->setThrowable($mockException);

        $this->assertEquals(['error' => $error], $responseContainer->toArray());
    }

    public function testReturnStackTraceEnabled()
    {
        $responseContainer = new ResponseModel();
        $responseContainer->setThrowable(new \Exception('Test'));
        $responseContainer->setReturnStackTrace(true);

        $this->assertTrue($responseContainer->isReturnStackTrace());

        $data = $responseContainer->toArray();
        $this->assertArrayHasKey('error', $data);
        $this->assertArrayHasKey('stack_trace', $data['error']);
    }

    public function testReturnStackTraceDisabled()
    {
        $responseContainer = new ResponseModel();
        $responseContainer->setThrowable(new \Exception('Test'));
        $responseContainer->setReturnStackTrace(false);

        $this->assertFalse($responseContainer->isReturnStackTrace());

        $data = $responseContainer->toArray();
        $this->assertArrayHasKey('error', $data);
        $this->assertArrayNotHasKey('stack_trace', $data['error']);
    }
}
