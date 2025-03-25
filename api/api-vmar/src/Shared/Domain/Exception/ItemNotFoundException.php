<?php

namespace SuperVMar\Shared\Domain\Exception;

use Exception;
use JsonException;
use SuperVMar\Shared\Domain\Utils;

class ItemNotFoundException extends Exception
{
    private const MESSAGE = '<%s> with filters: <%s> not found.';

    /**
     * @throws JsonException
     */
    public function __construct(string $item, array $filters)
    {
        $message = sprintf(self::MESSAGE, $item, Utils::jsonEncode($filters));
        parent::__construct($message);
    }
}