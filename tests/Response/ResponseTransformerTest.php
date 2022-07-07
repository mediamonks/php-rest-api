<?php

namespace MediaMonks\RestApi\Tests\Response;

use MediaMonks\RestApi\Model\ResponseModel;
use MediaMonks\RestApi\Request\Format;
use MediaMonks\RestApi\Response\ResponseTransformer;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponseTransformerTest extends TestCase
{
    private $responseModelFactory;

    protected function getSubject($options = [])
    {
        $serializer = m::mock('MediaMonks\RestApi\Serializer\SerializerInterface');
        $serializer->shouldReceive('serialize');

        $responseModel = new ResponseModel();

        $responseModelFactory = m::mock('MediaMonks\RestApi\Model\ResponseModelFactory');
        $responseModelFactory->shouldReceive('createFromContent')->andReturn($responseModel);

        $this->responseModelFactory = $responseModelFactory;

        return new ResponseTransformer($serializer, $responseModelFactory, $options);
    }

    public function testConstructSetsOptions()
    {
        $origin  = 'postmsgorigin';
        $subject = $this->getSubject(['post_message_origin' => $origin]);

        $this->assertEquals($origin, $subject->getPostMessageOrigin());
    }

    public function testSetOptions()
    {
        $subject = $this->getSubject();
        $origin  = 'postmsgorigin';

        $subject->setOptions(['post_message_origin' => $origin]);

        $this->assertEquals($origin, $subject->getPostMessageOrigin());
    }

    public function testSetOptionsWithoutPostMessageOrigin()
    {
        $subject = $this->getSubject();
        $origin  = 'postmsgorigin';

        $subject->setOptions(['someotherkey' => $origin]);

        $this->assertNull($subject->getPostMessageOrigin());
    }

    public function testSetPostMessageOrigin()
    {
        $subject = $this->getSubject();
        $origin  = 'postmsgorigin';

        $subject->setPostMessageOrigin($origin);

        $this->assertEquals($origin, $subject->getPostMessageOrigin());
    }

    public function testTransformLateFalsePreconditions()
    {
        $subject = $this->getSubject();

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getRequestFormat')->andReturn(Format::FORMAT_XML);;

        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->shouldReceive('getStatusCode')->andReturn(Response::HTTP_OK);
        $response->shouldReceive('setCallback');

        $subject->transformLate($request, $response);

        try {
            $response->shouldNotHaveReceived('setCallback');
            $response->shouldNotHaveReceived('setContent');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false);
        }
    }

    public function testTransformLateWrapperPostMessage()
    {
        $subject = $this->getSubject();

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getRequestFormat')->andReturn(Format::FORMAT_JSON);

        $request->query = m::mock('\Symfony\Component\HttpFoundation\ParameterBag');
        $request->query->shouldReceive('has')->andReturn(true);
        $request->query->shouldReceive('get')->andReturn(ResponseTransformer::WRAPPER_POST_MESSAGE);

        $response = m::mock('MediaMonks\RestApi\Response\JsonResponse');
        $response->shouldReceive('getStatusCode')->andReturn(Response::HTTP_OK);
        $response->shouldReceive('setContent')->andReturnSelf();
        $response->shouldReceive('getContent')->andReturn('foo');

        $response->headers = m::mock('\Symfony\Component\HttpFoundation\ResponseHeaderBag');
        $response->headers->shouldReceive('set');

        $subject->transformLate($request, $response);

        try {
            $response->shouldHaveReceived('setContent')->between(1, 1);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false);
        }
    }

    public function testTransformLateWrapperCallback()
    {
        $subject = $this->getSubject();

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getRequestFormat')->andReturn(Format::FORMAT_JSON);
        $request->query = m::mock('\Symfony\Component\HttpFoundation\ParameterBag');
        $request->query->shouldReceive('has')->andReturn(true);
        $request->query->shouldReceive('get');

        $response = m::mock('MediaMonks\RestApi\Response\JsonResponse');
        $response->shouldReceive('getStatusCode')->andReturn(Response::HTTP_OK);
        $response->shouldReceive('setCallback');

        $subject->transformLate($request, $response);

        try {
            $response->shouldHaveReceived('setCallback')->between(1, 1);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false);
        }
    }

    public function testTransformEarlyWResponseModelHappyPath()
    {
        // Get SUT
        $subject = $this->getSubject();

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getRequestFormat')->andReturn(Format::FORMAT_JSON);

        $request->headers = m::mock('Symfony\Component\HttpFoundation\HeaderBag');
        $request->headers->shouldReceive('has')->andReturn(true);

        $request->query = m::mock('Symfony\Component\HttpFoundation\ParameterBag');
        $request->query->shouldReceive('has')->andReturn(false);

        $responseModel = m::mock('MediaMonks\RestApi\Model\ResponseModel');
        $responseModel->shouldReceive('getStatusCode')->andReturn(Response::HTTP_CONFLICT);
        $responseModel->shouldReceive('setReturnStatusCode');
        $responseModel->shouldReceive('setReturnStackTrace');
        $responseModel->shouldReceive('toArray');

        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->shouldReceive('getContent')->andReturn($responseModel);
        $response->shouldReceive('setStatusCode');
        $response->shouldReceive('getStatusCode')->andReturn(Response::HTTP_CONFLICT);

        $response->headers = m::mock('Symfony\Component\HttpFoundation\ResponseHeaderBag');
        $response->headers->shouldReceive('set');

        // Perform operation
        $actualResponse = $subject->transformEarly($request, $response);

        // Verify post-conditions
        $this->assertNoException(function () use ($response) {
            $response->shouldHaveReceived('setStatusCode');
            $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        });

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $actualResponse);
    }

    public function testTransformEarlyForceHttpOk()
    {
        $subject = $this->getSubject();

        $request = new Request();
        $response = new Response('foo', Response::HTTP_NO_CONTENT);
        $response->headers->set('Content-Type', 'text/html');

        $subject->transformLate($request, $response);

        $this->assertEmpty($response->getContent());
        $this->assertFalse($response->headers->has('Content-Type'));
    }

    public function testForceEmptyResponseOnHttpNoContent()
    {
        // Get SUT
        $subject = $this->getSubject();

        // Prepare/mock pre-conditions
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getRequestFormat')->andReturn(Format::FORMAT_JSON);

        $request->headers = m::mock('Symfony\Component\HttpFoundation\HeaderBag');
        $request->headers->shouldReceive('has')->andReturn(true);

        $request->query = m::mock('Symfony\Component\HttpFoundation\ParameterBag');
        $request->query->shouldReceive('has')->andReturn(false);

        $responseModel = m::mock('MediaMonks\RestApi\Model\ResponseModel');
        $responseModel->shouldReceive('getStatusCode')->andReturn(Response::HTTP_NO_CONTENT);
        $responseModel->shouldReceive('setReturnStatusCode');
        $responseModel->shouldReceive('setReturnStackTrace');
        $responseModel->shouldReceive('toArray');

        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->shouldReceive('getContent')->andReturn($responseModel);
        $response->shouldReceive('setStatusCode');
        $response->shouldReceive('getStatusCode')->andReturn(200);

        $response->headers = m::mock('Symfony\Component\HttpFoundation\ResponseHeaderBag');
        $response->headers->shouldReceive('set');

        // Perform operation
        $subject->transformEarly($request, $response);

        // Verify post-conditions
        $this->assertNoException(function () use ($response) {
            $response->shouldHaveReceived('setStatusCode')->with(Response::HTTP_OK);
        });
    }

    public function testTransformEarlySerializeXml()
    {
        // Get SUT
        $subject = $this->getSubject();

        // Prepare/mock pre-conditions
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getRequestFormat')->andReturn(Format::FORMAT_XML);

        $request->headers = m::mock('Symfony\Component\HttpFoundation\HeaderBag');
        $request->headers->shouldReceive('has')->andReturn(true);

        $request->query = m::mock('Symfony\Component\HttpFoundation\ParameterBag');
        $request->query->shouldReceive('has')->andReturn(false);

        $responseModel = m::mock('MediaMonks\RestApi\Model\ResponseModel');
        $responseModel->shouldReceive('getStatusCode')->andReturn(Response::HTTP_CONFLICT);
        $responseModel->shouldReceive('setReturnStatusCode');
        $responseModel->shouldReceive('setReturnStackTrace');
        $responseModel->shouldReceive('toArray');

        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->shouldReceive('getContent')->andReturn($responseModel);
        $response->shouldReceive('setStatusCode');
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $response->shouldReceive('setContent');

        $response->headers = m::mock('Symfony\Component\HttpFoundation\ResponseHeaderBag');
        $response->headers->shouldReceive('set');

        // Perform operation
        $subject->transformEarly($request, $response);

        // Verify post-conditions
        $this->assertNoException(function () use ($response) {
            $response->shouldHaveReceived('setStatusCode')->with(Response::HTTP_OK);
            $response->shouldHaveReceived('setContent');
        });
    }

    public function testTransformEarlyWOResponseModel()
    {
        // Get SUT
        $subject = $this->getSubject();

        // Prepare/mock pre-conditions
        $content = 'some content';

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getRequestFormat')->andReturn(Format::FORMAT_XML);

        $request->headers = m::mock('Symfony\Component\HttpFoundation\HeaderBag');
        $request->headers->shouldReceive('has')->andReturn(true);

        $request->query = m::mock('Symfony\Component\HttpFoundation\ParameterBag');
        $request->query->shouldReceive('has')->andReturn(false);

        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->shouldReceive('getContent')->andReturn($content);
        $response->shouldReceive('setStatusCode');
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $response->shouldReceive('setContent');

        $response->headers = m::mock('Symfony\Component\HttpFoundation\ResponseHeaderBag');
        $response->headers->shouldReceive('set');

        $responseModel = m::mock('MediaMonks\RestApi\Model\ResponseModel');
        $responseModel->shouldReceive('getStatusCode')->andReturn(Response::HTTP_CONFLICT);
        $responseModel->shouldReceive('setReturnStatusCode');
        $responseModel->shouldReceive('setReturnStackTrace');
        $responseModel->shouldReceive('toArray');

        // Perform operation
        $subject->transformEarly($request, $response);

        $factory = $this->responseModelFactory;

        // Verify post-conditions
        $this->assertNoException(function () use ($factory, $content) {
            $factory->shouldHaveReceived('createFromContent');
        });
    }

    public function testDebugGetSet()
    {
        $subject = $this->getSubject();

        $this->assertFalse($subject->isDebug());
        $subject->setDebug(true);
        $this->assertTrue($subject->isDebug());

        $subject = $this->getSubject(['debug' => true]);
        $this->assertTrue($subject->isDebug());
    }

    public function testCreateResponseFromContent()
    {
        $subject = $this->getSubject();
        $responseModel = $subject->createResponseFromContent('foo');

        $this->assertInstanceOf('MediaMonks\RestApi\Response\Response', $responseModel);
    }

    protected function assertNoException($callback)
    {
        try {
            $callback();
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail('Exception thrown when none was expected. Exception message: ' . $e->getMessage());
        }
    }
}
