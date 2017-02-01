<?php

namespace tests\MediaMonks\RestApi\EventSubscriber;

use MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriberFactory;

class RestApiEventSubscriberFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $eventSubscriber = RestApiEventSubscriberFactory::create();
        $this->assertInstanceOf('MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriber', $eventSubscriber);
    }

    public function testCreateWithOptions()
    {
        $eventSubscriber = RestApiEventSubscriberFactory::create('/api',
            ['debug' => true, 'post_message_origin' => 'https://www.mediamonks.com']
        );
        $this->assertInstanceOf('MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriber', $eventSubscriber);
    }
}
