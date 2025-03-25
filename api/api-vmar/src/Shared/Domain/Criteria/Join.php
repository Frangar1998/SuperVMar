<?php

namespace SuperVMar\Shared\Domain\Criteria;

final readonly class Join
{
    public function __construct(
        private JoinType        $type,
        private JoinFirstTable  $firstTable,
        private JoinSecondTable $secondTable,
        private On $on
    )
    {
    }

    public function type(): JoinType
    {
        return $this->type;
    }

    public function firstTable(): JoinFirstTable
    {
        return $this->firstTable;
    }

    public function secondTable(): JoinSecondTable
    {
        return $this->secondTable;
    }

    public function on(): On
    {
        return $this->on;
    }


}