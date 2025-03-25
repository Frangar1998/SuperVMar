<?php

namespace SuperVMar\Shared\Domain;

use SuperVMar\Shared\Domain\Exception\InvalidValueException;

final class Assert
{
    public static function arrayOf(string $class, array $items): void
    {
        foreach ($items as $item) {
            self::instanceOf($class, $item);
        }
    }

    public static function instanceOf(string $class, mixed $object): void
    {
        if (!$object instanceof $class) {
            throw new InvalidValueException(sprintf('The object <%s> is not an instance of <%s>', $class, $object::class));
        }
    }
}