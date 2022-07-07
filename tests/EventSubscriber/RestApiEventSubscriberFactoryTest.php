<?php

namespace MediaMonks\RestApi\Tests\EventSubscriber;

use MediaMonks\RestApi\EventSubscriber\RestApiEventSubscriberFactory;
use PHPUnit\Framework\TestCase;

class RestApiEventSubscriberFactoryTest extends TestCase
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
