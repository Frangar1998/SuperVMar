<?php

declare(strict_types=1);

namespace SuperVMar\App\Tests\Unit\Shared\Domain\Exception;

use PHPUnit\Framework\TestCase;
use SuperVMar\Shared\Domain\Exception\CannotDeleteException;
use SuperVMar\Shared\Domain\Exception\DuplicateItemException;
use SuperVMar\Shared\Domain\Exception\InternalErrorException;
use SuperVMar\Shared\Domain\Exception\InvalidValueException;
use SuperVMar\Shared\Domain\Exception\ItemNotFoundException;
use SuperVMar\Shared\Domain\Exception\MandatoryParamsException;

final class ExceptionsTest extends TestCase
{
    public function test_item_not_found_exception_builds_correct_message(): void
    {
        $e = new ItemNotFoundException('Product', ['id' => '123']);

        $this->assertStringContainsString('Product', $e->getMessage());
        $this->assertStringContainsString('123', $e->getMessage());
        $this->assertInstanceOf(\Exception::class, $e);
    }

    public function test_duplicate_item_exception_builds_correct_message(): void
    {
        $e = new DuplicateItemException('User', 'user-uuid-123');

        $this->assertStringContainsString('User', $e->getMessage());
        $this->assertStringContainsString('user-uuid-123', $e->getMessage());
    }

    public function test_cannot_delete_exception_has_default_message(): void
    {
        $e = new CannotDeleteException();

        $this->assertNotEmpty($e->getMessage());
        $this->assertInstanceOf(\Exception::class, $e);
    }

    public function test_cannot_delete_exception_accepts_custom_message(): void
    {
        $e = new CannotDeleteException('User admin cannot be deleted.');

        $this->assertStringContainsString('admin', $e->getMessage());
    }

    public function test_mandatory_params_exception_includes_param_name(): void
    {
        $e = new MandatoryParamsException('username, password');

        $this->assertStringContainsString('username', $e->getMessage());
        $this->assertStringContainsString('password', $e->getMessage());
    }

    public function test_invalid_value_exception_is_throwable(): void
    {
        $e = new InvalidValueException('Test validation error');

        $this->assertSame('Test validation error', $e->getMessage());
    }

    public function test_internal_error_exception_has_default_message(): void
    {
        $e = new InternalErrorException();

        $this->assertNotEmpty($e->getMessage());
    }

    public function test_internal_error_exception_wraps_previous_throwable(): void
    {
        $previous = new \RuntimeException('root cause');
        $e = new InternalErrorException('Wrapped error', $previous);

        $this->assertSame($previous, $e->getPrevious());
    }
}
