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
 * @package    Zend_Layout
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_LayoutTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Layout_HelperTest::main");
}

require_once 'Zend/Layout/Controller/Action/Helper/Layout.php';
require_once 'Zend/Layout.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 * Test class for Zend_Layout_Controller_Action_Helper_Layout
 *
 * @category   Zend
 * @package    Zend_Layout
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Layout
 */
class Zend_Layout_HelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Layout_HelperTest");
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
        Zend_Layout_HelperTest_Layout::resetMvcInstance();
        Zend_Controller_Front::getInstance()->resetInstance();
        if (Zend_Controller_Action_HelperBroker::hasHelper('Layout')) {
            Zend_Controller_Action_HelperBroker::removeHelper('Layout');
        }
        if (Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer')) {
            Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
        }
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

    public function testConstructorWithLayoutObject()
    {
        $layout = new Zend_Layout();
        $helper = new Zend_Layout_Controller_Action_Helper_Layout($layout);
        $this->assertSame($layout, $helper->getLayoutInstance());
    }

    public function testGetLayoutCreatesLayoutObjectWhenNoPluginRegistered()
    {
        $helper = new Zend_Layout_Controller_Action_Helper_Layout();
        $layout = $helper->getLayoutInstance();
        $this->assertTrue($layout instanceof Zend_Layout);
    }

    public function testGetLayoutInstancePullsMvcLayoutInstance()
    {
        $layout = Zend_Layout::startMvc();
        $helper = new Zend_Layout_Controller_Action_Helper_Layout();
        $this->assertSame($layout, $helper->getLayoutInstance());
    }

    public function testSetLayoutInstanceReplacesExistingLayoutObject()
    {
        $layout = Zend_Layout::startMvc();
        $helper = new Zend_Layout_Controller_Action_Helper_Layout();
        $this->assertSame($layout, $helper->getLayoutInstance());

        $newLayout = new Zend_Layout();
        $this->assertNotSame($layout, $newLayout);

        $helper->setLayoutInstance($newLayout);
        $this->assertSame($newLayout, $helper->getLayoutInstance());
    }

    public function testDirectFetchesLayoutObject()
    {
        $layout = Zend_Layout::startMvc();
        $helper = new Zend_Layout_Controller_Action_Helper_Layout();

        $received = $helper->direct();
        $this->assertSame($layout, $received);
    }

    public function testHelperProxiesToLayoutObjectMethods()
    {
        $layout = Zend_Layout::startMvc();
        $helper = new Zend_Layout_Controller_Action_Helper_Layout();

        $helper->setOptions(array(
            'layout'     => 'foo.phtml',
            'layoutPath' => dirname(__FILE__) . '/_files/layouts',
            'contentKey' => 'foo'
        ));
        $this->assertEquals('foo.phtml', $helper->getLayout());
        $this->assertEquals(dirname(__FILE__) . '/_files/layouts', $helper->getLayoutPath());
        $this->assertEquals('foo', $helper->getContentKey());
    }
}

/**
 * Zend_Layout extension to allow resetting MVC instance
 */
class Zend_Layout_HelperTest_Layout extends Zend_Layout
{
    public static function resetMvcInstance()
    {
        self::$_mvcInstance = null;
    }
}

// Call Zend_Layout_HelperTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Layout_HelperTest::main") {
    Zend_Layout_HelperTest::main();
}
