<?php

namespace SuperVMar\Product\Domain\Exception;

use Exception;

class ProductEanAlreadyExistsException extends Exception
{
    private const string MESSAGE = 'Ya existe un producto con EAN <%s>.';

    public function __construct(string $ean)
    {
        $message = sprintf(self::MESSAGE, $ean);
        parent::__construct($message);
    }
}
