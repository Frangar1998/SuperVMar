<?php

namespace SuperVMar\Notification\Domain;

use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

interface PushSubscriptionRepository
{
    public function insert(PushSubscription $pushSubscription): void;

    public function update(PushSubscription $pushSubscription): void;

    public function deleteByUserId(Id $idUser): void;

    /** @param Id[] $userIds */
    public function searchByUserIds(array $userIds): PushSubscriptions;

    /**
     * @throws ItemNotFoundException
     */
    public function searchByCriteria(Criteria $criteria): PushSubscriptions;
}
