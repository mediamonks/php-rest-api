<?php

namespace MediaMonks\RestApi\Tests\Request;

use MediaMonks\RestApi\Request\Format;
use MediaMonks\RestApi\Request\RequestTransformer;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RequestTransformerTest extends TestCase
{
    /**
     * @return RequestTransformer
     */
    public function getSubject()
    {
        $serializer = m::mock('MediaMonks\RestApi\Serializer\SerializerInterface');
        $serializer->shouldReceive('getSupportedFormats')->andReturn(['json', 'xml']);
        $serializer->shouldReceive('getDefaultFormat')->andReturn('json');

        return new RequestTransformer($serializer);
    }

    /**
     * @dataProvider provideJsonMimeTypes
     */
    public function testTransformChangesRequestParameters(string $contentType): void
    {
        $subject = $this->getSubject();
        $content = ['Hello', 'World!'];
        $request = $this->getRequest(content: $content, contentType: $contentType);

        $subject->transform($request);

        $this->assertEquals($content, iterator_to_array($request->request->getIterator()));
    }

    /**
     * @dataProvider provideJsonMimeTypes
     */
    public function testTransformChangesRequestFormatDefault(string $contentType)
    {
        $subject = $this->getSubject();
        $request = $this->getRequest(content: [], contentType: $contentType);

        $subject->transform($request);

        $this->assertEquals('json', $request->getRequestFormat());
    }

    public function testTransformChangesRequestFormatGiven()
    {
        $subject = $this->getSubject();
        $request = $this->getRequest([]);
        $request->initialize(['_format' => 'xml']);

        $subject->transform($request);

        $this->assertEquals('xml', $request->getRequestFormat());
    }

    public function testTransformChangesRequestFormatUnknown()
    {
        $subject = $this->getSubject();
        $request = $this->getRequest([]);
        $request->initialize(['_format' => 'csv']);

        $subject->transform($request);

        $this->assertEquals(Format::getDefault(), $request->getRequestFormat());
    }

    protected function getRequest(mixed $content, string $contentType = 'application/json'): Request
    {
        $request = Request::create('/');
        $request->initialize([], [], [], [], [], [], json_encode($content));
        $request->headers->add(['Content-type' => $contentType]);
        return $request;
    }

    public static function provideJsonMimeTypes(): array
    {
        return [
            'application/json'             => ['application/json'],
            'application/merge-patch+json' => ['application/merge-patch+json'],
        ];
    }
}
