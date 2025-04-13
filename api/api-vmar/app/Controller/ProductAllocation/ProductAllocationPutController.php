<?php

namespace SuperVMar\App\Controller\ProductAllocation;

use JsonException;
use SuperVMar\ProductAllocation\Application\Save\SaveProductAllocationCommand;
use SuperVMar\Shared\Domain\Exception\MandatoryParamsException;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ProductAllocationPutController extends ApiController
{
    /**
     * @throws MandatoryParamsException
     * @throws JsonException
     */
    public function __invoke(string $idSpace, Request $request): Response
    {
        $data = $this->dataFromRequest($request);
        $this->dispatch(
            new SaveProductAllocationCommand(
                $data['product'],
                $idSpace,
                $data['quantity']
            )
        );

        return new Response(status: Response::HTTP_CREATED);
    }

    protected function mandatoryParams(): array
    {
        return ['product', 'quantity'];
    }
}