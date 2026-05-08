<?php

namespace SuperVMar\Notification\Domain\Service;

use SuperVMar\Notification\Domain\PushSubscription;
use SuperVMar\Notification\Domain\PushSubscriptionRepository;
use SuperVMar\Shared\Domain\Criteria\Criteria;
use SuperVMar\Shared\Domain\Criteria\FieldName;
use SuperVMar\Shared\Domain\Criteria\Filter;
use SuperVMar\Shared\Domain\Criteria\FilterField;
use SuperVMar\Shared\Domain\Criteria\FilterOperator;
use SuperVMar\Shared\Domain\Criteria\Filters;
use SuperVMar\Shared\Domain\Criteria\FilterValue;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\TableNames;
use SuperVMar\Shared\Domain\ValueObject\Id;

readonly class PushSubscriptionSearcher
{
    public function __construct(
        private PushSubscriptionRepository $pushSubscriptionRepository,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function searchByUserId(Id $idUser): PushSubscription
    {
        return $this->pushSubscriptionRepository->searchByCriteria(
            new Criteria(
                filters: new Filters(
                    [
                        new Filter(
                            new FilterField(TableNames::TABLE_PUSH_SUBSCRIPTION, new FieldName('idUser')),
                            FilterOperator::EQUAL,
                            new FilterValue($idUser)
                        )
                    ]
                )
            )
        )->first();
    }
}
