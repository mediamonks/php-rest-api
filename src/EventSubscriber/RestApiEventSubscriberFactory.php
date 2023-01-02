<?php

namespace MediaMonks\RestApi\EventSubscriber;

use MediaMonks\RestApi\Model\ResponseModel;
use MediaMonks\RestApi\Model\ResponseModelFactory;
use MediaMonks\RestApi\Request\PathRequestMatcher;
use MediaMonks\RestApi\Request\RequestTransformer;
use MediaMonks\RestApi\Response\ResponseTransformer;
use MediaMonks\RestApi\Serializer\JsonSerializer;

class RestApiEventSubscriberFactory
{
    public static function create(array $options = []): RestApiEventSubscriber
    {
        $options = self::getOptions($options);
        $requestTransformer = new RequestTransformer($options['serializer']);
        $responseModelFactory = new ResponseModelFactory($options['response_model']);
        $responseTransformer = new ResponseTransformer(
            $options['serializer'],
            $responseModelFactory,
            self::getResponseTransformerOptions($options)
        );

        return new RestApiEventSubscriber($options['request_matcher'], $requestTransformer, $responseTransformer);
    }

    protected static function getOptions(array $options = []): array
    {
        if (empty($options['path'])) {
            $options['path'] = '/api';
        }

        if (empty($options['serializer'])) {
            $options['serializer'] = new JsonSerializer();
        }

        if (empty($options['response_model'])) {
            $options['response_model'] = new ResponseModel();
        }

        if (empty($options['request_matcher'])) {
            $options['request_matcher'] = new PathRequestMatcher($options['path']);
        }

        return $options;
    }

    protected static function getResponseTransformerOptions(array $options = []): array
    {
        $responseTransformerOptions = [];
        if (isset($options['debug'])) {
            $responseTransformerOptions['debug'] = $options['debug'];
        }
        if (isset($options['post_message_origin'])) {
            $responseTransformerOptions['post_message_origin'] = $options['post_message_origin'];
        }

        return $responseTransformerOptions;
    }
}
