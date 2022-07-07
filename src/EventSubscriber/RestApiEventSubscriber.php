<?php

namespace MediaMonks\RestApi\EventSubscriber;

use JetBrains\PhpStorm\ArrayShape;
use MediaMonks\RestApi\Request\RequestMatcherInterface;
use MediaMonks\RestApi\Request\RequestTransformerInterface;
use MediaMonks\RestApi\Response\ResponseTransformerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RestApiEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestMatcherInterface      $requestMatcher,
        private RequestTransformerInterface  $requestTransformer,
        private ResponseTransformerInterface $responseTransformer
    )
    {

    }

    #[ArrayShape([KernelEvents::REQUEST => "array[]", KernelEvents::EXCEPTION => "array[]", KernelEvents::VIEW => "array[]", KernelEvents::RESPONSE => "array[]"])] public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onRequest', 512],
            ],
            KernelEvents::EXCEPTION => [
                ['onException', 512],
            ],
            KernelEvents::VIEW => [
                ['onView', 0],
            ],
            KernelEvents::RESPONSE => [
                ['onResponseEarly', 0],
                ['onResponseLate', -512],
            ],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$this->eventRequestMatches($event)) {
            return;
        }

        $this->requestTransformer->transform($event->getRequest());
    }

    public function onException(ExceptionEvent $event): void
    {
        if (!$this->eventRequestMatches($event)) {
            return;
        }

        $event->setResponse($this->responseTransformer->createResponseFromContent($event->getThrowable()));
    }

    public function onView(ViewEvent $event): void
    {
        if (!$this->eventRequestMatches($event)) {
            return;
        }

        $event->setResponse($this->responseTransformer->createResponseFromContent($event->getControllerResult()));
    }

    public function onResponseEarly(ResponseEvent $event): void
    {
        if (!$this->eventRequestMatches($event)) {
            return;
        }

        $event->setResponse($this->responseTransformer->transformEarly($event->getRequest(), $event->getResponse()));
    }

    public function onResponseLate(ResponseEvent $event): void
    {
        if (!$this->eventRequestMatches($event)) {
            return;
        }

        $this->responseTransformer->transformLate($event->getRequest(), $event->getResponse());
    }

    protected function eventRequestMatches(KernelEvent $event): bool
    {
        if ($event->getRequest()->getMethod() === Request::METHOD_OPTIONS) return false;

        return $this->requestMatcher->matches($event->getRequest(), $event->getRequestType());
    }
}
