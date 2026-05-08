<?php

namespace SuperVMar\Authentication\Domain;

use SuperVMar\Shared\Domain\Criteria\Criteria;

interface AuthRepository
{
    public function search(Criteria $criteria): AuthUser;
}