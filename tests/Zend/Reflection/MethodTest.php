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
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Reflection_Method
 */
require_once 'Zend/Reflection/Method.php';

/**
 * @see ZF-9018
 */
require_once dirname(__FILE__) . '/_files/ZF9018TestClass.php';

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Method
 */
class Zend_Reflection_MethodTest extends PHPUnit_Framework_TestCase
{

    static protected $_sampleClassFileRequired = false;

    public function setup()
    {
        if (self::$_sampleClassFileRequired === false) {
            $fileToRequire = dirname(__FILE__) . '/_files/TestSampleClass.php';
            require_once $fileToRequire;
            self::$_sampleClassFileRequired = true;
        }
    }

    public function testDeclaringClassReturn()
    {
        $method = new Zend_Reflection_Method('Zend_Reflection_TestSampleClass2', 'getProp1');
        $this->assertEquals(get_class($method->getDeclaringClass()), 'Zend_Reflection_Class');
    }

    public function testParemeterReturn()
    {
        $method = new Zend_Reflection_Method('Zend_Reflection_TestSampleClass2', 'getProp2');
        $parameters = $method->getParameters();
        $this->assertEquals(count($parameters), 2);
        $this->assertEquals(get_class(array_shift($parameters)), 'Zend_Reflection_Parameter');
    }

    public function testStartLine()
    {
        $reflectionMethod = new Zend_Reflection_Method('Zend_Reflection_TestSampleClass5', 'doSomething');

        $this->assertEquals($reflectionMethod->getStartLine(), 106);
        $this->assertEquals($reflectionMethod->getStartLine(true), 90);
    }

    public function testGetBodyReturnsCorrectBody()
    {
        $body = '        //we need a multi-line method body.
        $assigned = 1;
        $alsoAssigined = 2;
        return \'mixedValue\';';
        $reflectionMethod = new Zend_Reflection_Method('Zend_Reflection_TestSampleClass6', 'doSomething');
        $this->assertEquals($body, $reflectionMethod->getBody());
    }

    /**
     * @group ZF-9018
     * @group ZF-9501
     */
    public function testGetBodyReturnsCorrectBodyWhenContentEndsWithClosingCurlyBrace()
    {
        $body = '        if ( true ) {
            echo "True";
        } else {
            echo "False";
        }';
        $reflectionMethod = new Zend_Reflection_Method('ZF9018TestClass', 'doSomething');
        $this->assertEquals($body, $reflectionMethod->getBody());
    }

    /**
     * @group ZF-9018
     * @group ZF-9501
     */
    public function testGetBodyReturnsCorrectBodyWhenMethodWithInlineOpenBraceHasBodyWhichEndsWithClosingCurlyBrace()
    {
        $body = '        if ( true ) {
            echo "True";
        } else {
            echo "False";
        }';
        $reflectionMethod = new Zend_Reflection_Method('ZF9018TestClass', 'doSomethingOpenBraceInline');
        $this->assertEquals($body, $reflectionMethod->getBody());
    }

    public function testGetContentsReturnsCorrectContent()
    {
        $reflectionMethod = new Zend_Reflection_Method('Zend_Reflection_TestSampleClass5', 'doSomething');
        $this->assertEquals("    {\n\n        return 'mixedValue';\n\n    }\n", $reflectionMethod->getContents(false));
    }

    /**
     * @group ZF-10870
     */
    public function testGetBodyReturnsCorrectBodyWhenMethodSignatureIsMultiline()
    {
        $body = '        // FUNKY SIGNATURE';
        $reflectionMethod = new Zend_Reflection_Method('Zend_Reflection_TestSampleClass7', 'bigMethodSignature');
        $this->assertEquals($body, $reflectionMethod->getBody());
    }

    /**
     * @group ZF-10870
     */
    public function testGetBodyReturnsCorrectBodyWhenMethodSignatureAndBodyAreOnSameLine()
    {
        $body = 'return true;';
        $reflectionMethod = new Zend_Reflection_Method('Zend_Reflection_TestSampleClass7', 'testInlineMethod');
        $this->assertEquals($body, $reflectionMethod->getBody());
    }

}

