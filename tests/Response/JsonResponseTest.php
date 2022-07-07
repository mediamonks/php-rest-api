<?php

namespace MediaMonks\RestApi\Tests\Response;

use MediaMonks\RestApi\Response\JsonResponse;
use PHPUnit\Framework\TestCase;

class JsonResponseTest extends TestCase
{
    public function testJsonResponse()
    {
        $data     = ['foo', 'bar'];
        $response = new JsonResponse($data);
        $this->assertEquals(json_encode($data), $response->getContent());
    }

    public function testJsonResponseSetter()
    {
        $data     = ['foo', 'bar'];
        $response = new JsonResponse();
        $response->setData($data);
        $this->assertEquals(json_encode($data), $response->getContent());
    }
}
