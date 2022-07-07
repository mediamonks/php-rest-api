<?php

namespace MediaMonks\RestApi\Response;

abstract class AbstractPaginatedResponse
{
    public function __construct(protected mixed $data, protected int $limit, protected ?int $total = null)
    {
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(?int $total): void
    {
        $this->total = $total;
    }
}
