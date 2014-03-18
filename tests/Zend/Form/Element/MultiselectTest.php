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

// Call Zend_Form_Element_MultiselectTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Element_MultiselectTest::main");
}


require_once 'Zend/Form/Element/Multiselect.php';
require_once 'Zend/Translate.php';

/**
 * Test class for Zend_Form_Element_Multiselect
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class Zend_Form_Element_MultiselectTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Element_MultiselectTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * @var Zend_Form_Element_Multiselect
     */
    public $element;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->element = new Zend_Form_Element_Multiselect('foo');
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
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $view->addHelperPath(dirname(__FILE__) . '/../../../../library/Zend/View/Helper/');
        return $view;
    }

    public function testMultiselectElementInstanceOfMultiElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Multi);
    }

    public function testMultiselectElementInstanceOfXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testMultiselectElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testMultiselectElementIsAnArrayByDefault()
    {
        $this->assertTrue($this->element->isArray());
    }

    public function testMultiselectElementUsesSelectHelperInViewHelperDecoratorByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formSelect', $helper);
    }

    public function testMultipleOptionSetByDefault()
    {
        $this->assertNotNull($this->element->multiple);
        $this->assertEquals('multiple', $this->element->multiple);
    }

    public function testHasDefaultSeparator()
    {
        $this->assertEquals('<br />', $this->element->getSeparator());
    }

    public function testCanSetSeparator()
    {
        $this->testHasDefaultSeparator();
        $this->element->setSeparator("\n");
        $this->assertEquals("\n", $this->element->getSeparator());
    }

    public function testMultiOptionsEmptyByDefault()
    {
        $options = $this->element->getMultiOptions();
        $this->assertTrue(is_array($options));
        $this->assertTrue(empty($options));
    }

    public function testCanSetMultiOptions()
    {
        $this->testMultiOptionsEmptyByDefault();
        $this->element->addMultiOption('foo', 'foovalue');
        $this->assertEquals('foovalue', $this->element->getMultiOption('foo'));
        $this->element->setMultiOptions(array('bar' => 'barvalue', 'baz' => 'bazvalue'));
        $this->assertEquals(array('bar' => 'barvalue', 'baz' => 'bazvalue'), $this->element->getMultiOptions());
        $this->element->addMultiOptions(array('bat' => 'batvalue', 'foo' => 'foovalue'));
        $this->assertEquals(array('bar' => 'barvalue', 'baz' => 'bazvalue', 'bat' => 'batvalue', 'foo' => 'foovalue'), $this->element->getMultiOptions());
        $this->element->addMultiOption('test', 'testvalue');
        $this->assertEquals(array('bar' => 'barvalue', 'baz' => 'bazvalue', 'bat' => 'batvalue', 'foo' => 'foovalue', 'test' => 'testvalue'), $this->element->getMultiOptions());
    }

    /**
     * @group ZF-2824
     */
    public function testCanSetMultiOptionsUsingAssocArraysWithKeyValueKeys()
    {
        $options = array(
            array(
                'value' => '1',
                'key'   => 'aa',
            ),
            array (
                'key'   => '2',
                'value' => 'xxxx',
            ),
            array (
                'value' => '444',
                'key'   => 'ssss',
            ),
        );
        $this->element->addMultiOptions($options);
        $this->assertEquals($options[0]['value'], $this->element->getMultiOption('aa'));
        $this->assertEquals($options[1]['value'], $this->element->getMultiOption(2));
        $this->assertEquals($options[2]['value'], $this->element->getMultiOption('ssss'));
    }

    /**
     * @group ZF-2824
     */
    public function testCanSetMultiOptionsUsingConfigWithKeyValueKeys()
    {
        require_once 'Zend/Config/Xml.php';
        $config = new Zend_Config_Xml(dirname(__FILE__) . '/../_files/config/multiOptions.xml', 'testing');
        $this->element->setMultiOptions($config->options->toArray());
        $this->assertEquals($config->options->first->value, $this->element->getMultiOption('aa'));
        $this->assertEquals($config->options->second->value, $this->element->getMultiOption(2));
        $this->assertEquals($config->options->third->value, $this->element->getMultiOption('ssss'));

        require_once 'Zend/Config/Ini.php';
        $config = new Zend_Config_Ini(dirname(__FILE__) . '/../_files/config/multiOptions.ini', 'testing');
        $this->element->setMultiOptions($config->options->toArray());
        $this->assertEquals($config->options->first->value, $this->element->getMultiOption('aa'));
        $this->assertEquals($config->options->second->value, $this->element->getMultiOption(2));
        $this->assertEquals($config->options->third->value, $this->element->getMultiOption('ssss'));

    }

    public function testCanRemoveMultiOption()
    {
        $this->testMultiOptionsEmptyByDefault();
        $this->element->addMultiOption('foo', 'foovalue');
        $this->assertEquals('foovalue', $this->element->getMultiOption('foo'));
        $this->element->removeMultiOption('foo');
        $this->assertNull($this->element->getMultiOption('foo'));
    }

    public function testOptionsAreRenderedInFinalMarkup()
    {
        $options = array(
            'foovalue' => 'Foo',
            'barvalue' => 'Bar'
        );
        $this->element->addMultiOptions($options);
        $html = $this->element->render($this->getView());
        foreach ($options as $value => $label) {
            $this->assertRegexp('/<option.*value="' . $value . '"[^>]*>' . $label . '/s', $html, $html);
        }
    }

    public function testTranslatedOptionsAreRenderedInFinalMarkupWhenTranslatorPresent()
    {
        $translations = array(
            'ThisShouldNotShow'   => 'Foo Value',
            'ThisShouldNeverShow' => 'Bar Value'
        );
        require_once 'Zend/Translate.php';
        $translate = new Zend_Translate('array', $translations, 'en');
        $translate->setLocale('en');

        $options = array(
            'foovalue' => 'ThisShouldNotShow',
            'barvalue' => 'ThisShouldNeverShow'
        );

        $this->element->setTranslator($translate)
                      ->addMultiOptions($options);

        $html = $this->element->render($this->getView());
        foreach ($options as $value => $label) {
            $this->assertNotContains($label, $html, $html);
            $this->assertRegexp('/<option.*value="' . $value . '"[^>]*>' . $translations[$label] . '/s', $html, $html);
        }
    }

    public function testOptionLabelsAreTranslatedWhenTranslateAdapterIsPresent()
    {
        $translations = include dirname(__FILE__) . '/../_files/locale/array.php';
        $translate    = new Zend_Translate('array', $translations, 'en');
        $translate->setLocale('en');

        $options = array(
            'foovalue' => 'Foo',
            'barvalue' => 'Bar'
        );
        $this->element->addMultiOptions($options)
                      ->setTranslator($translate);
        $test = $this->element->getMultiOption('barvalue');
        $this->assertEquals($translations[$options['barvalue']], $test);

        $test = $this->element->getMultiOptions();
        foreach ($test as $key => $value) {
            $this->assertEquals($translations[$options[$key]], $value);
        }
    }

    public function testOptionLabelsAreUntouchedIfTranslatonDoesNotExistInnTranslateAdapter()
    {
        $translations = include dirname(__FILE__) . '/../_files/locale/array.php';
        $translate    = new Zend_Translate('array', $translations, 'en');
        $translate->setLocale('en');

        $options = array(
            'foovalue' => 'Foo',
            'barvalue' => 'Bar',
            'testing'  => 'Test Value',
        );
        $this->element->addMultiOptions($options)
                      ->setTranslator($translate);
        $test = $this->element->getMultiOption('testing');
        $this->assertEquals($options['testing'], $test);
    }

    public function testMultiselectIsArrayByDefault()
    {
        $this->assertTrue($this->element->isArray());
    }

    /**
     * @group ZF-5568
     */
    public function testOptGroupTranslationsShouldWorkAfterPopulatingElement()
    {
        $translations = array(
            'ThisIsTheLabel'      => 'Optgroup label',
            'ThisShouldNotShow'   => 'Foo Value',
            'ThisShouldNeverShow' => 'Bar Value'
        );
        require_once 'Zend/Translate.php';
        $translate = new Zend_Translate('array', $translations, 'en');
        $translate->setLocale('en');

        $options = array(
            'ThisIsTheLabel' => array(
                'foovalue' => 'ThisShouldNotShow',
                'barvalue' => 'ThisShouldNeverShow',
            ),
        );

        $this->element->setTranslator($translate)
                      ->addMultiOptions($options);

        $this->element->setValue('barValue');

        $html = $this->element->render($this->getView());
        $this->assertContains($translations['ThisIsTheLabel'], $html, $html);
    }

    /**
     * @group ZF-5937
     */
    public function testAddMultiOptionShouldWorkAfterTranslatorIsDisabled()
    {
        $options = array(
            'foovalue' => 'Foo',
        );
        $this->element->setDisableTranslator(true)
                      ->addMultiOptions($options);
        $test = $this->element->getMultiOption('foovalue');
        $this->assertEquals($options['foovalue'], $test);
    }

    /**
     * Used by test methods susceptible to ZF-2794, marks a test as incomplete
     *
     * @link   http://framework.zend.com/issues/browse/ZF-2794
     * @return void
     */
    protected function _checkZf2794()
    {
        if (strtolower(substr(PHP_OS, 0, 3)) == 'win' && version_compare(PHP_VERSION, '5.1.4', '=')) {
            $this->markTestIncomplete('Error occurs for PHP 5.1.4 on Windows');
        }
    }

    /**
     * @group ZF-11667
     */
    public function testSimilarErrorMessagesForMultiElementAreNotDuplicated()
    {
        $this->element->setConcatJustValuesInErrorMessage(true);

        // create element with 4 checkboxes
        $this->element->setMultiOptions(array(
            'multiOptions' => array(
                array('key' => 'a', 'value' => 'A'),
                array('key' => 'b', 'value' => 'B'),
                array('key' => 'c', 'value' => 'C'),
                array('key' => 'd', 'value' => 'D'),
            )
        ));

        // check 3 of them
        $this->element->setValue(array('A', 'B', 'D'));

        // later on, fails some validation on submit
        $this->element->addError('some error! %value%');

        $this->assertEquals(
            array('some error! A; B; D'),
            $this->element->getMessages()
        );
    }
}

// Call Zend_Form_Element_MultiselectTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Element_MultiselectTest::main") {
    Zend_Form_Element_MultiselectTest::main();
}
