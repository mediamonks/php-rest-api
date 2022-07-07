<?php

namespace MediaMonks\RestApi\Request;

use Symfony\Component\HttpFoundation\Request;

interface RequestMatcherInterface
{
    public function matches(Request $request, ?int $requestType);
}
