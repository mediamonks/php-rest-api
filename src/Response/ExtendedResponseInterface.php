<?php
declare(strict_types=1);

namespace MediaMonks\RestApi\Response;

interface ExtendedResponseInterface
{
    public function setCustomContent(mixed $content): static;
    public function getCustomContent(): mixed;
}
