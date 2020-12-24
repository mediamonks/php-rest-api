<?php

namespace tests\MediaMonks\RestApi\EventSubscriber;

use MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriber;
use MediaMonks\RestApi\Request\RequestMatcherInterface;
use MediaMonks\RestApi\Request\RequestTransformerInterface;
use MediaMonks\RestApi\Response\Response;
use MediaMonks\RestApi\Response\ResponseTransformerInterface;
use \Mockery as m;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class RestApiEventSubscriberTest extends \PHPUnit_Framework_TestCase
{

    protected function getSubject($mocks = null)
    {
        [$matcher, $requestTransformer, $responseTransformer] = $mocks
            ?: $this->getMocks();

        return new RestApiEventSubscriber(
            $matcher,
            $requestTransformer,
            $responseTransformer
        );
    }

    protected function getMocks()
    {
        $matcher = m::mock(RequestMatcherInterface::class);
        $requestTransformer = m::mock(RequestTransformerInterface::class);
        $responseTransformer = m::mock(ResponseTransformerInterface::class);
        $responseTransformer->shouldReceive('createResponseFromContent')
            ->andReturn(new Response());

        return [$matcher, $requestTransformer, $responseTransformer];
    }

    public function testGetSubscribedEvents()
    {
        $subscribedEvents = RestApiEventSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::REQUEST, $subscribedEvents);
        $this->assertArrayHasKey(KernelEvents::EXCEPTION, $subscribedEvents);
        $this->assertArrayHasKey(KernelEvents::VIEW, $subscribedEvents);
        $this->assertArrayHasKey(KernelEvents::RESPONSE, $subscribedEvents);
    }

    public function testOnRequestIsBound()
    {
        $this->methodIsBound('onRequest', KernelEvents::REQUEST);
    }

    public function testOnRequestNoMatch()
    {
        [$matcher, $requestTransformer, $responseTransformer] = $this->getMocks(
        );
        $matcher->shouldReceive('matches')->andReturn(false);
        $requestTransformer->shouldReceive('transform');

        $subject = $this->getSubject(
            [$matcher, $requestTransformer, $responseTransformer]
        );

        $request = m::mock(Request::class);
        $request->shouldReceive('getMethod')->andReturn(Request::METHOD_GET);

        $mockEvent = m::mock(RequestEvent::class);
        $mockEvent->shouldReceive('getRequest')->andReturn($request);
        $mockEvent->shouldReceive('getRequestType');

        $subject->onRequest($mockEvent);

        try {
            $requestTransformer->shouldNotHaveReceived('transform');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testOnRequest()
    {
        [$matcher, $requestTransformer, $responseTransformer] = $this->getMocks(
        );
        $matcher->shouldReceive('matches')->andReturn(true);
        $requestTransformer->shouldReceive('transform');

        $subject = $this->getSubject(
            [$matcher, $requestTransformer, $responseTransformer]
        );

        $kernel = m::mock(HttpKernel::class);
        $request = m::mock(Request::class);
        $request->shouldReceive('getMethod')->andReturn(Request::METHOD_GET);

        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $subject->onRequest($event);

        try {
            $requestTransformer->shouldHaveReceived('transform')->between(1, 1);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testOnRequestOptionMethod()
    {
        [$matcher, $requestTransformer, $responseTransformer] = $this->getMocks();
        $matcher->shouldReceive('matches')->andReturn(true);
        $requestTransformer->shouldReceive('transform');

        $subject = $this->getSubject(
            [$matcher, $requestTransformer, $responseTransformer]
        );

        $kernel = m::mock(HttpKernel::class);
        $request = m::mock(Request::class);

        $request->shouldReceive('setMethod')->andReturn(Request::METHOD_OPTIONS);
        $request->shouldReceive('getMethod')->andReturn(Request::METHOD_OPTIONS);

        $request->setMethod(Request::METHOD_OPTIONS);

        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $subject->onRequest($event);

        try {
            $requestTransformer->shouldNotHaveReceived('transform');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testOnExceptionIsBound()
    {
        $this->methodIsBound('onException', KernelEvents::EXCEPTION);
    }

    public function testOnExceptionNoMatch()
    {
        [$matcher, $requestTransformer, $responseTransformer] = $this->getMocks(
        );
        $matcher->shouldReceive('matches')->andReturn(false);

        $subject = $this->getSubject(
            [$matcher, $requestTransformer, $responseTransformer]
        );

        $kernel = m::mock(HttpKernel::class);
        $request = m::mock(Request::class);
        $request->shouldReceive('getMethod')->andReturn(Request::METHOD_GET);

        $e = new \Exception();

        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $e);
        $subject->onException($event);

        try {

            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testOnException()
    {
        [$matcher, $requestTransformer, $responseTransformer] = $this->getMocks(
        );
        $matcher->shouldReceive('matches')->andReturn(true);

        $subject = $this->getSubject(
            [$matcher, $requestTransformer, $responseTransformer]
        );

        $kernel = m::mock(HttpKernel::class);
        $request = m::mock(Request::class);
        $request->shouldReceive('getMethod')->andReturn(Request::METHOD_GET);

        $e = new \Exception();

        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $e);
        $subject->onException($event);

        try {
            $this->assertNotEmpty($event->getResponse());
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testOnViewIsBould()
    {
        $this->methodIsBound('onView', KernelEvents::VIEW);
    }

    public function testOnViewNoMatch()
    {
        [$matcher, $requestTransformer, $responseTransformer] = $this->getMocks(
        );
        $matcher->shouldReceive('matches')->andReturn(false);

        $subject = $this->getSubject(
            [$matcher, $requestTransformer, $responseTransformer]
        );

        $kernel = m::mock(HttpKernel::class);
        $request = m::mock(Request::class);
        $request->shouldReceive('getMethod')->andReturn(Request::METHOD_GET);

        $event = new ViewEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, 'foo');

        $subject->onView($event);

        try {
            $this->assertEmpty($event->getResponse());
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testOnView()
    {
        [$matcher, $requestTransformer, $responseTransformer] = $this->getMocks(
        );
        $matcher->shouldReceive('matches')->andReturn(true);

        $subject = $this->getSubject(
            [$matcher, $requestTransformer, $responseTransformer]
        );

        $kernel = m::mock(HttpKernel::class);
        $request = m::mock(Request::class);
        $request->shouldReceive('getMethod')->andReturn(Request::METHOD_GET);

        $event = new ViewEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, 'foo');

        $subject->onView($event);

        try {
            $this->assertNotEmpty($event->getResponse());
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testOnResponseEarlyIsBound()
    {
        $this->methodIsBound('onResponseEarly', KernelEvents::RESPONSE);
    }

    public function testOnResponseEarlyNoMatch()
    {
        [$matcher, $requestTransformer, $responseTransformer] = $this->getMocks(
        );
        $matcher->shouldReceive('matches')->andReturn(false);

        $subject = $this->getSubject(
            [$matcher, $requestTransformer, $responseTransformer]
        );

        $kernel = m::mock(HttpKernel::class);
        $request = m::mock(Request::class);
        $request->shouldReceive('getMethod')->andReturn(Request::METHOD_GET);
        $response = m::mock(Response::class);

        $event = new ResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $subject->onResponseEarly($event);

        try {
            //$mockEvent->shouldNotHaveReceived('setResponse');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testOnResponseEarly()
    {
        $response = new \Symfony\Component\HttpFoundation\Response();

        [$matcher, $requestTransformer, $responseTransformer] = $this->getMocks();
        $matcher->shouldReceive('matches')->andReturn(true);
        $responseTransformer->shouldReceive('transformEarly')->andReturn($response);

        $subject = $this->getSubject(
            [$matcher, $requestTransformer, $responseTransformer]
        );

        $kernel = m::mock(HttpKernel::class);
        $request = m::mock(Request::class);
        $request->shouldReceive('getMethod')->andReturn(Request::METHOD_GET);

        $event = new ResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $subject->onResponseEarly($event);

        try {
            $this->assertEquals($response, $event->getResponse());
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testOnResponseLateIsBound()
    {
        $this->methodIsBound('onResponseLate', KernelEvents::RESPONSE);
    }

    public function testOnResponseLateNoMatch()
    {
        [$matcher, $requestTransformer, $responseTransformer] = $this->getMocks(
        );
        $matcher->shouldReceive('matches')->andReturn(false);

        $subject = $this->getSubject(
            [$matcher, $requestTransformer, $responseTransformer]
        );

        $kernel = m::mock(HttpKernel::class);
        $request = m::mock(Request::class);
        $request->shouldReceive('getMethod')->andReturn(Request::METHOD_GET);
        $response = m::mock(Response::class);

        $event = new ResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
        $subject->onResponseLate($event);

        try {
            $requestTransformer->shouldNotHaveReceived('transformLate');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testOnResponseLate()
    {
        [$matcher, $requestTransformer, $responseTransformer] = $this->getMocks(
        );
        $matcher->shouldReceive('matches')->andReturn(true);
        $responseTransformer->shouldReceive('transformLate');

        $subject = $this->getSubject(
            [$matcher, $requestTransformer, $responseTransformer]
        );

        $kernel = m::mock(HttpKernel::class);
        $request = m::mock(Request::class);
        $request->shouldReceive('getMethod')->andReturn(Request::METHOD_GET);
        $response = m::mock(Response::class);

        $event = new ResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $subject->onResponseLate($event);

        try {
            $responseTransformer->shouldHaveReceived('transformLate')->between(
                1,
                1
            );
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    protected function methodIsBound($method, $testEvent)
    {
        foreach (
            RestApiEventSubscriber::getSubscribedEvents() as $event =>
            $listeners
        ) {
            foreach ($listeners as $listener) {
                [$listener] = $listener;
                if ($listener == $method && $event == $testEvent) {
                    $this->assertTrue(true);

                    return;
                }
            }
        }

        $this->assertTrue(false, $method.' is not bound to event '.$testEvent);
    }
}
