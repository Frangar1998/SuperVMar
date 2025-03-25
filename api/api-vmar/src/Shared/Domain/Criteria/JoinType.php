<?php

namespace SuperVMar\Shared\Domain\Criteria;

enum JoinType: string
{
    case INNER = 'INNER';
    case LEFT = 'LEFT';
    case RIGHT = 'RIGHT';
}
