<?php

namespace SuperVMar\User\Domain\Exception;

use Exception;
use SuperVMar\User\Domain\Service\PasswordRequirements;

class InvalidPasswordException extends Exception
{
    private const string MESSAGE = 'The password does not meet the following requirements:' . PHP_EOL . '%s';
    private const string ERROR_LENGTH = 'The password must have at least ' . PasswordRequirements::MIN_LENGTH . ' characters and at max ' . PasswordRequirements::MAX_LENGTH . ' characters.';
    private const string ERROR_NUMBER = 'The password must has at least one number.';
    private const string ERROR_UPPER = 'The password must has at least one uppercase letter.';
    private const string ERROR_LOWER = 'The password must has at least one lowercase letter.';
    private const string ERROR_SPECIAL = 'The password must has at least one of these special characters: ' . PasswordRequirements::VALID_SPECIAL_CHARACTERS;
    private const string ERROR_SAME = 'The passwords must be the same.';
    private const string ERROR_CURRENT_PASSWORD = 'The current password is not correct.';

    public function __construct(array $errors)
    {
        $message = '';
        foreach ($errors as $error) {
            $message .= match ($error) {
                'ERROR_LENGTH' => self::ERROR_LENGTH . PHP_EOL,
                'ERROR_NUMBER' => self::ERROR_NUMBER . PHP_EOL,
                'ERROR_UPPER' => self::ERROR_UPPER . PHP_EOL,
                'ERROR_LOWER' => self::ERROR_LOWER . PHP_EOL,
                'ERROR_SPECIAL' => self::ERROR_SPECIAL . PHP_EOL,
                'ERROR_SAME' => self::ERROR_SAME . PHP_EOL,
                'ERROR_CURRENT_PASSWORD' => self::ERROR_CURRENT_PASSWORD . PHP_EOL,
            };
        }
        $message = sprintf(self::MESSAGE, $message);
        parent::__construct($message);
    }
}