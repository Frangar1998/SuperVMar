<?php

namespace SuperVMar\Shared\Domain\Criteria;

use SuperVMar\Shared\Domain\Collection;

final class Joins extends Collection
{

    protected function type(): string
    {
        return Join::class;
    }
}