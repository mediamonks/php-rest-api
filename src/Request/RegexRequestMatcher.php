<?php

namespace MediaMonks\RestApi\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RegexRequestMatcher extends AbstractRequestMatcher
{
    public function __construct(protected array $whitelist = [], protected array $blacklist = [])
    {

    }

    public function matches(Request $request, ?int $requestType = HttpKernelInterface::MAIN_REQUEST): bool
    {
        if ($requestType !== HttpKernelInterface::MAIN_REQUEST) {
            return false;
        }

        if ($this->matchPreviouslyMatchedRequest($request)) {
            return true;
        }

        if (!$this->matchRequestPathAgainstLists($request->getPathInfo())) {
            return false;
        }

        $this->markRequestAsMatched($request);

        return true;
    }

    protected function markRequestAsMatched(Request $request)
    {
        $request->attributes->set(self::ATTRIBUTE_MATCHED, true);
    }

    protected function matchPreviouslyMatchedRequest(Request $request): bool
    {
        return $request->attributes->getBoolean(self::ATTRIBUTE_MATCHED);
    }

    protected function matchRequestPathAgainstLists($requestPath): bool
    {
        if ($this->matchRequestPathAgainstBlacklist($requestPath)) {
            return false;
        }

        if ($this->matchRequestPathAgainstWhitelist($requestPath)) {
            return true;
        }

        return false;
    }

    protected function matchRequestPathAgainstBlacklist(string $requestPath): bool
    {
        foreach ($this->blacklist as $regex) {
            if (preg_match($regex, $requestPath)) {
                return true;
            }
        }

        return false;
    }

    protected function matchRequestPathAgainstWhitelist(string $requestPath): bool
    {
        foreach ($this->whitelist as $regex) {
            if (preg_match($regex, $requestPath)) {
                return true;
            }
        }

        return false;
    }
}
