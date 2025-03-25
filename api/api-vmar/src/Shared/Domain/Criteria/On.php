<?php

namespace SuperVMar\Shared\Domain\Criteria;

final readonly class On
{
    public function __construct(
        private OnFirstField $firstField,
        private OnOperator $operator,
        private OnSecondField $secondField,
    )
    {
    }

    public function firstField(): OnFirstField
    {
        return $this->firstField;
    }

    public function operator(): OnOperator
    {
        return $this->operator;
    }

    public function secondField(): OnSecondField
    {
        return $this->secondField;
    }


}