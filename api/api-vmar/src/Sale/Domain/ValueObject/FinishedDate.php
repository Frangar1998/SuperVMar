<?php

namespace SuperVMar\Sale\Domain\ValueObject;

use SuperVMar\Shared\Domain\ValueObject\DateValueObject;

final readonly class FinishedDate extends DateValueObject
{
    public function formatDate(): string
    {
        return $this->format('Y-m-d H:i:s');
    }
}