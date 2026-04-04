<?php

namespace SuperVMar\User\Domain;

use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

interface UserRepository
{
    public function insert(User $user): void;
    public function update(User $user): void;
    public function delete(User $user): void;
    /**
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): Users;
    public function searchJobNameByUserId(Id $userId): ?string;
}