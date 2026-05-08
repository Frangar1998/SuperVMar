<?php

namespace SuperVMar\Notification\Domain;

use SuperVMar\Shared\Domain\ValueObject\Id;

interface RestockerFinder
{
    /** @return Id[] */
    public function findRestockerUserIdsByZone(Id $idZone): array;
}
