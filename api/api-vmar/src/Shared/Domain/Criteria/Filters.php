<?php

namespace SuperVMar\Shared\Domain\Criteria;

use SuperVMar\Shared\Domain\Collection;

final class Filters extends Collection
{
    protected function type(): string
    {
        return Filter::class;
    }
}