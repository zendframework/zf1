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

// Call Zend_Form_Decorator_ViewHelperTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Decorator_ViewHelperTest::main");
}

require_once 'Zend/Form/Decorator/ViewHelper.php';

require_once 'Zend/Form/Element.php';
require_once 'Zend/Form/Element/Text.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_Form_Decorator_ViewHelper
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class Zend_Form_Decorator_ViewHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Decorator_ViewHelperTest");
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
        $this->decorator = new Zend_Form_Decorator_ViewHelper();
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
        return $view;
    }

    public function getElement()
    {
        $element = new Zend_Form_Element_Text('foo');
        $this->decorator->setElement($element);
        return $element;
    }

    public function testGetHelperWillUseElementHelperAttributeInAbsenceOfHelper()
    {
        $element = new Zend_Form_Element('foo');
        $element->helper = 'formTextarea';
        $this->decorator->setElement($element);
        $this->assertEquals('formTextarea', $this->decorator->getHelper());
    }

    public function testGetHelperWillUseElementTypeInAbsenceOfHelper()
    {
        $element = new Zend_Form_Decorator_ViewHelperTest_Textarea('foo');
        $this->decorator->setElement($element);
        $this->assertEquals('formTextarea', $this->decorator->getHelper());
    }

    public function testGetHelperWillUseHelperProvidedInOptions()
    {
        $this->decorator->setOptions(array('helper' => 'formSubmit'));
        $this->assertEquals('formSubmit', $this->decorator->getHelper());
    }

    public function testGetHelperReturnsNullByDefault()
    {
        $this->assertNull($this->decorator->getHelper());
    }

    public function testCanSetHelper()
    {
        $this->decorator->setHelper('formSubmit');
        $this->assertEquals('formSubmit', $this->decorator->getHelper());
    }

    public function testAppendsBracketsIfElementIsAnArray()
    {
        $element = $this->getElement();
        $element->setIsArray(true);
        $name = $this->decorator->getName();
        $expect = $element->getName() . '[]';
        $this->assertEquals($expect, $name);
    }

    public function testRenderThrowsExceptionIfNoViewSetInElement()
    {
        $element = $this->getElement();
        $content = 'test content';
        try {
            $test = $this->decorator->render($content);
            $this->fail('Render should raise exception without view');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('ViewHelper decorator cannot render', $e->getMessage());
        }
    }

    public function testRenderRendersElementWithSpecifiedHelper()
    {
        $element = $this->getElement();
        $element->setView($this->getView());
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertContains($content, $test);
        $this->assertRegexp('#<input.*?name="foo"#s', $test);
    }

    public function testMultiOptionsPassedToViewHelperAreTranslated()
    {
        require_once 'Zend/Form/Element/Select.php';
        require_once 'Zend/Translate.php';
        $element = new Zend_Form_Element_Select('foo');
        $options = array(
            'foo' => 'This Foo Will Not Be Displayed',
            'bar' => 'This Bar Will Not Be Displayed',
            'baz' => 'This Baz Will Not Be Displayed',
        );
        $element->setMultiOptions($options);

        $translations = array(
            'This Foo Will Not Be Displayed' => 'This is the Foo Value',
            'This Bar Will Not Be Displayed' => 'This is the Bar Value',
            'This Baz Will Not Be Displayed' => 'This is the Baz Value',
        );
        $translate = new Zend_Translate('array', $translations, 'en');
        $translate->setLocale('en');

        $element->setTranslator($translate);
        $test = $element->render($this->getView());
        foreach ($options as $key => $value) {
            $this->assertNotContains($value, $test);
            $this->assertContains($translations[$value], $test);
        }
    }
    
    /**
     * @group ZF-9689
     */
    public function testRenderWithListSeparatorForMulticheckbox()
    {
        require_once 'Zend/Form/Element/MultiCheckbox.php';
        
        $element = new Zend_Form_Element_MultiCheckbox('foo');
        $options = array(
            'foo' => 'Foo',
            'bar' => 'Bar',
        );
        $element->setMultiOptions($options);
        $element->setSeparator('</p><p>');
        $element->setDecorators(
            array(
                array('ViewHelper', array('separator' => '')),
                array('HtmlTag', array('tag' => 'p')),
            )
        );
        
        $expected = '<p><label><input type="checkbox" name="foo[]" id="foo-foo" value="foo">Foo</label></p>'
                  . '<p><label><input type="checkbox" name="foo[]" id="foo-bar" value="bar">Bar</label></p>';
        $actual   = $element->render($this->getView());
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @group ZF-9689
     */
    public function testRenderWithListSeparatorForRadio()
    {
        require_once 'Zend/Form/Element/Radio.php';
        
        $element = new Zend_Form_Element_Radio('foo');
        $options = array(
            'foo' => 'Foo',
            'bar' => 'Bar',
        );
        $element->setMultiOptions($options);
        $element->setSeparator('</p><p>');
        $element->setDecorators(
            array(
                array('ViewHelper', array('separator' => '')),
                array('HtmlTag', array('tag' => 'p')),
            )
        );
        
        $expected = '<p><label><input type="radio" name="foo" id="foo-foo" value="foo">Foo</label></p>'
                  . '<p><label><input type="radio" name="foo" id="foo-bar" value="bar">Bar</label></p>';
        $actual   = $element->render($this->getView());
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group ZF-5056
     */
    public function testRenderingButtonWithValue()
    {
        // Create element
        require_once 'Zend/Form/Element/Button.php';

        $element = new Zend_Form_Element_Button('foo');
        $element->setValue('bar');
        $element->setLabel('baz');
        $element->setDecorators(
            array(
                 'ViewHelper',
            )
        );

        // Test
        $this->assertEquals(
            PHP_EOL
                . '<button name="foo" id="foo" type="button" value="bar">baz</button>',
            $element->render($this->getView())
        );
    }

    /**
     * @group ZF-5056
     */
    public function testRenderingButtonAsTypeSubmit()
    {
        // Create element
        require_once 'Zend/Form/Element/Button.php';

        $element = new Zend_Form_Element_Button('foo');
        $element->setAttrib('type', 'submit');
        $element->setDecorators(
            array(
                 'ViewHelper',
            )
        );

        // Test
        $this->assertEquals(
            PHP_EOL . '<button name="foo" id="foo" type="submit">foo</button>',
            $element->render($this->getView())
        );
    }
}

class Zend_Form_Decorator_ViewHelperTest_Textarea extends Zend_Form_Element
{
    public function __construct($name, $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
        $this->helper = null;
    }
}

// Call Zend_Form_Decorator_ViewHelperTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Decorator_ViewHelperTest::main") {
    Zend_Form_Decorator_ViewHelperTest::main();
}
