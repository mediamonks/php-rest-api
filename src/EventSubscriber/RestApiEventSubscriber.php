<?php

namespace MediaMonks\RestApi\EventSubscriber;

use MediaMonks\RestApi\Request\RequestMatcherInterface;
use MediaMonks\RestApi\Request\RequestTransformerInterface;
use MediaMonks\RestApi\Response\ResponseTransformerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RestApiEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var RequestMatcherInterface
     */
    private $requestMatcher;

    /**
     * @var RequestTransformerInterface
     */
    private $requestTransformer;

    /**
     * @var ResponseTransformerInterface
     */
    private $responseTransformer;

    /**
     * @param RequestMatcherInterface $requestMatcher
     * @param RequestTransformerInterface $requestTransformer
     * @param ResponseTransformerInterface $responseTransformer
     */
    public function __construct(
        RequestMatcherInterface $requestMatcher,
        RequestTransformerInterface $requestTransformer,
        ResponseTransformerInterface $responseTransformer
    ) {
        $this->requestMatcher = $requestMatcher;
        $this->requestTransformer = $requestTransformer;
        $this->responseTransformer = $responseTransformer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST   => [
                ['onRequest', 512],
            ],
            KernelEvents::EXCEPTION => [
                ['onException', 512],
            ],
            KernelEvents::VIEW      => [
                ['onView', 0],
            ],
            KernelEvents::RESPONSE  => [
                ['onResponseEarly', 0],
                ['onResponseLate', -512],
            ],
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function onRequest(RequestEvent $event)
    {
        if (!$this->eventRequestMatches($event)) {
            return;
        }
        $this->requestTransformer->transform($event->getRequest());
    }

    /**
     * convert exception to rest api response
     *
     * @param ExceptionEvent $event
     */
    public function onException(ExceptionEvent $event)
    {
        if (!$this->eventRequestMatches($event)) {
            return;
        }
        $event->setResponse($this->responseTransformer->createResponseFromContent($event->getThrowable()));
    }

    /**
     * convert response to rest api response
     *
     * @param ViewEvent $event
     */
    public function onView(ViewEvent $event)
    {
        if (!$this->eventRequestMatches($event)) {
            return;
        }
        $event->setResponse($this->responseTransformer->createResponseFromContent($event->getControllerResult()));
    }

    /**
     * converts content to correct output format
     *
     * @param ResponseEvent $event
     */
    public function onResponseEarly(ResponseEvent $event)
    {
        if (!$this->eventRequestMatches($event)) {
            return;
        }
        $event->setResponse($this->responseTransformer->transformEarly($event->getRequest(), $event->getResponse()));
    }

    /**
     * wrap the content if needed
     *
     * @param ResponseEvent $event
     */
    public function onResponseLate(ResponseEvent $event)
    {
        if (!$this->eventRequestMatches($event)) {
            return;
        }
        $this->responseTransformer->transformLate($event->getRequest(), $event->getResponse());
    }

    /**
     * @param KernelEvent $event
     * @return bool
     */
    protected function eventRequestMatches(KernelEvent $event)
    {
        if ($event->getRequest()->getMethod() === 'OPTIONS') return false;

        return $this->requestMatcher->matches($event->getRequest(), $event->getRequestType());
    }
}
