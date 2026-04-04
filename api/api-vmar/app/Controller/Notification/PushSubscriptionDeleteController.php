<?php

namespace SuperVMar\App\Controller\Notification;

use SuperVMar\Notification\Application\Delete\DeletePushSubscriptionCommand;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

final readonly class PushSubscriptionDeleteController extends ApiController
{
    public function __invoke(string $idUser): Response
    {
        $this->dispatch(new DeletePushSubscriptionCommand($idUser));

        return new Response(status: Response::HTTP_OK);
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}
