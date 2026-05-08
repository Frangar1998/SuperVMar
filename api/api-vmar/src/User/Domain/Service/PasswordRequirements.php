<?php

namespace SuperVMar\User\Domain\Service;

final class PasswordRequirements
{
    public const string VALID_SPECIAL_CHARACTERS = "#?!@$%^&*_";
    public const int MIN_LENGTH = 12;
    public const int MAX_LENGTH = 100;

    public static function validatePassword(string $password, string $passwordRepeat): array
    {
        $invalid = [];
        self::samePassword($password, $passwordRepeat) || ($invalid[] = 'ERROR_SAME');
        self::validLength($password) || ($invalid[] = 'ERROR_LENGTH');
        self::hasOneNumber($password) || ($invalid[] = 'ERROR_NUMBER');
        self::hasOneUpperLetter($password) || ($invalid[] = 'ERROR_UPPER');
        self::hasOneLowerLetter($password) || ($invalid[] = 'ERROR_LOWER');
        self::hasOneSpecialCharacter($password) || ($invalid[] = 'ERROR_SPECIAL');
        return $invalid;
    }

    protected static function samePassword(string $password, string $passwordRepeat): bool
    {
        return $password === $passwordRepeat;
    }

    protected static function validLength(string $password): bool
    {
        return !empty($password) && strlen($password) >= self::MIN_LENGTH && strlen($password) <= self::MAX_LENGTH;
    }

    protected static function hasOneNumber(string $password): bool
    {
        return preg_match('/[0-9]+/', $password);
    }

    protected static function hasOneUpperLetter(string $password): bool
    {
        return preg_match('/[A-Z]+/', $password);
    }

    protected static function hasOneLowerLetter(string $password): bool
    {
        return preg_match('/[a-z]+/', $password);
    }

    protected static function hasOneSpecialCharacter(string $password): bool
    {
        return preg_match('/[' . self::VALID_SPECIAL_CHARACTERS . ']+/', $password);
    }
}