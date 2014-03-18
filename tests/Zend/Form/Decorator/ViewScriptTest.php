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
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Form_Decorator_ViewScriptTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Decorator_ViewScriptTest::main");
}

require_once 'Zend/Form/Decorator/ViewScript.php';

require_once 'Zend/Form/Element.php';
require_once 'Zend/Form/Element/Text.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_Form_Decorator_ViewScript
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class Zend_Form_Decorator_ViewScriptTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Decorator_ViewScriptTest");
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
        $this->decorator = new Zend_Form_Decorator_ViewScript();
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

    public function getView()
    {
        $view = new Zend_View();
        $view->addHelperPath(dirname(__FILE__) . '/../../../../library/Zend/View/Helper');
        $view->addScriptPath(dirname(__FILE__) . '/../_files/views/');
        return $view;
    }

    public function getElement()
    {
        $element = new Zend_Form_Element_Text('foo');
        $element->setView($this->getView());
        $this->decorator->setElement($element);
        return $element;
    }

    public function testRenderRaisesExceptionIfNoViewScriptRegistered()
    {
        $this->getElement();
        try {
            $this->decorator->render('');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('script', $e->getMessage());
        }
    }

    public function testViewScriptNullByDefault()
    {
        $this->assertNull($this->decorator->getViewScript());
    }

    public function testCanSetViewScript()
    {
        $this->testViewScriptNullByDefault();
        $this->decorator->setViewScript('decorator.phtml');
        $this->assertEquals('decorator.phtml', $this->decorator->getViewScript());
    }

    public function testCanSetViewScriptViaOption()
    {
        $this->testViewScriptNullByDefault();
        $this->decorator->setOption('viewScript', 'decorator.phtml');
        $this->assertEquals('decorator.phtml', $this->decorator->getViewScript());
    }

    public function testCanSetViewScriptViaElementAttribute()
    {
        $this->testViewScriptNullByDefault();
        $this->getElement()->setAttrib('viewScript', 'decorator.phtml');
        $this->assertEquals('decorator.phtml', $this->decorator->getViewScript());
    }

    public function testCanSetViewModule()
    {
        $this->testViewScriptNullByDefault();
        $this->decorator->setViewModule('fooModule');
        $this->assertEquals('fooModule', $this->decorator->getViewModule());
    }

    public function testCanSetViewModuleViaOption()
    {
        $this->testViewScriptNullByDefault();
        $this->decorator->setOption('viewModule', 'fooModule');
        $this->assertEquals('fooModule', $this->decorator->getViewModule());
    }

    public function testCanSetViewModuleViaElementAttribute()
    {
        $this->testViewScriptNullByDefault();
        $this->getElement()->setAttrib('viewModule', 'fooModule');
        $this->assertEquals('fooModule', $this->decorator->getViewModule());
    }

    public function testRenderingRendersViewScript()
    {
        $this->testCanSetViewScriptViaElementAttribute();
        $test = $this->decorator->render('');
        $this->assertContains('This is content from the view script', $test);
    }

    public function testRenderingRendersViewScriptWithModule()
    {
        $this->testCanSetViewScriptViaElementAttribute();

        $module = 'fooModule';

        // add module to front controller so partial view helper can verify it exists
        require_once 'Zend/Controller/Front.php';
        Zend_Controller_Front::getInstance()->addControllerDirectory('', $module);

        $this->getElement()->setAttrib('viewModule', $module);
        $test = $this->decorator->render('');
        $this->assertContains('This is content from the view script', $test);
    }

    public function testOptionsArePassedToPartialAsVariables()
    {
        $this->decorator->setOptions(array(
            'foo'        => 'Foo Value',
            'bar'        => 'Bar Value',
            'baz'        => 'Baz Value',
            'bat'        => 'Bat Value',
            'viewScript' => 'decorator.phtml',
        ));
        $this->getElement();
        $test = $this->decorator->render('');
        foreach ($this->decorator->getOptions() as $key => $value) {
            $this->assertContains("$key: $value", $test);
        }
    }

    public function testCanReplaceContentBySpecifyingFalsePlacement()
    {
        $this->decorator->setViewScript('replacingDecorator.phtml')
             ->setOption('placement', false)
             ->setElement($this->getElement());
        $test = $this->decorator->render('content to decorate');
        $this->assertNotContains('content to decorate', $test, $test);
        $this->assertContains('This is content from the view script', $test);
    }

    public function testContentCanBeRenderedWithinViewScript()
    {
        $this->decorator->setViewScript('contentWrappingDecorator.phtml')
             ->setOption('placement', false)
             ->setElement($this->getElement());

        $test = $this->decorator->render('content to decorate');
        $this->assertContains('content to decorate', $test, $test);
        $this->assertContains('This text prefixes the content', $test);
        $this->assertContains('This text appends the content', $test);
    }

    public function testDecoratorCanControlPlacementFromWithinViewScript()
    {
        $this->decorator->setViewScript('decoratorCausesReplacement.phtml')
             ->setElement($this->getElement());

        $test = $this->decorator->render('content to decorate');
        $this->assertContains('content to decorate', $test, $test);

        $count = substr_count($test, 'content to decorate');
        $this->assertEquals(1, $count);

        $this->assertContains('This text prefixes the content', $test);
        $this->assertContains('This text appends the content', $test);
    }

    /**
     * @group ZF-6061
     */
    public function testRenderingWithoutHelperInAttribs()
    {
        $this->decorator->setViewScript('withouthelperinattribs.phtml')
            ->setElement($this->getElement());

        $expected = 'Foo:'
                  . PHP_EOL
                  . '<input type="text" name="foo" id="foo" value="">';

        $this->assertSame($expected, $this->decorator->render('Foo:'));
    }
}

// Call Zend_Form_Decorator_ViewScriptTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Decorator_ViewScriptTest::main") {
    Zend_Form_Decorator_ViewScriptTest::main();
}
