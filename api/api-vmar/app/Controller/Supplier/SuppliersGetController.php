<?php

namespace SuperVMar\App\Controller\Supplier;

use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\Supplier\Application\Search\SearchSuppliersQuery;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class SuppliersGetController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        $response = $this->ask(new SearchSuppliersQuery());

        return new JsonResponse($response->toArray());
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}