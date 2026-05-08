<?php

namespace SuperVMar\App\Controller\Notification;

use JsonException;
use SuperVMar\Notification\Application\Save\SavePushSubscriptionCommand;
use SuperVMar\Shared\Domain\Exception\MandatoryParamsException;
use SuperVMar\Shared\Domain\ValueObject\Uuid;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class PushSubscriptionPutController extends ApiController
{
    /**
     * @throws MandatoryParamsException
     * @throws JsonException
     */
    public function __invoke(string $idUser, Request $request): Response
    {
        $data = $this->dataFromRequest($request);
        $this->dispatch(
            new SavePushSubscriptionCommand(
                Uuid::random()->value(),
                $idUser,
                $data['endpoint'],
                $data['authKey'],
                $data['p256dhKey'],
            )
        );

        return new Response(status: Response::HTTP_CREATED);
    }

    protected function mandatoryParams(): array
    {
        return ['endpoint', 'authKey', 'p256dhKey'];
    }
}
