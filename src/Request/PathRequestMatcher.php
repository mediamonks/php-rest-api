<?php

namespace MediaMonks\RestApi\Request;

class PathRequestMatcher extends RegexRequestMatcher
{
    public function __construct(string $path)
    {
        parent::__construct([sprintf('~^%s~', $path), sprintf('~^%s/~', $path)]);
    }
}
