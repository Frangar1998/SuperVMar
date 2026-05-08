<?php

namespace SuperVMar\App\Controller\Sale;

use SuperVMar\Sale\Application\Delete\DeleteSaleCommand;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

final readonly class SaleDeleteController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $this->dispatch(new DeleteSaleCommand($id));

        return new Response(status: Response::HTTP_OK);
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}
