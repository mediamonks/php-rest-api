<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriberFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;

$eventSubscriber = RestApiEventSubscriberFactory::create();

// register the event subscriber in the event dispatcher
$dispatcher = new EventDispatcher;
$dispatcher->addSubscriber($eventSubscriber);

// then inject the $dispatcher in your http kernel
