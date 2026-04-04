<?php

namespace SuperVMar\App\Controller\Notification;

use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class VapidKeyGetController
{
    public function __construct(
        private string $vapidPublicKey,
    )
    {
    }

    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['publicKey' => $this->vapidPublicKey]);
    }
}
