<?php

namespace MediaMonks\RestApi\Tests\Request;

use MediaMonks\RestApi\Request\PathRequestMatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class PathRequestMatcherTest extends TestCase
{

    public function testMatches()
    {
        $matcher = new PathRequestMatcher('/foo');
        foreach ([
                     ['path' => '/foo', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => true],
                     ['path' => '/bar', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/foo', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                     ['path' => '/bar', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                 ] as $test
        ) {
            $this->assertEquals($test['result'],
                $matcher->matches($this->getRequestFromPath($test['path']), $test['type']));
        }
    }


    /**
     * @param string $path
     * @return Request
     */
    protected function getRequestFromPath($path)
    {
        return Request::create($path);
    }
}
