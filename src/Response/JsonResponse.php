<?php

namespace MediaMonks\RestApi\Response;

use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;

class JsonResponse extends BaseJsonResponse
    implements ExtendedResponseInterface
{
    protected mixed $customContent;

    /**
     * Constructor.
     *
     * @param mixed $data The response data
     * @param int $status The response status code
     * @param array $headers An array of response headers
     */
    public function __construct($data = null, int $status = 200, array $headers = [])
    {
        parent::__construct('', $status, $headers);

        if (null === $data) {
            $data = new \ArrayObject();
        }

        $this->setCustomContent($data);
    }

    /**
     * We need this because setData() does json encoding already and
     * this messes up the jsonp callback.
     * It is a performance hit to let it decode/encode a second time
     *
     * @param mixed $content
     * @return $this
     */
    public function setCustomContent(mixed $content): static
    {
        $this->customContent = $content;

        return $this;
    }

    public function getCustomContent(): mixed
    {
        return $this->customContent;
    }

    public function getContent(): string|false
    {
        return $this->customContent;
    }

    public function setData(mixed $data = []): static
    {
        $this->setCustomContent($data);
        return parent::setData($data);
    }

    /**
     * @return mixed
     */
    public function getCallback()
    {
        return $this->callback;
    }
}
