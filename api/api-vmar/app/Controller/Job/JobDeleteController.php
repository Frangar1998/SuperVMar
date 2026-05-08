<?php

namespace SuperVMar\App\Controller\Job;

use SuperVMar\Job\Application\Delete\DeleteJobCommand;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

final readonly class JobDeleteController extends ApiController
{

    public function __invoke(string $id): Response
    {
        $this->dispatch(new DeleteJobCommand($id));

        return new Response(status: Response::HTTP_OK);
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}