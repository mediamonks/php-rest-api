<?php

namespace MediaMonks\RestApi\Response;

use Symfony\Component\HttpFoundation\Response as BaseResponse;

class Response extends BaseResponse
{
    /**
     * @param mixed $data    The response data
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     * @param bool  $json    If the data is already a JSON string
     */
    public function __construct($data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct('', $status, $headers);

        $this->setContent($data);
    }

    /**
     * Sets the response content.
     *
     * We need to allow all sorts of content, not just the ones the regular Response setContent() allows
     *
     * @param mixed $content
     * @return Response
     * @api
     */
    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }
}
