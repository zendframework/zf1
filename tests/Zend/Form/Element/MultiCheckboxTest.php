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

// Call Zend_Form_Element_MultiCheckboxTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Element_MultiCheckboxTest::main");
}

require_once 'Zend/Form/Element/MultiCheckbox.php';

/**
 * Test class for Zend_Form_Element_MultiCheckbox
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class Zend_Form_Element_MultiCheckboxTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Element_MultiCheckboxTest");
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
        $this->element = new Zend_Form_Element_MultiCheckbox('foo');
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
        $view->addHelperPath(dirname(__FILE__) . '/../../../../library/Zend/View/Helper');
        return $view;
    }

    public function testMultiCheckboxElementSubclassesMultiElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Multi);
    }

    public function testMultiCheckboxElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testMultiCheckboxElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testMultiCheckboxElementIsAnArrayByDefault()
    {
        $this->assertTrue($this->element->isArray());
    }

    public function testHelperAttributeSetToFormMultiCheckboxByDefault()
    {
        $this->assertEquals('formMultiCheckbox', $this->element->getAttrib('helper'));
    }

    public function testMultiCheckboxElementUsesMultiCheckboxHelperInViewHelperDecoratorByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formMultiCheckbox', $helper);
    }

    public function testCanDisableIndividualMultiCheckboxOptions()
    {
        $this->element->setMultiOptions(array(
                'foo'  => 'Foo',
                'bar'  => 'Bar',
                'baz'  => 'Baz',
                'bat'  => 'Bat',
                'test' => 'Test',
            ))
            ->setAttrib('disable', array('baz', 'test'));
        $html = $this->element->render($this->getView());
        foreach (array('baz', 'test') as $test) {
            if (!preg_match('/(<input[^>]*?(value="' . $test . '")[^>]*>)/', $html, $m)) {
                $this->fail('Unable to find matching disabled option for ' . $test);
            }
            $this->assertRegexp('/<input[^>]*?(disabled="disabled")/', $m[1]);
        }
        foreach (array('foo', 'bar', 'bat') as $test) {
            if (!preg_match('/(<input[^>]*?(value="' . $test . '")[^>]*>)/', $html, $m)) {
                $this->fail('Unable to find matching option for ' . $test);
            }
            $this->assertNotRegexp('/<input[^>]*?(disabled="disabled")/', $m[1], var_export($m, 1));
        }
    }

    public function testSpecifiedSeparatorIsUsedWhenRendering()
    {
        $this->element->setMultiOptions(array(
                'foo'  => 'Foo',
                'bar'  => 'Bar',
                'baz'  => 'Baz',
                'bat'  => 'Bat',
                'test' => 'Test',
            ))
            ->setSeparator('--FooBarFunSep--');
        $html = $this->element->render($this->getView());
        $this->assertContains($this->element->getSeparator(), $html);
        $count = substr_count($html, $this->element->getSeparator());
        $this->assertEquals(4, $count);
    }

    /**
     * @group ZF-2830
     */
    public function testRenderingMulticheckboxCreatesCorrectArrayNotation()
    {
        $this->element->addMultiOption(1, 'A');
        $this->element->addMultiOption(2, 'B');
        $html = $this->element->render($this->getView());
        $this->assertContains('name="foo[]"', $html, $html);
        $count = substr_count($html, 'name="foo[]"');
        $this->assertEquals(2, $count);
    }

    /**
     * @group ZF-2828
     */
    public function testCanPopulateCheckboxOptionsFromPostedData()
    {
        $form = new Zend_Form(array(
            'elements' => array(
                '100_1' => array('MultiCheckbox', array(
                    'multiOptions' => array(
                        '100_1_1'  => 'Agriculture',
                        '100_1_2'  => 'Automotive',
                        '100_1_12' => 'Chemical',
                        '100_1_13' => 'Communications',
                    ),
                    'required' => true,
                )),
            ),
        ));
        $data = array(
            '100_1' => array(
                '100_1_1',
                '100_1_2',
                '100_1_12',
                '100_1_13'
            ),
        );
        $form->populate($data);
        $html = $form->render($this->getView());
        foreach ($form->getElement('100_1')->getMultiOptions() as $key => $value) {
            if (!preg_match('#(<input[^>]*' . $key . '[^>]*>)#', $html, $m)) {
                $this->fail('Missing input for a given multi option: ' . $html);
            }
            $this->assertContains('checked="checked"', $m[1]);
        }
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

    /**#+
     * @group ZF-3286
     */
    public function testShouldRegisterInArrayValidatorByDefault()
    {
        $this->assertTrue($this->element->registerInArrayValidator());
    }

    public function testShouldAllowSpecifyingWhetherOrNotToUseInArrayValidator()
    {
        $this->testShouldRegisterInArrayValidatorByDefault();
        $this->element->setRegisterInArrayValidator(false);
        $this->assertFalse($this->element->registerInArrayValidator());
        $this->element->setRegisterInArrayValidator(true);
        $this->assertTrue($this->element->registerInArrayValidator());
    }

    public function testInArrayValidatorShouldBeRegisteredAfterValidation()
    {
        $options = array(
            'foo' => 'Foo Value',
            'bar' => 'Bar Value',
            'baz' => 'Baz Value',
        );
        $this->element->setMultiOptions($options);
        $this->assertFalse($this->element->getValidator('InArray'));
        $this->element->isValid('test');
        $validator = $this->element->getValidator('InArray');
        $this->assertTrue($validator instanceof Zend_Validate_InArray);
    }

    public function testShouldNotValidateIfValueIsNotInArray()
    {
        $options = array(
            'foo' => 'Foo Value',
            'bar' => 'Bar Value',
            'baz' => 'Baz Value',
        );
        $this->element->setMultiOptions($options);
        $this->assertFalse($this->element->getValidator('InArray'));
        $this->assertFalse($this->element->isValid('test'));
    }
    /**#@-*/

    /**
     * No assertion; just making sure no error occurs
     *
     * @group ZF-4915
     */
    public function testRetrievingErrorMessagesShouldNotResultInError()
    {
        $this->element->addMultiOptions(array(
                          'foo' => 'Foo',
                          'bar' => 'Bar',
                          'baz' => 'Baz',
                      ))
                      ->addErrorMessage('%value% is invalid');
        $this->element->isValid(array('foo', 'bogus'));
        $html = $this->element->render($this->getView());
    }

    /**
     * @group ZF-11402
     */
    public function testValidateShouldNotAcceptEmptyArray()
    {
        $this->element->addMultiOptions(array(
            'foo' => 'Foo',
            'bar' => 'Bar',
            'baz' => 'Baz',
        ));
        $this->element->setRegisterInArrayValidator(true);
    
        $this->assertTrue($this->element->isValid(array('foo')));
        $this->assertTrue($this->element->isValid(array('foo','baz')));
        
        $this->element->setAllowEmpty(true);
        $this->assertTrue($this->element->isValid(array()));

        // Empty value + AllowEmpty=true = no error messages
        $messages = $this->element->getMessages();
        $this->assertEquals(0, count($messages), 'Received unexpected error message(s)');
        
        $this->element->setAllowEmpty(false);
        $this->assertFalse($this->element->isValid(array()));
        
        // Empty value + AllowEmpty=false = notInArray error message
        $messages = $this->element->getMessages();
        $this->assertTrue(is_array($messages), 'Expected error message');
        $this->assertArrayHasKey('notInArray', $messages, 'Expected \'notInArray\' error message');
        
        $this->element->setRequired(true)->setAllowEmpty(false);
        $this->assertFalse($this->element->isValid(array()));
        
        // Empty value + Required=true + AllowEmpty=false = isEmpty error message
        $messages = $this->element->getMessages();
        $this->assertTrue(is_array($messages), 'Expected error message');
        $this->assertArrayHasKey('isEmpty', $messages, 'Expected \'isEmpty\' error message');
    }

    /**
     * @group ZF-12059
     */
    public function testDisabledForAttribute()
    {
        $this->element->setLabel('Foo');

        $expected = '<dt id="foo-label"><label class="optional">Foo</label></dt>'
                  . PHP_EOL
                  . '<dd id="foo-element">'
                  . PHP_EOL
                  . '</dd>';
        $this->assertSame($expected, $this->element->render($this->getView()));
    }

    /**
     * @group ZF-12059
     */
    public function testDisabledForAttributeWithoutLabelDecorator()
    {
        $this->element->setLabel('Foo')->removeDecorator('label');

        $expected = '<dd id="foo-element">'
                  . PHP_EOL
                  . '</dd>';
        $this->assertSame($expected, $this->element->render($this->getView()));
    }
}

// Call Zend_Form_Element_MultiCheckboxTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Element_MultiCheckboxTest::main") {
    Zend_Form_Element_MultiCheckboxTest::main();
}
