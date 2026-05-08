<?php

namespace SuperVMar\Shared\Domain\Criteria;

final readonly class Filter
{
    public function __construct(
        private FilterField $field,
        private FilterOperator $operator,
        private FilterValue $value
    )
    {
    }

    public function field(): FilterField
    {
        return $this->field;
    }

    public function operator(): FilterOperator
    {
        return $this->operator;
    }

    public function value(): FilterValue
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            'field' => $this->field->__toString(),
            'operator' => $this->operator->value,
            'value' => $this->value->value()
        ];
    }
}