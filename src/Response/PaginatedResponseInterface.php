<?php

namespace MediaMonks\RestApi\Response;

interface PaginatedResponseInterface
{
    public function toArray();

    public function getData();
}
