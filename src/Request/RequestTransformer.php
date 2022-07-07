<?php

namespace MediaMonks\RestApi\Request;

use MediaMonks\RestApi\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestTransformer implements RequestTransformerInterface
{
    public function __construct(protected SerializerInterface $serializer)
    {
    }

    public function transform(Request $request)
    {
        $this->acceptJsonBody($request);
        $this->setRequestFormat($request);
    }

    protected function acceptJsonBody(Request $request)
    {
        if (str_starts_with($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : []);
        }
    }

    protected function setRequestFormat(Request $request)
    {
        $default = Format::getDefault();
        $format = $request->getRequestFormat($request->query->get('_format', $default));

        if (!in_array($format, $this->serializer->getSupportedFormats())) {
            $format = $this->serializer->getDefaultFormat();
        }

        $request->setRequestFormat($format);
    }
}
