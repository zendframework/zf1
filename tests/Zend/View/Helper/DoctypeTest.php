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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_View_Helper_DoctypeTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_DoctypeTest::main");
}

/** Zend_View_Helper_Doctype */
require_once 'Zend/View/Helper/Doctype.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/**
 * Test class for Zend_View_Helper_Doctype.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_DoctypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_Doctype
     */
    public $helper;

    /**
     * @var string
     */
    public $basePath;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_DoctypeTest");
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
        $regKey = 'Zend_View_Helper_Doctype';
        if (Zend_Registry::isRegistered($regKey)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[$regKey]);
        }
        $this->helper = new Zend_View_Helper_Doctype();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    public function testRegistryEntryCreatedAfterInstantiation()
    {
        $this->assertTrue(Zend_Registry::isRegistered('Zend_View_Helper_Doctype'));
        $doctype = Zend_Registry::get('Zend_View_Helper_Doctype');
        $this->assertTrue($doctype instanceof ArrayObject);
        $this->assertTrue(isset($doctype['doctype']));
        $this->assertTrue(isset($doctype['doctypes']));
        $this->assertTrue(is_array($doctype['doctypes']));
    }

    public function testDoctypeMethodReturnsObjectInstance()
    {
        $doctype = $this->helper->doctype();
        $this->assertTrue($doctype instanceof Zend_View_Helper_Doctype);
    }

    public function testPassingDoctypeSetsDoctype()
    {
        $doctype = $this->helper->doctype('XHTML1_STRICT');
        $this->assertEquals('XHTML1_STRICT', $doctype->getDoctype());
    }

    public function testIsXhtmlReturnsTrueForXhtmlDoctypes()
    {
        $types = array(
            'XHTML1_STRICT',
            'XHTML1_TRANSITIONAL',
            'XHTML1_FRAMESET',
            'XHTML1_RDFA',
            'XHTML1_RDFA11',
            'XHTML5',
        );

        foreach ($types as $type) {
            $doctype = $this->helper->doctype($type);
            $this->assertEquals($type, $doctype->getDoctype());
            $this->assertTrue($doctype->isXhtml());
        }

        $doctype = $this->helper->doctype('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://framework.zend.com/foo/DTD/xhtml1-custom.dtd">');
        $this->assertEquals('CUSTOM_XHTML', $doctype->getDoctype());
        $this->assertTrue($doctype->isXhtml());
    }

    public function testIsXhtmlReturnsFalseForNonXhtmlDoctypes()
    {
        foreach (array('HTML4_STRICT', 'HTML4_LOOSE', 'HTML4_FRAMESET') as $type) {
            $doctype = $this->helper->doctype($type);
            $this->assertEquals($type, $doctype->getDoctype());
            $this->assertFalse($doctype->isXhtml());
        }

        $doctype = $this->helper->doctype('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 10.0 Strict//EN" "http://framework.zend.com/foo/DTD/html10-custom.dtd">');
        $this->assertEquals('CUSTOM', $doctype->getDoctype());
        $this->assertFalse($doctype->isXhtml());
    }

	public function testIsHtml5() {
		foreach (array('HTML5', 'XHTML5') as $type) {
            $doctype = $this->helper->doctype($type);
            $this->assertEquals($type, $doctype->getDoctype());
            $this->assertTrue($doctype->isHtml5());
        }

		foreach (array('HTML4_STRICT', 'HTML4_LOOSE', 'HTML4_FRAMESET', 'XHTML1_STRICT', 'XHTML1_TRANSITIONAL', 'XHTML1_FRAMESET') as $type) {
			$doctype = $this->helper->doctype($type);
            $this->assertEquals($type, $doctype->getDoctype());
            $this->assertFalse($doctype->isHtml5());
		}
	}

    public function testIsRdfa()
    {
        $this->assertTrue($this->helper->doctype('XHTML1_RDFA')->isRdfa());
        $this->assertTrue($this->helper->doctype('XHTML1_RDFA11')->isRdfa());

        // built-in doctypes
        foreach (array('HTML4_STRICT', 'HTML4_LOOSE', 'HTML4_FRAMESET', 'XHTML1_STRICT', 'XHTML1_TRANSITIONAL', 'XHTML1_FRAMESET') as $type) {
            $doctype = $this->helper->doctype($type);
            $this->assertFalse($doctype->isRdfa());
        }

        // custom doctype
        $doctype = $this->helper->doctype('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 10.0 Strict//EN" "http://framework.zend.com/foo/DTD/html10-custom.dtd">');
        $this->assertFalse($doctype->isRdfa());
    }

	public function testCanRegisterCustomHtml5Doctype() {
		$doctype = $this->helper->doctype('<!DOCTYPE html>');
        $this->assertEquals('CUSTOM', $doctype->getDoctype());
        $this->assertTrue($doctype->isHtml5());
	}

    public function testCanRegisterCustomXhtmlDoctype()
    {
        $doctype = $this->helper->doctype('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://framework.zend.com/foo/DTD/xhtml1-custom.dtd">');
        $this->assertEquals('CUSTOM_XHTML', $doctype->getDoctype());
        $this->assertTrue($doctype->isXhtml());
    }

    public function testCanRegisterCustomHtmlDoctype()
    {
        $doctype = $this->helper->doctype('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 10.0 Strict//EN" "http://framework.zend.com/foo/DTD/html10-custom.dtd">');
        $this->assertEquals('CUSTOM', $doctype->getDoctype());
        $this->assertFalse($doctype->isXhtml());
    }

    public function testMalformedCustomDoctypeRaisesException()
    {
        try {
            $doctype = $this->helper->doctype('<!FOO HTML>');
            $this->fail('Malformed doctype should raise exception');
        } catch (Exception $e) {
        }
    }

    public function testStringificationReturnsDoctypeString()
    {
        $doctype  = $this->helper->doctype('XHTML1_STRICT');
        $string   = $doctype->__toString();
        $registry = Zend_Registry::get('Zend_View_Helper_Doctype');
        $this->assertEquals($registry['doctypes']['XHTML1_STRICT'], $string);
    }
}

// Call Zend_View_Helper_DoctypeTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_DoctypeTest::main") {
    Zend_View_Helper_DoctypeTest::main();
}
