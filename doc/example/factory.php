<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$eventSubscriber = \MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriberFactory::create();

// register the event subscriber in the event dispatcher
$dispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();
$dispatcher->addSubscriber($eventSubscriber);

// then inject the $dispatcher in your http kernel
