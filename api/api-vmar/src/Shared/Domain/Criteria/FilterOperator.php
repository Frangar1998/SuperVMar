<?php

namespace SuperVMar\Shared\Domain\Criteria;

enum FilterOperator: string
{
    case EQUAL = '=';
    case NOT_EQUAL = '!=';
    case GREATER = '>';
    case LOWER = '<';
    case CONTAINS = 'CONTAINS';
    case NOT_CONTAINS = 'NOT_CONTAINS';
    case GREATER_EQUAL = '>=';
    case LOWER_EQUAL = '<=';
    case NULL = 'IS NULL';
    case NOT_NULL = 'IS NOT NULL';
}
