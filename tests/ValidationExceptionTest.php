<?php

namespace Jasny;

/**
 * @covers \Jasny\ValidationException
 * @backupStaticAttributes enabled
 */
class ValidationExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        error_reporting(E_ALL ^ E_STRICT);
    }

    /**
     * @expectedException \Jasny\ValidationException
     * @expectedExceptionMessage Validation failed
     */
    public function testThrow()
    {
        $validationResult = ValidationResult::error('some error');
        throw new ValidationException($validationResult);
    }

    public function testGetValidationResult()
    {
        $validationResult = ValidationResult::error('some error');
        $validationException = new ValidationException($validationResult);

        $this->assertSame($validationResult, $validationException->getValidationResult());
    }

    public function testGetError()
    {
        $validationResult = ValidationResult::error('some error');
        $validationException = new ValidationException($validationResult);

        $this->assertEquals('some error', $validationException->getError());
    }

    public function testGetErrors()
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

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testConstructWithoutFailedValidation()
    {
        $validationResult = ValidationResult::success();
        new ValidationException($validationResult);
    }
}
