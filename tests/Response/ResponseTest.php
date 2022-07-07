<?php

namespace MediaMonks\RestApi\Tests\Response;

use MediaMonks\RestApi\Response\JsonResponse;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testCursorPaginatedResponse()
    {
        $data     = ['foo', 'bar'];
        $response = new JsonResponse($data);
        $this->assertEquals(json_encode($data), $response->getContent());
    }
}
