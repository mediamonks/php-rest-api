<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriberFactory;

$app = new Silex\Application();

$app['dispatcher']->addSubscriber(RestApiEventSubscriberFactory::create());

$app->get('/api', function() {
    return 'Hello Api';
});

$app->run();
