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

// Call Zend_View_Helper_FormResetTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_FormResetTest::main");
}

require_once 'Zend/View/Helper/FormReset.php';
require_once 'Zend/View.php';
require_once 'Zend/Registry.php';

/**
 * Test class for Zend_View_Helper_FormReset.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_FormResetTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_FormResetTest");
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
        if (Zend_Registry::isRegistered('Zend_View_Helper_Doctype')) {
            $registry = Zend_Registry::getInstance();
            unset($registry['Zend_View_Helper_Doctype']);
        }
        $this->view   = new Zend_View();
        $this->helper = new Zend_View_Helper_FormReset();
        $this->helper->setView($this->view);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper, $this->view);
    }

    public function testShouldRenderResetInput()
    {
        $html = $this->helper->formReset(array(
            'name'    => 'foo',
            'value'   => 'Reset',
        ));
        $this->assertRegexp('/<input[^>]*?(type="reset")/', $html);
    }

    /**
     * @group ZF-2845
     */
    public function testShouldAllowDisabling()
    {
        $html = $this->helper->formReset(array(
            'name'    => 'foo',
            'value'   => 'Reset',
            'attribs' => array('disable' => true)
        ));
        $this->assertRegexp('/<input[^>]*?(disabled="disabled")/', $html);
    }

    public function testShouldRenderAsHtmlByDefault()
    {
        $test = $this->helper->formReset('foo', 'bar');
        $this->assertNotContains(' />', $test);
    }

    public function testShouldAllowRenderingAsXHtml()
    {
        $this->view->doctype('XHTML1_STRICT');
        $test = $this->helper->formReset('foo', 'bar');
        $this->assertContains(' />', $test);
    }
}

// Call Zend_View_Helper_FormResetTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_FormResetTest::main") {
    Zend_View_Helper_FormResetTest::main();
}
