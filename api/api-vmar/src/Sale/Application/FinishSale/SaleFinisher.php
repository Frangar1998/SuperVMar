<?php

namespace SuperVMar\Sale\Application\FinishSale;

use SuperVMar\Sale\Domain\SaleRepository;
use SuperVMar\Sale\Domain\Service\BillGenerator;
use SuperVMar\Sale\Domain\Service\SaleSearcher;
use SuperVMar\Sale\Domain\ValueObject\PayMethod;
use SuperVMar\Shared\Domain\Bus\Event\QueueEventBus;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\ValueObject\Id;

readonly class SaleFinisher
{
    public function __construct(
        private SaleSearcher   $saleSearcher,
        private SaleRepository $saleRepository,
        private QueueEventBus  $queueEventBus,
        private BillGenerator  $billGenerator,
    )
    {
    }

    /**
     * @throws ItemNotFoundException
     */
    public function finishSale(
        Id        $id,
        PayMethod $payMethod,
    ): void
    {
        $sale = $this->saleSearcher->search($id);
        $sale->setPayMethod($payMethod);
        $sale->setFinishedDate();

        $this->saleRepository->update($sale);

        $this->queueEventBus->publish(...$sale->pullDomainEvents());

        $bill = $this->billGenerator->generate($sale);
        $sale->setBill($bill);
        $this->saleRepository->updateBill($sale->id(), $bill);
    }
}