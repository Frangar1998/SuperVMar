<?php

namespace SuperVMar\Notification\Application\Send;

use SuperVMar\Notification\Domain\PushSubscriptionRepository;
use SuperVMar\Notification\Domain\WebPushSender;

readonly class RestockerNotifier
{
    public function __construct(
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
        $subscriptions = $this->pushSubscriptionRepository->searchAll();

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
