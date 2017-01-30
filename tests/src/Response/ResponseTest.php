<?php

namespace tests\MediaMonks\RestApi\Response;

use MediaMonks\RestApi\Response\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testCursorPaginatedResponse()
    {
        $data     = ['foo', 'bar'];
        $response = new Response($data);
        $this->assertEquals($data, $response->getContent());
    }
}
