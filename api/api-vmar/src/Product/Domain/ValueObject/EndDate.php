<?php

namespace SuperVMar\Product\Domain\ValueObject;

use SuperVMar\Shared\Domain\ValueObject\DateValueObject;

final readonly class EndDate extends DateValueObject
{
    public function formatDate(): string
    {
        return $this->format('Y-m-d H:i:s');
    }
}