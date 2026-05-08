<?php

namespace SuperVMar\App\Controller\Product;

use JsonException;
use SuperVMar\Product\Application\ReceiveStock\ReceiveStockCommand;
use SuperVMar\Shared\Domain\Exception\MandatoryParamsException;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ProductReceiveStockPutController extends ApiController
{
    /**
     * @throws MandatoryParamsException
     * @throws JsonException
     */
    public function __invoke(string $idProduct, Request $request): Response
    {
        $data = $this->dataFromRequest($request);

        $this->dispatch(
            new ReceiveStockCommand(
                $idProduct,
                $data['quantity'],
            )
        );

        return new Response(status: Response::HTTP_OK);
    }

    protected function mandatoryParams(): array
    {
        return ['quantity'];
    }
}
