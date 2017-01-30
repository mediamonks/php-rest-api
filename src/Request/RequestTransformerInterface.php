<?php

namespace MediaMonks\RestApi\Request;

use Symfony\Component\HttpFoundation\Request;

interface RequestTransformerInterface
{
    public function transform(Request $request);
}
