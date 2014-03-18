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

// Call Zend_Server_Method_CallbackTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Server_Method_CallbackTest::main");
}

/** Zend_Server_Method_Callback */
require_once 'Zend/Server/Method/Callback.php';

/**
 * Test class for Zend_Server_Method_Callback
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Server
 */
class Zend_Server_Method_CallbackTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Server_Method_CallbackTest");
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
        $this->callback = new Zend_Server_Method_Callback();
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

    public function testClassShouldBeNullByDefault()
    {
        $this->assertNull($this->callback->getClass());
    }

    public function testClassShouldBeMutable()
    {
        $this->assertNull($this->callback->getClass());
        $this->callback->setClass('Foo');
        $this->assertEquals('Foo', $this->callback->getClass());
    }

    public function testMethodShouldBeNullByDefault()
    {
        $this->assertNull($this->callback->getMethod());
    }

    public function testMethodShouldBeMutable()
    {
        $this->assertNull($this->callback->getMethod());
        $this->callback->setMethod('foo');
        $this->assertEquals('foo', $this->callback->getMethod());
    }

    public function testFunctionShouldBeNullByDefault()
    {
        $this->assertNull($this->callback->getFunction());
    }

    public function testFunctionShouldBeMutable()
    {
        $this->assertNull($this->callback->getFunction());
        $this->callback->setFunction('foo');
        $this->assertEquals('foo', $this->callback->getFunction());
    }

    public function testTypeShouldBeNullByDefault()
    {
        $this->assertNull($this->callback->getType());
    }

    public function testTypeShouldBeMutable()
    {
        $this->assertNull($this->callback->getType());
        $this->callback->setType('instance');
        $this->assertEquals('instance', $this->callback->getType());
    }

    /**
     * @expectedException Zend_Server_Exception
     */
    public function testSettingTypeShouldThrowExceptionWhenInvalidTypeProvided()
    {
        $this->callback->setType('bogus');
    }

    public function testCallbackShouldSerializeToArray()
    {
        $this->callback->setClass('Foo')
                       ->setMethod('bar')
                       ->setType('instance');
        $test = $this->callback->toArray();
        $this->assertTrue(is_array($test));
        $this->assertEquals('Foo', $test['class']);
        $this->assertEquals('bar', $test['method']);
        $this->assertEquals('instance', $test['type']);
    }

    public function testConstructorShouldSetStateFromOptions()
    {
        $options = array(
            'type'   => 'static',
            'class'  => 'Foo',
            'method' => 'bar',
        );
        $callback = new Zend_Server_Method_Callback($options);
        $test = $callback->toArray();
        $this->assertSame($options, $test);
    }

    public function testSettingFunctionShouldSetTypeAsFunction()
    {
        $this->assertNull($this->callback->getType());
        $this->callback->setFunction('foo');
        $this->assertEquals('function', $this->callback->getType());
    }
}

// Call Zend_Server_Method_CallbackTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Server_Method_CallbackTest::main") {
    Zend_Server_Method_CallbackTest::main();
}
