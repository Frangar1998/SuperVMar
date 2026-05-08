<?php

namespace SuperVMar\App\Controller\Tax;

use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\Tax\Application\Search\SearchTaxesQuery;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class TaxesGetController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        $response = $this->ask(new SearchTaxesQuery());

        return new JsonResponse($response->toArray());
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}