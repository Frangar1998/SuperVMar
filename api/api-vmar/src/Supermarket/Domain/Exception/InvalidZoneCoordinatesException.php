<?php

namespace SuperVMar\Supermarket\Domain\Exception;

use Exception;

class InvalidZoneCoordinatesException extends Exception
{
    private const string MESSAGE = 'The coordinates [cornerTopLeft: %s, cornerTopRight: %s, cornerBottomRight: %s, cornetBottomLeft: %s] are not valid.';

    public function __construct(string $cornerTopLeft, string $cornerTopRight, string $cornerBottomRight, string $cornetBottomLeft)
    {
        $message = sprintf(self::MESSAGE, $cornerTopLeft, $cornerTopRight, $cornerBottomRight, $cornetBottomLeft);
        parent::__construct($message);
    }
}