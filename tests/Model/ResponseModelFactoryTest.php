<?php

namespace MediaMonks\RestApi\Tests\Model;

use MediaMonks\RestApi\Exception\ValidationException;
use MediaMonks\RestApi\Model\ResponseModel;
use MediaMonks\RestApi\Model\ResponseModelFactory;
use MediaMonks\RestApi\Response\OffsetPaginatedResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResponseModelFactoryTest extends TestCase
{
    public function testAutoDetectException()
    {
        $exception = new \Exception('foo');
        $responseContainer = $this->createResponseModel($exception);

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $responseContainer->getStatusCode());
        $this->assertNull($responseContainer->getData());
        $this->assertEquals($exception, $responseContainer->getThrowable());
        $this->assertNull($responseContainer->getResponse());
        $this->assertNull($responseContainer->getExtendedResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());

        $responseContainerArray = $responseContainer->toArray();

        $this->assertArrayHasKey('error', $responseContainerArray);
        $this->assertEquals('error', $responseContainerArray['error']['code']);
        $this->assertEquals('foo', $responseContainerArray['error']['message']);
    }

    public function testAutoDetectHttpException()
    {
        $notFoundHttpException = new NotFoundHttpException('foo');
        $responseContainer = $this->createResponseModel($notFoundHttpException);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $responseContainer->getStatusCode());
        $this->assertNull($responseContainer->getData());
        $this->assertEquals($notFoundHttpException, $responseContainer->getThrowable());
        $this->assertNull($responseContainer->getResponse());
        $this->assertNull($responseContainer->getExtendedResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());

        $responseContainerArray = $responseContainer->toArray();

        $this->assertArrayHasKey('error', $responseContainerArray);
        $this->assertEquals($responseContainerArray['error']['code'], 'error.not_found');
        $this->assertEquals($responseContainerArray['error']['message'], 'foo');
    }

    public function testAutoDetectPaginatedResponse()
    {
        $paginatedResponse = new OffsetPaginatedResponse('foo', 1, 2, 3);
        $responseContainer = $this->createResponseModel($paginatedResponse);

        $this->assertEquals(Response::HTTP_OK, $responseContainer->getStatusCode());
        $this->assertIsString($responseContainer->getData());
        $this->assertNull($responseContainer->getThrowable());
        $this->assertNull($responseContainer->getResponse());
        $this->assertNull($responseContainer->getExtendedResponse());
        $this->assertEquals($paginatedResponse, $responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());

        $responseContainerArray = $responseContainer->toArray();
        $this->assertArrayHasKey('data', $responseContainerArray);
        $this->assertArrayHasKey('pagination', $responseContainerArray);
    }

    public function testAutoDetectEmptyResponse()
    {
        $responseContainer = $this->createResponseModel(null);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $responseContainer->getStatusCode());
        $this->assertNull($responseContainer->getData());
        $this->assertNull($responseContainer->getThrowable());
        $this->assertNull($responseContainer->getResponse());
        $this->assertNull($responseContainer->getExtendedResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertTrue($responseContainer->isEmpty());
    }

    public function testAutoDetectEmptyExtendedResponse()
    {
        $responseContainer = $this->createResponseModel(null);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $responseContainer->getStatusCode());
        $this->assertNull($responseContainer->getData());
        $this->assertNull($responseContainer->getThrowable());
        $this->assertNull($responseContainer->getResponse());
        $this->assertNull($responseContainer->getExtendedResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertTrue($responseContainer->isEmpty());
    }

    public function testAutoDetectStringResponse()
    {
        $stringData = 'foo';
        $responseContainer = $this->createResponseModel($stringData);

        $this->assertEquals(Response::HTTP_OK, $responseContainer->getStatusCode());
        $this->assertIsString($responseContainer->getData());
        $this->assertNull($responseContainer->getThrowable());
        $this->assertNull($responseContainer->getResponse());
        $this->assertNull($responseContainer->getExtendedResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());
    }

    public function testAutoDetectArrayResponse()
    {
        $arrayData = ['foo', 'bar'];
        $responseContainer = $this->createResponseModel($arrayData);

        $this->assertEquals(Response::HTTP_OK, $responseContainer->getStatusCode());
        $this->assertIsArray($responseContainer->getData());
        $this->assertNull($responseContainer->getThrowable());
        $this->assertNull($responseContainer->getResponse());
        $this->assertNull($responseContainer->getExtendedResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());
    }

    public function testAutoDetectRedirectResponse()
    {
        $uri = 'http://www.mediamonks.com';
        $redirect = new RedirectResponse($uri, Response::HTTP_MOVED_PERMANENTLY);
        $responseContainer = $this->createResponseModel($redirect);

        $this->assertEquals(Response::HTTP_MOVED_PERMANENTLY, $responseContainer->getStatusCode());
        $this->assertNull($responseContainer->getThrowable());
        $this->assertEquals($redirect, $responseContainer->getResponse());
        $this->assertNull($responseContainer->getExtendedResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());

        $data = $responseContainer->toArray();

        $this->assertEquals($uri, $data['location']);
    }

    public function testAutoDetectSymfonyResponse()
    {
        $data = 'foo';
        $response = new Response($data);
        $responseContainer = $this->createResponseModel($response);

        $this->assertEquals(Response::HTTP_OK, $responseContainer->getStatusCode());
        $this->assertEquals($data, $responseContainer->getData());
        $this->assertNull($responseContainer->getThrowable());
        $this->assertEquals($response, $responseContainer->getResponse());
        $this->assertNull($responseContainer->getExtendedResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());
    }

    public function testAutoDetectMediaMonksResponse()
    {
        $data = 'foo';
        $response = new \MediaMonks\RestApi\Response\Response($data);
        $responseContainer = $this->createResponseModel($response);

        $this->assertEquals(Response::HTTP_OK, $responseContainer->getStatusCode());
        $this->assertEquals($data, $responseContainer->getData());
        $this->assertNull($responseContainer->getThrowable());
        $this->assertNull($responseContainer->getResponse());
        $this->assertEquals($response, $responseContainer->getExtendedResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());
    }

    public function testAutoDetectValidationExceptionResponse()
    {
        $exception = new ValidationException([]);
        $responseContainer = $this->createResponseModel($exception);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $responseContainer->getStatusCode());
        $this->assertNull($responseContainer->getData());
        $this->assertEquals($exception, $responseContainer->getThrowable());
        $this->assertNull($responseContainer->getResponse());
        $this->assertNull($responseContainer->getExtendedResponse());
        $this->assertNull($responseContainer->getPagination());
        $this->assertFalse($responseContainer->isEmpty());
    }

    protected function createResponseModel(mixed $content): ResponseModel
    {
        $responseModelFactory = new ResponseModelFactory(new ResponseModel());

        return $responseModelFactory->createFromContent($content);
    }
}
