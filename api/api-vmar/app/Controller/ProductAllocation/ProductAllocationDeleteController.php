<?php

namespace SuperVMar\App\Controller\ProductAllocation;

use SuperVMar\ProductAllocation\Application\Delete\DeleteProductAllocationCommand;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

final readonly class ProductAllocationDeleteController extends ApiController
{
    public function __invoke(string $idSpace): Response
    {
        $this->dispatch(new DeleteProductAllocationCommand($idSpace));

        return new Response(status: Response::HTTP_OK);
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}