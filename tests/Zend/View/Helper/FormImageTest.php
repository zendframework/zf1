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

// Call Zend_View_Helper_FormImageTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_FormImageTest::main");
}

require_once 'Zend/View.php';
require_once 'Zend/View/Helper/FormImage.php';

/**
 * Test class for Zend_View_Helper_FormImage.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_FormImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_FormImageTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->view = new Zend_View();
        $this->view->doctype('HTML4_LOOSE');  // Reset doctype to default
        
        $this->helper = new Zend_View_Helper_FormImage();
        $this->helper->setView($this->view);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    public function testFormImageRendersFormImageXhtml()
    {
        $button = $this->helper->formImage('foo', 'bar');
        $this->assertRegexp('/<input[^>]*?src="bar"/', $button);
        $this->assertRegexp('/<input[^>]*?name="foo"/', $button);
        $this->assertRegexp('/<input[^>]*?type="image"/', $button);
    }

    public function testDisablingFormImageRendersImageInputWithDisableAttribute()
    {
        $button = $this->helper->formImage('foo', 'bar', array('disable' => true));
        $this->assertRegexp('/<input[^>]*?disabled="disabled"/', $button);
        $this->assertRegexp('/<input[^>]*?src="bar"/', $button);
        $this->assertRegexp('/<input[^>]*?name="foo"/', $button);
        $this->assertRegexp('/<input[^>]*?type="image"/', $button);
    }
    
    /**
     * @group ZF-11477
     */
    public function testRendersAsHtmlByDefault()
    {
        $test = $this->helper->formImage(array(
            'name' => 'foo',
        ));
        $this->assertNotContains(' />', $test);
    }

    /**
     * @group ZF-11477
     */
    public function testCanRendersAsXHtml()
    {
        $this->view->doctype('XHTML1_STRICT');
        $test = $this->helper->formImage(array(
            'name' => 'foo',
        ));
        $this->assertContains(' />', $test);
    }
}

// Call Zend_View_Helper_FormImageTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_FormImageTest::main") {
    Zend_View_Helper_FormImageTest::main();
}
