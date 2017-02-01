<?php

namespace tests\MediaMonks\RestApi\EventSubscriber;

use MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriberFactory;
use Mockery as m;

class RestApiEventSubscriberFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $eventSubscriber = RestApiEventSubscriberFactory::create();
        $this->assertInstanceOf('MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriber', $eventSubscriber);
    }

    public function testCreateWithOptions()
    {
        $eventSubscriber = RestApiEventSubscriberFactory::create(
            [
                'debug' => true,
                'post_message_origin' => 'https://www.mediamonks.com'
            ]
        );
        $this->assertInstanceOf('MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriber', $eventSubscriber);
    }
}
