<?php

namespace SuperVMar\App\Controller\Supplier;

use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\Supplier\Application\Delete\DeleteSupplierCommand;
use Symfony\Component\HttpFoundation\Response;

final readonly class SupplierDeleteController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $this->dispatch(new DeleteSupplierCommand($id));

        return new Response(status: Response::HTTP_OK);
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}