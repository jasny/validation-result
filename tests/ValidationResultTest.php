<?php

namespace Jasny;

/**
 * Tests for Jasny\DB.
 * 
 * @package Test
 * @backupStaticAttributes enabled
 */
class ValidationResultTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        error_reporting(E_ALL ^ E_STRICT);
    }

    public function tearDown()
    {
        ValidationResult::$translate = null;
    }

    public function testAddError()
    {
        $validation = new ValidationResult();
        $validation->addError("Test");
        
        $this->assertEquals(["Test"], $validation->getErrors());
    }

    public function testAddErrorMultiple()
    {
        $validation = new ValidationResult();
        $validation->addError("Foo");
        $validation->addError("Bar");
                
        $this->assertEquals(["Foo", "Bar"], $validation->getErrors());
    }
    
    public function testAddErrorWithArgs()
    {
        $validation = new ValidationResult();
        $validation->addError("Colors %s and %s for %03d", "red", "blue", 20);
        
        $this->assertEquals(["Colors red and blue for 020"], $validation->getErrors());
    }
    

    public function testAdd()
    {
        $validation = new ValidationResult();
        $validation->addError("Qux");
        
        $error = new ValidationResult();
        $error->addError("Foo");
        $error->addError("Bar");
        
        $validation->add($error);
            
        $this->assertEquals(["Qux", "Foo", "Bar"], $validation->getErrors());
    }

    public function testAddWithPrefix()
    {
        $validation = new ValidationResult();
        $validation->addError("Qux");
        
        $error = new ValidationResult();
        $error->addError("Foo");
        $error->addError("Bar");
        
        $validation->add($error, "bad");
            
        $this->assertEquals(["Qux", "bad Foo", "bad Bar"], $validation->getErrors());
    }
    
    
    public function testTranslate()
    {
        $translator = $this->getMockBuilder('stdClass')
            ->setMethods(array('translate'))
            ->getMock();
    
        $translator->expects($this->exactly(2))
            ->method('translate')
            ->withConsecutive([$this->equalTo('Foo')], [$this->equalTo('Bar')])
            ->will($this->onConsecutiveCalls('Red', 'Blue'));
    
        ValidationResult::$translate = [$translator, 'translate'];
    
        $validation = new ValidationResult();
        $validation->addError("Foo");
        $validation->addError("Bar");
                
        $this->assertEquals(["Red", "Blue"], $validation->getErrors());
    }
    
    public function testTranslatePrefix()
    {
        $translator = $this->getMockBuilder('stdClass')
            ->setMethods(array('translate'))
            ->getMock();
    
        $translator->expects($this->exactly(2))
            ->method('translate')
            ->withConsecutive([$this->equalTo('Color %s')], [$this->equalTo('error')])
            ->will($this->onConsecutiveCalls('Colour %s', 'fault'));
    
        ValidationResult::$translate = [$translator, 'translate'];
    
        $error = new ValidationResult();
        $error->addError("Color %s", "red");

        $validation = new ValidationResult();
        $validation->add($error, "error");
        
        $this->assertEquals(["fault Colour red"], $validation->getErrors());
    }
    
    
    public function testSucceeded()
    {
        $validation = new ValidationResult();
        $this->assertTrue($validation->succeeded());
        $this->assertTrue($validation->isSuccess(), 'alias');
                
        $validation->addError("Test");
        $this->assertFalse($validation->succeeded());
        $this->assertFalse($validation->isSuccess(), 'alias');
    }
    
    public function testFailed()
    {
        $validation = new ValidationResult();
        $this->assertFalse($validation->failed());
        
        $validation->addError("Test");
        $this->assertTrue($validation->failed());
    }
    
    
    public function testGetError()
    {
        $validation = new ValidationResult();
        $validation->addError("Foo");
        
        $this->assertEquals("Foo", $validation->getError());
    }

    public function testGetErrorWithMultiple()
    {
        $validation = new ValidationResult();
        $validation->addError("Foo");
        $validation->addError("Bar");

        error_reporting(error_reporting() ^ E_USER_NOTICE);
        $this->assertEquals("Foo", $validation->getError());
    }
    
    /**
     * @expectedException PHPUnit_Framework_Error_Notice
     * @expectedExceptionDescription There are multiple errors, returning only the first
     */
    public function testGetErrorWarningWithMultiple()
    {
        $validation = new ValidationResult();
        $validation->addError("Foo");
        $validation->addError("Bar");
                
        $validation->getError();
    }

    
    public function testSuccess()
    {
        $validation = ValidationResult::success();
        
        $this->assertInstanceOf(ValidationResult::class, $validation);
        $this->assertEquals([], $validation->getErrors());
    }
    
    public function testError()
    {
        $validation = ValidationResult::error("Foo");
        
        $this->assertInstanceOf(ValidationResult::class, $validation);
        $this->assertEquals(["Foo"], $validation->getErrors());
    }
    
    public function testErrorWithArgs()
    {
        $validation = ValidationResult::error("Colors %s and %s for %03d", "red", "blue", 20);
        
        $this->assertInstanceOf(ValidationResult::class, $validation);
        $this->assertEquals(["Colors red and blue for 020"], $validation->getErrors());
    }
}

