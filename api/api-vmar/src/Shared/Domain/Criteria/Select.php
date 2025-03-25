<?php

namespace SuperVMar\Shared\Domain\Criteria;

final readonly class Select
{
    public function __construct(
        private Fields $fields,
    )
    {
    }

    public function fields(): array
    {
        return $this->fields->items();
    }


}