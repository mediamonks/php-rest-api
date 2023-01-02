<?php

namespace MediaMonks\RestApi\Tests\Request;

use MediaMonks\RestApi\Request\AbstractRequestMatcher;
use MediaMonks\RestApi\Request\RegexRequestMatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RegexRequestMatcherTest extends TestCase
{

    public function testMatchesEmptyWhitelist()
    {
        $matcher = new RegexRequestMatcher([]);
        foreach ([
                     ['path' => '/foo', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/bar', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/foo', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                     ['path' => '/bar', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                 ] as $test
        ) {
            $this->assertEquals($test['result'],
                $matcher->matches($this->getRequestFromPath($test['path']), $test['type']));
        }
    }

    public function testMatchesWhitelist()
    {
        $matcher = new RegexRequestMatcher([
            '~^/api$~',
            '~^/api/~'
        ]);
        foreach ([
                     ['path' => '/foo', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/foo', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/fapi', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/api', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => true],
                     ['path' => '/api', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                     ['path' => '/api/', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => true],
                     ['path' => '/api/', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                     ['path' => '/api/foo', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => true],
                     ['path' => '/api/doc', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => true],
                 ] as $test
        ) {
            $this->assertEquals($test['result'],
                $matcher->matches($this->getRequestFromPath($test['path']), $test['type']));
        }
    }

    public function testMatchesWhitelistBlacklist()
    {
        $matcher = new RegexRequestMatcher([
            '~^/api$~',
            '~^/api/~'
        ], [
            '~^/api/doc~'
        ]);
        foreach ([
                     ['path' => '/foo', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/foo', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/fapi', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/api', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => true],
                     ['path' => '/api', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                     ['path' => '/api/', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => true],
                     ['path' => '/api/', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                     ['path' => '/api/foo', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => true],
                     ['path' => '/api/doc', 'type' => HttpKernelInterface::MASTER_REQUEST, 'result' => false],
                     ['path' => '/api/doc', 'type' => HttpKernelInterface::SUB_REQUEST, 'result' => false],
                 ] as $test
        ) {
            $this->assertEquals($test['result'],
                $matcher->matches($this->getRequestFromPath($test['path']), $test['type']));
        }
    }

    public function testMatchedRequestIsMarkedAsMatched()
    {
        $matcher = new RegexRequestMatcher(['~^/api$~']);
        $request = $this->getRequestFromPath('/api');

        $this->assertEquals(true, $matcher->matches($request));
        $this->assertTrue($request->attributes->has(AbstractRequestMatcher::ATTRIBUTE_MATCHED));
        $this->assertEquals(true, $matcher->matches($request));
    }

    public function testNonMatchedRequestIsNotMarkedAsMatched()
    {
        $matcher = new RegexRequestMatcher(['~^/api$~']);
        $request = $this->getRequestFromPath('/');

        $this->assertEquals(false, $matcher->matches($request));
        $this->assertFalse($request->attributes->has(AbstractRequestMatcher::ATTRIBUTE_MATCHED));
    }

    public function testMatchedRequestIsNotMatchedTwice()
    {
        $matcher = new RegexRequestMatcher(['~^/api$~']);
        $request = $this->getRequestFromPath('/');

        $this->assertEquals(false, $matcher->matches($request));
        $this->assertFalse($request->attributes->has(AbstractRequestMatcher::ATTRIBUTE_MATCHED));
        $this->assertEquals(false, $matcher->matches($request));
    }

    public function testMatchesAlreadyMatched()
    {
        $subject = new RegexRequestMatcher(['~^/api$~']);
        $request = $this->getRequestFromPath('/api');

        // First match, path 1
        $this->assertTrue($subject->matches($request));
        // Second match, shortcut path 2
        $this->assertTrue($subject->matches($request));
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
