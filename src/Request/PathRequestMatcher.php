<?php

namespace MediaMonks\RestApi\Request;

class PathRequestMatcher extends RegexRequestMatcher
{
    /**
     * @param string $path
     */
    public function __construct($path)
    {
        parent::__construct([sprintf('~^/%s~', $path), sprintf('~^/%s/~', $path)]);
    }
}
