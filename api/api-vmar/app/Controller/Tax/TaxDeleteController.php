<?php

namespace SuperVMar\App\Controller\Tax;

use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\Tax\Application\Delete\DeleteTaxCommand;
use Symfony\Component\HttpFoundation\Response;

final readonly class TaxDeleteController extends ApiController
{
    public function __invoke(string $id): Response
    {
        $this->dispatch(new DeleteTaxCommand($id));

        return new Response(status: Response::HTTP_OK);
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}