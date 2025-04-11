<?php

namespace SuperVMar\Product\Domain\ValueObject;

enum Status: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;
}