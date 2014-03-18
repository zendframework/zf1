<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Server_Method_ParameterTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Server_Method_ParameterTest::main");
}

/** Zend_Server_Method_Parameter */
require_once 'Zend/Server/Method/Parameter.php';

/**
 * Test class for Zend_Server_Method_Parameter
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Server
 */
class Zend_Server_Method_ParameterTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Server_Method_ParameterTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->parameter = new Zend_Server_Method_Parameter();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testDefaultValueShouldBeNullByDefault()
    {
        $this->assertNull($this->parameter->getDefaultValue());
    }

    public function testDefaultValueShouldBeMutable()
    {
        $this->assertNull($this->parameter->getDefaultValue());
        $this->parameter->setDefaultValue('foo');
        $this->assertEquals('foo', $this->parameter->getDefaultValue());
    }

    public function testDescriptionShouldBeEmptyStringByDefault()
    {
        $this->assertSame('', $this->parameter->getDescription());
    }

    public function testDescriptionShouldBeMutable()
    {
        $message = 'This is a description';
        $this->assertSame('', $this->parameter->getDescription());
        $this->parameter->setDescription($message);
        $this->assertEquals($message, $this->parameter->getDescription());
    }

    public function testSettingDescriptionShouldCastToString()
    {
        $message = 123456;
        $this->parameter->setDescription($message);
        $test = $this->parameter->getDescription();
        $this->assertNotSame($message, $test);
        $this->assertEquals($message, $test);
    }

    public function testNameShouldBeNullByDefault()
    {
        $this->assertNull($this->parameter->getName());
    }

    public function testNameShouldBeMutable()
    {
        $name = 'foo';
        $this->assertNull($this->parameter->getName());
        $this->parameter->setName($name);
        $this->assertEquals($name, $this->parameter->getName());
    }

    public function testSettingNameShouldCastToString()
    {
        $name = 123456;
        $this->parameter->setName($name);
        $test = $this->parameter->getName();
        $this->assertNotSame($name, $test);
        $this->assertEquals($name, $test);
    }

    public function testParameterShouldBeRequiredByDefault()
    {
        $this->assertFalse($this->parameter->isOptional());
    }

    public function testParameterShouldAllowBeingOptional()
    {
        $this->assertFalse($this->parameter->isOptional());
        $this->parameter->setOptional(true);
        $this->assertTrue($this->parameter->isOptional());
    }

    public function testTypeShouldBeMixedByDefault()
    {
        $this->assertEquals('mixed', $this->parameter->getType());
    }

    public function testTypeShouldBeMutable()
    {
        $type = 'string';
        $this->assertEquals('mixed', $this->parameter->getType());
        $this->parameter->setType($type);
        $this->assertEquals($type, $this->parameter->getType());
    }

    public function testSettingTypeShouldCastToString()
    {
        $type = 123456;
        $this->parameter->setType($type);
        $test = $this->parameter->getType();
        $this->assertNotSame($type, $test);
        $this->assertEquals($type, $test);
    }

    public function testParameterShouldSerializeToArray()
    {
        $type         = 'string';
        $name         = 'foo';
        $optional     = true;
        $defaultValue = 'bar';
        $description  = 'Foo bar!';
        $parameter    = compact('type', 'name', 'optional', 'defaultValue', 'description');
        $this->parameter->setType($type)
                        ->setName($name)
                        ->setOptional($optional)
                        ->setDefaultValue($defaultValue)
                        ->setDescription($description);
        $test = $this->parameter->toArray();
        $this->assertEquals($parameter, $test);
    }

    public function testConstructorShouldSetObjectStateFromPassedOptions()
    {
        $type         = 'string';
        $name         = 'foo';
        $optional     = true;
        $defaultValue = 'bar';
        $description  = 'Foo bar!';
        $options      = compact('type', 'name', 'optional', 'defaultValue', 'description');
        $parameter    = new Zend_Server_Method_Parameter($options);
        $test         = $parameter->toArray();
        $this->assertEquals($options, $test);
    }
}

// Call Zend_Server_Method_ParameterTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Server_Method_ParameterTest::main") {
    Zend_Server_Method_ParameterTest::main();
}
