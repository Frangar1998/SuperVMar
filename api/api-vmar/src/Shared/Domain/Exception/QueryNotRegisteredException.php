<?php

namespace SuperVMar\Shared\Domain\Exception;

use RuntimeException;
use SuperVMar\Shared\Domain\Bus\Query\Query;

class QueryNotRegisteredException extends RuntimeException
{
    private const MESSAGE = 'The query <%s> has no query handler';

    public function __construct(Query $query)
    {
        $queryClass = get_class($query);
        $message = sprintf(self::MESSAGE, $queryClass);
        parent::__construct($message);
    }
}