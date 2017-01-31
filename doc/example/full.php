<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$whitelist = [
    '~^/api/$~',
    '~^/api~'
];
$blacklist = [
    '~^/api/doc~'
];
$options = [
    'debug' => false,
    'post_message_origin' => 'https://www.mediamonks.com'
];

// choose a serializer, we pick json as default
$serializer = new MediaMonks\RestApi\Serializer\JsonSerializer();

// initialize the request matcher with the whitelist and blacklist
$requestMatcher = new MediaMonks\RestApi\Request\RequestMatcher($whitelist, $blacklist);

// initialize the request transformer, this sets the output format as an attribute in the request
$requestTransformer = new MediaMonks\RestApi\Request\RequestTransformer($serializer);

// this is the model that will be used to transform your output to
$responseModel = new MediaMonks\RestApi\Model\ResponseModel();

// will return a new response model for every response
$responseModelFactory = new \MediaMonks\RestApi\Model\ResponseModelFactory($responseModel);

// where most of the magic happens, converts any response or exception into the response model
$responseTransformer = new MediaMonks\RestApi\Response\ResponseTransformer($serializer, $responseModelFactory, $options);

// the subscriber that ties it all together and hooks into the HttpKernel
$eventSubscriber = new MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriber(
    $requestMatcher,
    $requestTransformer,
    $responseTransformer
);

// register the event subscriber in the event dispatcher
$dispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();
$dispatcher->addSubscriber($eventSubscriber);

// then inject the $dispatcher in your http kernel
