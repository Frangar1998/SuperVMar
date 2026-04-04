<?php

namespace SuperVMar\Sale\Domain;

use DateTime;
use SuperVMar\Sale\Domain\Entity\Line;
use SuperVMar\Sale\Domain\Entity\Lines;
use SuperVMar\Sale\Domain\Entity\Product;
use SuperVMar\Sale\Domain\Event\ProductScannedDomainEvent;
use SuperVMar\Sale\Domain\Event\SaleFinishedDomainEvent;
use SuperVMar\Sale\Domain\ValueObject\Amount;
use SuperVMar\Sale\Domain\ValueObject\FinishedDate;
use SuperVMar\Sale\Domain\ValueObject\PayMethod;
use SuperVMar\Sale\Domain\ValueObject\Quantity;
use SuperVMar\Sale\Domain\ValueObject\TaxesAmount;
use SuperVMar\Sale\Domain\ValueObject\TotalAmount;
use SuperVMar\Shared\Domain\AggregateRoot;
use SuperVMar\Shared\Domain\Utils;
use SuperVMar\Shared\Domain\ValueObject\Id;

final class Sale extends AggregateRoot
{
    public function __construct(
        private readonly Id $id,
        private Amount $amount,
        private TaxesAmount $taxesAmount,
        private TotalAmount $totalAmount,
        private readonly Lines $lines,
        private PayMethod $payMethod = PayMethod::NONE,
        private ?FinishedDate $finishedDate = null
    )
    {
    }
    
    public function id(): Id
    {
        return $this->id;
    }
    
    public function amount(): Amount
    {
        return $this->amount;
    }

    public function taxesAmount(): TaxesAmount
    {
        return $this->taxesAmount;
    }

    public function totalAmount(): TotalAmount
    {
        return $this->totalAmount;
    }

    public function lines(): Lines
    {
        return $this->lines;
    }

    public function payMethod(): PayMethod
    {
        return $this->payMethod;
    }

    public function finishedDate(): ?FinishedDate
    {
        return $this->finishedDate;
    }

    public function addOrUpdateLine(
        Product $product,
        Quantity $quantity
    ): void
    {
        $key = $this->lines->hasProduct($product);
        if ($key === null) {
            $this->addLine($product, $quantity);
        } else {
            $this->updateLine($quantity, $key);
        }
    }

    protected function addLine(
        Product $product,
        Quantity $quantity
    ): void
    {
        $this->lines->add(
            new Line(
                new Id(Id::random()->value()),
                $product,
                $this->calculateTotalAmount($product, $quantity),
                $quantity
            )
        );
        $this->record(
            new ProductScannedDomainEvent(
                $this->id,
                $product->id(),
                $quantity->value()
            )
        );
    }

    protected function updateLine(
        Quantity $addedQuantity,
        int $key
    ): void
    {
        /**
         * @var Line $line
         */
        $line = $this->lines->getItem($key);
        if ($addedQuantity->value() > 0) {
            $line->addQuantity($addedQuantity);
            $this->record(
                new ProductScannedDomainEvent(
                    $this->id,
                    $line->product()->id(),
                    $addedQuantity->value()
                )
            );
        } else {
            $line->subtractQuantity(new Quantity(abs($addedQuantity->value())));
        }

        if ($line->quantity()->value() == 0) {
            $this->lines->removeByKey($key);
        } else {
            $this->lines->replace($line, $key);
        }
    }

    public function updateAmounts(
        Product $product,
        Quantity $quantity
    ): void
    {
        if ($quantity->value() > 0) {
            $this->amount = $this->amount->add($this->calculateAmount($product, $quantity));
            $this->totalAmount = $this->totalAmount->add($this->calculateTotalAmount($product, $quantity));
            $this->taxesAmount = $this->taxesAmount->add($this->calculateTaxesAmount($product, $quantity));
        } else {
            $absQuantity = new Quantity(abs($quantity->value()));
            $this->amount = $this->amount->subtract($this->calculateAmount($product, $absQuantity));
            $this->totalAmount = $this->totalAmount->subtract($this->calculateTotalAmount($product, $absQuantity));
            $this->taxesAmount = $this->taxesAmount->subtract($this->calculateTaxesAmount($product, $absQuantity));
        }
    }

    protected function calculateAmount(
        Product $product,
        Quantity $quantity
    ): Amount
    {
        return new Amount($quantity->value() * $product->priceValue() * $product->taxInvertedValue());
    }

    protected function calculateTotalAmount(
        Product $product,
        Quantity $quantity
    ): Amount
    {
        return new Amount($quantity->value() * $product->priceValue());
    }

    protected function calculateTaxesAmount(
        Product $product,
        Quantity $quantity
    ): Amount
    {
        return new Amount($quantity->value() * $product->priceValue() * $product->taxValue());
    }

    public function setFinishedDate(): void
    {
        $this->finishedDate = new FinishedDate();
        $this->record(
            new SaleFinishedDomainEvent(
                $this->id->value(),
                $this->lines->toArray()
            )
        );
    }

    public function setPayMethod(PayMethod $payMethod): void
    {
        $this->payMethod = $payMethod;

    }

    public static function create(
        Id $id
    ): self
    {
        return new self(
            $id,
            new Amount(),
            new TaxesAmount(),
            new TotalAmount(),
            new Lines()
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Amount($data['amount']),
            new TaxesAmount($data['taxes']),
            new TotalAmount($data['totalAmount']),
            Lines::fromArray($data['lines']),
            PayMethod::from($data['payMethod']),
            isset($data['date']) ? new FinishedDate($data['date']) : null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'amount' => $this->amount->value(),
            'taxes' => $this->taxesAmount->value(),
            'totalAmount' => $this->totalAmount->value(),
            'lines' => $this->lines->toArray(),
            'payMethod' => $this->payMethod->value,
            'finishedDate' => $this->finishedDate?->formatDate(),
        ];
    }
    
}