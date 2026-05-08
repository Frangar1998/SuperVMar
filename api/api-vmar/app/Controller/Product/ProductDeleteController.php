<?php

namespace SuperVMar\App\Controller\Product;

use SuperVMar\Product\Application\Delete\DeleteProductCommand;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Response;

final readonly class ProductDeleteController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $this->dispatch(new DeleteProductCommand($id));

        return new Response(status: Response::HTTP_OK);
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}