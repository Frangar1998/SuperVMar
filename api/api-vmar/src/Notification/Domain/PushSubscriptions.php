<?php

namespace SuperVMar\Notification\Domain;

use SuperVMar\Shared\Domain\Collection;

final class PushSubscriptions extends Collection
{
    protected function type(): string
    {
        return PushSubscription::class;
    }

    public static function fromArray(array $subscriptions): self
    {
        return new self(
            array_map(
                fn(array $subscription) => PushSubscription::fromArray($subscription),
                $subscriptions
            )
        );
    }
}
