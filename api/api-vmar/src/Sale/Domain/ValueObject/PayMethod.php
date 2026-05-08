<?php

namespace SuperVMar\Sale\Domain\ValueObject;

enum PayMethod: string
{
    case CARD = 'card';
    case CASH = 'cash';
    case NONE = 'none';
}
