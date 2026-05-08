<?php

namespace SuperVMar\Product\Domain\Entity;

use SuperVMar\Product\Domain\ValueObject\EndDate;
use SuperVMar\Product\Domain\ValueObject\Price;
use SuperVMar\Product\Domain\ValueObject\StartDate;
use SuperVMar\Shared\Domain\ValueObject\Id;

final class HistoricalPrice
{
    public function __construct(
        private readonly Id        $id,
        private readonly Price     $price,
        private readonly StartDate $startDate,
        private ?EndDate           $endDate = null
    )
    {
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function price(): Price
    {
        return $this->price;
    }

    public function startDate(): StartDate
    {
        return $this->startDate;
    }

    public function endDate(): ?EndDate
    {
        return $this->endDate;
    }

    public function setEndDate(EndDate $endDate): void
    {
        $this->endDate = $endDate;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            new Id($data['id']),
            new Price($data['price']),
            new StartDate($data['startDate']),
            isset($data['endDate']) ? new EndDate($data['endDate']) : null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'price' => $this->price->value(),
            'startDate' => $this->startDate->formatDate(),
            'endDate' => $this->endDate?->formatDate()
        ];
    }

    public function compare(self $other): bool
    {
        return $this->id->equals($other->id())
            && $this->price->equals($other->price())
            && $this->startDate->equals($other->startDate())
            && $this->endDate?->equals($other->endDate());
    }
}