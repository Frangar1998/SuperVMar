<?php

namespace SuperVMar\Shared\Domain\Criteria;

use SuperVMar\Shared\Domain\Collection;

class Fields extends Collection
{
    protected function type(): string
    {
        return Field::class;
    }
}