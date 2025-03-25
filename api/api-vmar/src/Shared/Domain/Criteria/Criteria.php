<?php

namespace SuperVMar\Shared\Domain\Criteria;

final readonly class Criteria
{
    public function __construct(
        private Filters $filters,
        private ?Select $select = null,
        private ?Joins $joins = null,
        private ?Order  $order = null,
        private ?int    $offset = null,
        private ?int    $limit = null
    )
    {
    }

    public function hasSelect(): bool
    {
        return $this->select !== null;
    }

    public function hasJoins(): bool
    {
        return $this->joins !== null && $this->joins->count() > 0;
    }

    public function hasOrder(): bool
    {
        return $this->order !== null && !$this->order->isNone();
    }

    public function hasOffset(): bool
    {
        return $this->offset !== null;
    }

    public function hasLimit(): bool
    {
        return $this->limit !== null;
    }

    public function filters(): Filters
    {
        return $this->filters;
    }

    public function select(): ?Select
    {
        return $this->select;
    }

    public function joins(): Joins
    {
        return $this->joins;
    }

    public function order(): Order
    {
        return $this->order;
    }

    public function offset(): ?int
    {
        return $this->offset;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }
}