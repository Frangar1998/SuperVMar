<?php

namespace SuperVMar\App\Controller\Sale;

use JsonException;
use SuperVMar\Product\Application\Save\SaveProductCommand;
use SuperVMar\Sale\Application\SaveLine\SaveSaleLineCommand;
use SuperVMar\Shared\Domain\Exception\MandatoryParamsException;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class SaleLinePutController extends ApiController
{
    /**
     * @throws MandatoryParamsException
     * @throws JsonException
     */
    public function __invoke(string $id, Request $request): Response
    {
        $data = $this->dataFromRequest($request);
        $this->dispatch(
            new SaveSaleLineCommand(
                $id,
                $data['product'],
                $data['quantity'],
            )
        );

        return new Response(status: Response::HTTP_CREATED);
    }

    protected function mandatoryParams(): array
    {
        return ['product', 'quantity'];
    }
}