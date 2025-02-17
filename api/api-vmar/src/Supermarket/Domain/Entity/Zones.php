<?php

namespace SuperVMar\Supermarket\Domain\Entity;

use SuperVMar\Shared\Domain\Collection;

final class Zones extends Collection
{

    protected function type(): string
    {
        return Zone::class;
    }
}