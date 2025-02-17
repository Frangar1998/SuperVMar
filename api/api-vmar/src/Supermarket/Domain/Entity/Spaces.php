<?php

namespace SuperVMar\Supermarket\Domain\Entity;

use SuperVMar\Shared\Domain\Collection;

final class Spaces extends Collection
{

    protected function type(): string
    {
        return Space::class;
    }
}