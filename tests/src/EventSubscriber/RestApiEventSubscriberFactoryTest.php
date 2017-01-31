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
}
