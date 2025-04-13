<?php

namespace SuperVMar\App\Controller\Supermarket;

use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\Supermarket\Application\Search\Zones\SearchZonesQuery;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ZonesGetController extends ApiController
{
    public function __invoke(string $idSupermarket): JsonResponse
    {
        $response = $this->ask(new SearchZonesQuery($idSupermarket));

        return new JsonResponse($response->toArray());
    }

    protected function mandatoryParams(): array
    {
        return [];
    }
}