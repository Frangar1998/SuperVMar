<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Sale\Application;

use SuperVMar\App\Tests\Unit\ApplicationTestCase;
use SuperVMar\Sale\Application\FinishSale\FinishSaleCommand;
use SuperVMar\Sale\Application\FinishSale\FinishSaleCommandHandler;
use SuperVMar\Sale\Application\FinishSale\SaleFinisher;

final class FinishSaleCommandHandlerTest extends ApplicationTestCase
{
    private SaleFinisher $finisher;
    private FinishSaleCommandHandler $handler;

    protected function setUp(): void
    {
        $this->finisher = $this->createMock(SaleFinisher::class);
        $this->handler  = new FinishSaleCommandHandler($this->finisher);
    }

    public function test_finishes_sale_with_cash_payment(): void
    {
        $this->finisher->expects($this->once())->method('finishSale');

        ($this->handler)(new FinishSaleCommand(id: self::ID_SALE, payMethod: 'cash'));
    }

    public function test_finishes_sale_with_card_payment(): void
    {
        $this->finisher->expects($this->once())->method('finishSale');

        ($this->handler)(new FinishSaleCommand(id: self::ID_SALE, payMethod: 'card'));
    }

    public function test_passes_correct_pay_method_to_finisher(): void
    {
        $capturedPayMethod = null;

        $this->finisher
            ->expects($this->once())
            ->method('finishSale')
            ->willReturnCallback(function ($id, $payMethod) use (&$capturedPayMethod) {
                $capturedPayMethod = $payMethod;
            });

        ($this->handler)(new FinishSaleCommand(id: self::ID_SALE, payMethod: 'card'));

        $this->assertSame('card', $capturedPayMethod->value);
    }
}
