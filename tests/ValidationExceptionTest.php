<?php

namespace Jasny\Tests;

use Jasny\ValidationException;
use Jasny\ValidationResult;
use PHPUnit\Framework\Attributes\BackupStaticProperties;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[BackupStaticProperties(true)]
#[CoversClass(ValidationException::class)]
class ValidationExceptionTest extends TestCase
{
    public function setUp(): void
    {
        error_reporting(E_ALL ^ E_STRICT);
    }

    public function testThrow(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Validation failed;\n * First error\n * Second error");

        $validationResult = new ValidationResult();
        $validationResult->addError('First error');
        $validationResult->addError('Second error');

        throw new ValidationException($validationResult);
    }

    public function testGetValidationResult(): void
    {
        $validationResult = ValidationResult::error('some error');
        $validationException = new ValidationException($validationResult);

        $this->assertSame($validationResult, $validationException->getValidationResult());
    }

    public function testGetError(): void
    {
        $validationResult = ValidationResult::error('some error');
        $validationException = new ValidationException($validationResult);

        $this->assertEquals('some error', $validationException->getError());
    }

    public function testGetErrors(): void
    {
        $validationResult = new ValidationResult();
        $validationResult->addError('First error');
        $validationResult->addError('Second error');

        $validationException = new ValidationException($validationResult);

        $this->assertEquals([
            'First error',
            'Second error',
        ], $validationException->getErrors());
    }

    public function testError(): void
    {
        $validationException = ValidationException::error('err %s %d %s', 'a', 11, 'b');

        $this->assertInstanceOf(ValidationException::class, $validationException);
        $this->assertEquals('err a 11 b', $validationException->getError());
    }

    public function testConstructWithoutFailedValidation(): void
    {
        $this->expectException(\UnexpectedValueException::class);

        $validationResult = ValidationResult::success();
        new ValidationException($validationResult);
    }
}
