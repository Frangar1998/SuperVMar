<?php

namespace SuperVMar\App\Controller\Supplier;

use JsonException;
use SuperVMar\Shared\Domain\Exception\MandatoryParamsException;
use SuperVMar\Shared\Infrastructure\Symfony\ApiController;
use SuperVMar\Supplier\Application\Save\SaveSupplierCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class SupplierPutController extends ApiController
{
    /**
     * @throws MandatoryParamsException
     * @throws JsonException
     */
    public function __invoke(string $id, Request $request): Response
    {
        $data = $this->dataFromRequest($request);
        $this->dispatch(
            new SaveSupplierCommand(
                $id,
                $data['name'],
                $data['phone'],
                $data['email'],
                $data['contact']
            )
        );

        return new Response(status: Response::HTTP_CREATED);
    }

    protected function mandatoryParams(): array
    {
        return ['name', 'phone', 'email', 'contact'];
    }
}