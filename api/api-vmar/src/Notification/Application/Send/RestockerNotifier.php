<?php

namespace SuperVMar\Notification\Application\Send;

use SuperVMar\Notification\Domain\PushSubscriptionRepository;
use SuperVMar\Notification\Domain\RestockerFinder;
use SuperVMar\Notification\Domain\WebPushSender;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class RestockerNotifier
{
    public function __construct(
        private RestockerFinder $restockerFinder,
        private PushSubscriptionRepository $pushSubscriptionRepository,
        private WebPushSender $webPushSender,
    )
    {
    }

    public function notify(
        string $idZone,
        string $productName,
        string $zoneName,
        int    $quantity,
    ): void
    {
        $userIds = $this->restockerFinder->findRestockerUserIdsByZone(new Id($idZone));

        if (empty($userIds)) {
            return;
        }

        $subscriptions = $this->pushSubscriptionRepository->searchByUserIds($userIds);

        if ($subscriptions->count() === 0) {
            return;
        }

        $status = $quantity === 0 ? 'VACÍO' : 'STOCK BAJO';
        $payload = [
            'title' => 'Reposición necesaria',
            'body' => "{$productName} · {$zoneName} · {$status}",
            'icon' => '/images/supervmar-logo.png',
            'data' => [
                'url' => '/productos/reposiciones',
                'zoneId' => $idZone,
                'status' => $quantity === 0 ? 'empty' : 'low',
            ],
        ];

        $this->webPushSender->send($subscriptions, $payload);
    }
}
