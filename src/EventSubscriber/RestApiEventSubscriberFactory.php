<?php

namespace MediaMonks\RestApi\EventSubscriber;

use MediaMonks\RestApi\Model\ResponseModel;
use MediaMonks\RestApi\Model\ResponseModelFactory;
use MediaMonks\RestApi\Model\ResponseModelInterface;
use MediaMonks\RestApi\Request\RequestMatcher;
use MediaMonks\RestApi\Request\RequestTransformer;
use MediaMonks\RestApi\Response\ResponseTransformer;
use MediaMonks\RestApi\Serializer\JsonSerializer;
use MediaMonks\RestApi\Serializer\SerializerInterface;

class RestApiEventSubscriberFactory
{
    /**
     * @param array $whitelist
     * @param array $blacklist
     * @param SerializerInterface|null $serializer
     * @param ResponseModelInterface|null $responseModel
     * @param array $options
     * @return RestApiEventSubscriber
     */
    public static function create(
        array $whitelist = [],
        array $blacklist = [],
        SerializerInterface $serializer = null,
        ResponseModelInterface $responseModel = null,
        $options = []
    ) {
        if (empty($whitelist)) {
            $whitelist = ['~^/api/$~', '~^/api~'];
        }
        if (empty($serializer)) {
            $serializer = new JsonSerializer();
        }
        if (empty($responseModel)) {
            $responseModel = new ResponseModel();
        }

        $requestMatcher = new RequestMatcher($whitelist, $blacklist);
        $requestTransformer = new RequestTransformer($serializer);
        $responseModelFactory = new ResponseModelFactory($responseModel);
        $responseTransformer = new ResponseTransformer($serializer, $responseModelFactory, $options);

        return new RestApiEventSubscriber($requestMatcher, $requestTransformer, $responseTransformer);
    }
}
