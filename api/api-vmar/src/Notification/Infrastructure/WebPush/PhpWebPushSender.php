<?php

namespace SuperVMar\Notification\Infrastructure\WebPush;

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use SuperVMar\Notification\Domain\WebPushSender;
use SuperVMar\Notification\Domain\PushSubscription;
use SuperVMar\Notification\Domain\PushSubscriptions;
use Throwable;

final readonly class PhpWebPushSender implements WebPushSender
{
    public function __construct(
        private string $vapidSubject,
        private string $vapidPublicKey,
        private string $vapidPrivateKey,
    )
    {
    }

    public function send(PushSubscriptions $subscriptions, array $payload): void
    {
        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => $this->vapidSubject,
                    'publicKey' => $this->vapidPublicKey,
                    'privateKey' => $this->vapidPrivateKey,
                ],
            ]);

            $encodedPayload = json_encode($payload, JSON_THROW_ON_ERROR);

            /** @var PushSubscription $pushSubscription */
            foreach ($subscriptions as $pushSubscription) {
                $subscription = Subscription::create([
                    'endpoint' => $pushSubscription->endpoint()->value(),
                    'keys' => [
                        'auth' => $pushSubscription->authKey()->value(),
                        'p256dh' => $pushSubscription->p256dhKey()->value(),
                    ],
                ]);

                $webPush->sendOneNotification($subscription, $encodedPayload);
            }
        } catch (Throwable) {
        }
    }
}
