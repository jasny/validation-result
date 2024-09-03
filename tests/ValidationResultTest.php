<?php

namespace Jasny\Tests;

use Jasny\PHPUnit\CallbackMockTrait;
use Jasny\PHPUnit\ConsecutiveTrait;
use Jasny\PHPUnit\ExpectWarningTrait;
use Jasny\ValidationException;
use Jasny\ValidationResult;
use PHPUnit\Framework\Attributes\BackupStaticProperties;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\TestCase;

#[BackupStaticProperties(true)]
#[CoversClass(ValidationResult::class)]
class ValidationResultTest extends TestCase
{
    use CallbackMockTrait;
    use ConsecutiveTrait;
    use ExpectWarningTrait;

    public function tearDown(): void
    {
        ValidationResult::$translate = null;
    }

    public function testAddError(): void
    {
        $validation = new ValidationResult();
        $validation->addError("Test");

        $this->assertEquals(["Test"], $validation->getErrors());
    }

    public function testAddErrorMultiple(): void
    {
        $validation = new ValidationResult();
        $validation->addError("Foo");
        $validation->addError("Bar");

        $this->assertEquals(["Foo", "Bar"], $validation->getErrors());
    }

    public function testAddErrorWithArgs(): void
    {
        $validation = new ValidationResult();
        $validation->addError("Colors %s and %s for %03d", "red", "blue", 20);

        $this->assertEquals(["Colors red and blue for 020"], $validation->getErrors());
    }


    public function testAdd(): void
    {
        $validation = new ValidationResult();
        $validation->addError("Qux");

        $error = new ValidationResult();
        $error->addError("Foo");
        $error->addError("Bar");

        $validation->add($error);

        $this->assertEquals(["Qux", "Foo", "Bar"], $validation->getErrors());
    }

    public function testAddWithPrefix(): void
    {
        $validation = new ValidationResult();
        $validation->addError("Qux");

        $error = new ValidationResult();
        $error->addError("Foo");
        $error->addError("Bar");

        $validation->add($error, "bad");

        $this->assertEquals(["Qux", "bad Foo", "bad Bar"], $validation->getErrors());
    }


    public function testTranslate(): void
    {
        $translator = $this->createCallbackMock($this->exactly(2), function (InvocationMocker $invoke) {
            $invoke
                ->with(...$this->consecutive(['Foo'], ['Bar']))
                ->willReturnOnConsecutiveCalls('Red', 'Blue');
        });

        ValidationResult::$translate = $translator;

        $validation = new ValidationResult();
        $validation->addError("Foo");
        $validation->addError("Bar");

        $this->assertEquals(["Red", "Blue"], $validation->getErrors());
    }

    public function testTranslatePrefix(): void
    {
        $translator = $this->createCallbackMock($this->exactly(2), function (InvocationMocker $invoke) {
            $invoke
                ->with(...$this->consecutive(['Color %s'], ['error']))
                ->willReturnOnConsecutiveCalls('Colour %s', 'fault');
        });

        ValidationResult::$translate = $translator;

        $error = new ValidationResult();
        $error->addError("Color %s", "red");

        $validation = new ValidationResult();
        $validation->add($error, "error");

        $this->assertEquals(["fault Colour red"], $validation->getErrors());
    }


    public function testSucceeded(): void
    {
        $validation = new ValidationResult();
        $this->assertTrue($validation->succeeded());
        $this->assertTrue($validation->isSuccess(), 'alias');

        $validation->addError("Test");
        $this->assertFalse($validation->succeeded());
        $this->assertFalse($validation->isSuccess(), 'alias');
    }

    public function testFailed(): void
    {
        $validation = new ValidationResult();
        $this->assertFalse($validation->failed());

        $validation->addError("Test");
        $this->assertTrue($validation->failed());
    }


    public function testGetError(): void
    {
        $validation = new ValidationResult();
        $validation->addError("Foo");

        $this->assertEquals("Foo", $validation->getError());
    }

    public function testGetErrorWithMultiple(): void
    {
        $this->expectNoticeMessage("There are multiple errors, returning only the first");

        $validation = new ValidationResult();
        $validation->addError("Foo");
        $validation->addError("Bar");

        $validation->getError();
    }

    public function testMustSucceedForSuccess(): void
    {
        $validation = new ValidationResult();
        $validation->mustSucceed();

        $this->assertTrue(true, 'No exception was thrown');
    }

    public function testMustSucceedForFailed(): void
    {
        $validation = new ValidationResult();
        $validation->addError("Foo");

        try {
            $validation->mustSucceed();
        } catch (ValidationException $exception) {
            $this->assertSame($validation, $exception->getValidationResult());
            return;
        }

        $this->fail("No validation exception was thrown");
    }


    public function testSuccess(): void
    {
        $validation = ValidationResult::success();

        $this->assertInstanceOf(ValidationResult::class, $validation);
        $this->assertEquals([], $validation->getErrors());
    }

    public function testError(): void
    {
        $validation = ValidationResult::error("Foo");

        $this->assertInstanceOf(ValidationResult::class, $validation);
        $this->assertEquals(["Foo"], $validation->getErrors());
    }

    public function testErrorWithArgs(): void
    {
        $validation = ValidationResult::error("Colors %s and %s for %03d", "red", "blue", 20);

        $this->assertInstanceOf(ValidationResult::class, $validation);
        $this->assertEquals(["Colors red and blue for 020"], $validation->getErrors());
    }
}
