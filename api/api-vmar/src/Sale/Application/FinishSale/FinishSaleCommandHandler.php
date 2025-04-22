<?php

namespace SuperVMar\Sale\Application\FinishSale;

use SuperVMar\Sale\Domain\ValueObject\PayMethod;
use SuperVMar\Shared\Domain\Bus\Command\CommandHandler;
use SuperVMar\Shared\Domain\ValueObject\Id;

final readonly class FinishSaleCommandHandler implements CommandHandler
{
    public function __construct(
        private SaleFinisher $saleFinisher,
    )
    {
    }

    public function __invoke(FinishSaleCommand $command): void
    {
        $id = new Id($command->id());
        $payMethod = PayMethod::from($command->payMethod());

        $this->saleFinisher->finishSale($id, $payMethod);
    }
}