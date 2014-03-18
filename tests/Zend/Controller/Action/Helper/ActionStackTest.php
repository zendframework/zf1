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
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Controller_Action_Helper_ActionStackTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Controller_Action_Helper_ActionStackTest::main");
}


require_once 'Zend/Controller/Action/Helper/ActionStack.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Simple.php';

/**
 * Test class for Zend_Controller_Action_Helper_ActionStack.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Action
 * @group      Zend_Controller_Action_Helper
 */
class Zend_Controller_Action_Helper_ActionStackTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Controller_Front
     */
    public $front;

    /**
     * @var Zend_Controller_Request_Http
     */
    public $request;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Controller_Action_Helper_ActionStackTest");
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
        $this->front = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();

        $this->request = new Zend_Controller_Request_Http();
        $this->front->setRequest($this->request);
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

    public function testConstructorInstantiatesPluginIfNotPresent()
    {
        $this->assertFalse($this->front->hasPlugin('Zend_Controller_Plugin_ActionStack'));
        $helper = new Zend_Controller_Action_Helper_ActionStack();
        $this->assertTrue($this->front->hasPlugin('Zend_Controller_Plugin_ActionStack'));
    }

    public function testConstructorUsesExistingPluginWhenPresent()
    {
        $plugin = new Zend_Controller_Plugin_ActionStack();
        $this->front->registerPlugin($plugin);
        $helper = new Zend_Controller_Action_Helper_ActionStack();
        $this->assertTrue($this->front->hasPlugin('Zend_Controller_Plugin_ActionStack'));
        $registered = $this->front->getPlugin('Zend_Controller_Plugin_ActionStack');
        $this->assertSame($plugin, $registered);
    }

    public function testPushStackPushesToPluginStack()
    {
        $helper = new Zend_Controller_Action_Helper_ActionStack();
        $plugin = $this->front->getPlugin('Zend_Controller_Plugin_ActionStack');

        $request = new Zend_Controller_Request_Simple();
        $request->setModuleName('foo')
                ->setControllerName('bar')
                ->setActionName('baz');

        $helper->pushStack($request);

        $next = $plugin->popStack();
        $this->assertTrue($next instanceof Zend_Controller_Request_Abstract);
        $this->assertEquals($request->getModuleName(), $next->getModuleName());
        $this->assertEquals($request->getControllerName(), $next->getControllerName());
        $this->assertEquals($request->getActionName(), $next->getActionName());
        $this->assertFalse($next->isDispatched());
    }

    public function testActionToStackPushesNewRequestToPluginStack()
    {
        $helper = new Zend_Controller_Action_Helper_ActionStack();
        $plugin = $this->front->getPlugin('Zend_Controller_Plugin_ActionStack');

        $helper->actionToStack('baz', 'bar', 'foo');
        $next = $plugin->popStack();
        $this->assertTrue($next instanceof Zend_Controller_Request_Abstract);
        $this->assertEquals('foo', $next->getModuleName());
        $this->assertEquals('bar', $next->getControllerName());
        $this->assertEquals('baz', $next->getActionName());
        $this->assertFalse($next->isDispatched());
    }

    public function testPassingRequestToActionToStackPushesRequestToPluginStack()
    {
        $helper = new Zend_Controller_Action_Helper_ActionStack();
        $plugin = $this->front->getPlugin('Zend_Controller_Plugin_ActionStack');

        $request = new Zend_Controller_Request_Simple();
        $request->setModuleName('foo')
                ->setControllerName('bar')
                ->setActionName('baz');

        $helper->actionToStack($request);

        $next = $plugin->popStack();
        $this->assertTrue($next instanceof Zend_Controller_Request_Abstract);
        $this->assertEquals($request->getModuleName(), $next->getModuleName());
        $this->assertEquals($request->getControllerName(), $next->getControllerName());
        $this->assertEquals($request->getActionName(), $next->getActionName());
        $this->assertFalse($next->isDispatched());
    }

    public function testDirectProxiesToActionToStack()
    {
        $helper = new Zend_Controller_Action_Helper_ActionStack();
        /** FC should be reseted to test ActionStack with a really blank FC */
        $this->front->resetInstance();
        try {
            $helper->direct('baz', 'bar', 'foo');
            $this->fail('Zend_Controller_Action_Exception should be thrown');
        } catch (Zend_Exception $e) {
            $this->assertTrue(
                $e instanceof Zend_Controller_Action_Exception,
                'Zend_Controller_Action_Exception expected, ' . get_class($e)
                    . ' caught'
            );
        }
    }

     public function testCannotStackActionIfNoRequestAvailable()
    {
        $helper = new Zend_Controller_Action_Helper_ActionStack();
        $plugin = $this->front->getPlugin('Zend_Controller_Plugin_ActionStack');

        $helper->direct('baz', 'bar', 'foo');
        $next = $plugin->popStack();
        $this->assertTrue($next instanceof Zend_Controller_Request_Abstract);
        $this->assertEquals('foo', $next->getModuleName());
        $this->assertEquals('bar', $next->getControllerName());
        $this->assertEquals('baz', $next->getActionName());
        $this->assertFalse($next->isDispatched());
    }
}

// Call Zend_Controller_Action_Helper_ActionStackTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Controller_Action_Helper_ActionStackTest::main") {
    Zend_Controller_Action_Helper_ActionStackTest::main();
}
