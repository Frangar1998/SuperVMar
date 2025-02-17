<?php

namespace SuperVMar\Shared\Domain\Criteria;

final readonly class Order
{
    public function __construct(
        private OrderBy $orderBy,
        private OrderType $orderType
    )
    {
    }

    public static function createDesc(OrderBy $orderBy): self
    {
        return new self($orderBy, OrderType::DESC);
    }

    public static function createAsc(OrderBy $orderBy): self
    {
        return new self($orderBy, OrderType::ASC);
    }

    public function orderBy(): OrderBy
    {
        return $this->orderBy;
    }

    public function orderType(): OrderType
    {
        return $this->orderType;
    }

    public static function fromValues(?string $orderBy, ?string $order): self
    {
        return null === $orderBy ? self::none() : new self(new OrderBy($orderBy), OrderType::tryFrom($order));
    }

    public function isNone(): bool
    {
        return $this->orderType() === OrderType::NONE;
    }

    public static function none(): self
    {
        return new self(new OrderBy(''), OrderType::NONE);
    }
}