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
 * @category    ZendX
 * @package     ZendX_JQuery
 * @subpackage  View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id: AllTests.php 11232 2008-09-05 08:16:33Z beberlei $
 */

require_once dirname(__FILE__)."/../../../TestHelper.php";

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'ZendX_JQuery_View_DecoratorTest::main');
}

require_once "Zend/Registry.php";
require_once "Zend/View.php";
require_once "ZendX/JQuery.php";
require_once "ZendX/JQuery/View/Helper/JQuery.php";

require_once "ZendX/JQuery/Form.php";
require_once "ZendX/JQuery/Form/Element/Spinner.php";
require_once "Zend/Form/Decorator/ViewHelper.php";
require_once "ZendX/JQuery/Form/Decorator/UiWidgetElement.php";
require_once "ZendX/JQuery/Form/Decorator/TabContainer.php";
require_once "ZendX/JQuery/Form/Decorator/TabPane.php";

class ZendX_JQuery_Form_DecoratorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Zend_Registry::_unsetInstance();
    }

    /**
     * Returns the contens of the exepcted $file
     *
     * @param  string $file
     * @return string
     */
    protected function _getExpected($file)
    {
        return file_get_contents(dirname(__FILE__) . '/_files/expected/' . $file);
    }

    public function testUiWidgetElementDecoratorRender()
    {
        $ac = new ZendX_JQuery_Form_Element_Spinner("ac1");
        // Remove all non jQUery related decorators
        $ac->removeDecorator('Errors');
        $ac->removeDecorator('HtmlTag');
        $ac->removeDecorator('Label');

        try {
            $ac->render();
            $this->fail();
        } catch(Zend_Form_Decorator_Exception $e) {

        } catch(Zend_Exception $e) {
            $this->fail();
        }

        $view = new Zend_View();
        ZendX_JQuery::enableView($view);

        $ac->setView($view);
        $output = $ac->render();

        $this->assertContains("ac1", $output);
    }

    public function testUiWidgetElementJQueryParams()
    {
        $spinner = new ZendX_JQuery_Form_Element_Spinner("ac1");
        $uiWidget = $spinner->getDecorator('UiWidgetElement');

        $uiWidget->setJQueryParam("foo", "bar");
        $this->assertEquals(array("foo" => "bar"), $uiWidget->getJQueryParams());

        $uiWidget->setJQueryParams(array("bar" => "baz"));
        $this->assertEquals(array("foo" => "bar", "bar" => "baz"), $uiWidget->getJQueryParams());

        $this->assertEquals("bar", $uiWidget->getJQueryParam("foo"));
        $this->assertEquals("baz", $uiWidget->getJQueryParam("bar"));
        $this->assertNull($uiWidget->getJQueryParam("unknownParam"));
    }

    public function testUiWidgetElementRendersElementJQueryParams()
    {
        $view = new Zend_View();
        ZendX_JQuery::enableView($view);

        $spinner = new ZendX_JQuery_Form_Element_Spinner("ac1");
        $spinner->setJQueryParam('min', 100);
        $spinner->setView($view);
        $output = $spinner->render();
        $this->assertEquals(array('$("#ac1").spinner({"min":100});'), $view->jQuery()->getOnLoadActions());
    }

    public function testUiWidgetContainerGetHelper()
    {
        $container = new ZendX_JQuery_Form_Decorator_TabContainer();
        $this->assertEquals("tabContainer", $container->getHelper());
    }

    public function testUiWidgetContainerGetAttribs()
    {
        $container = new ZendX_JQuery_Form_Decorator_TabContainer();
        $ac = new ZendX_JQuery_Form_Element_Spinner("ac1");
        $container->setElement($ac);

        $this->assertEquals(array("helper" => "spinner", "options" => array()), $container->getAttribs());
    }

    public function testUiWidgetContainerGetJQueryParams()
    {
        $container = new ZendX_JQuery_Form_Decorator_TabContainer();
        $ac = new ZendX_JQuery_Form_Element_Spinner("spinner");
        $ac->setJQueryParams(array("foo" => "bar", "baz" => "baz"));
        $container->setElement($ac);

        $this->assertEquals(array("foo" => "bar", "baz" => "baz"), $container->getJQueryParams());
    }

    public function testUiWidgetPaneRenderingThrowsExceptionWithoutContainerIdOption()
    {
        $spinner = new ZendX_JQuery_Form_Element_Spinner("spinner1");
        $spinner->setView(new Zend_View());
        $spinner->setJQueryParam("title", "Title");

        $pane = new ZendX_JQuery_Form_Decorator_TabPane();
        $pane->setElement($spinner);

        try {
            $pane->render("");
            $this->fail();
        } catch(Zend_Form_Decorator_Exception $e) {

        }
    }

    public function testUiWidgetPaneRenderingThrowsExceptionWithoutTitleOption()
    {
        $spinner = new ZendX_JQuery_Form_Element_Spinner("spinner1");
        $spinner->setView(new Zend_View());
        $spinner->setJQueryParam("containerId", "xyzId");

        $pane = new ZendX_JQuery_Form_Decorator_TabPane();
        $pane->setElement($spinner);

        try {
            $pane->render("");
            $this->fail();
        } catch(Zend_Form_Decorator_Exception $e) {

        }
    }

    public function testUiWidgetPaneRenderingNoPaneWhenElementHasNoView()
    {
        $spinner = new ZendX_JQuery_Form_Element_Spinner("spinner1");

        $pane = new ZendX_JQuery_Form_Decorator_TabPane();
        $pane->setElement($spinner);

        $this->assertEquals("justthis", $pane->render("justthis"));
    }

    public function testUiWidgetContainerRender()
    {
        $view = new Zend_View();
        ZendX_JQuery::enableView($view);

        // Create new jQuery Form
        $form = new ZendX_JQuery_Form();
        $form->setView($view);
        $form->setAction('formdemo.php');
        $form->setAttrib('id', 'mainForm');

        // Use a TabContainer for your form:
        $form->setDecorators(array(
            'FormElements',
            array('TabContainer', array(
                'id'          => 'tabContainer',
                'style'       => 'width: 600px;',
                'jQueryParams' => array(
                    'tabPosition' => 'top'
                ),
            )),
            'Form',
        ));

        $subForm1 = new ZendX_JQuery_Form('subform1');
        $subForm1->setView($view);

        // Add Element Spinner
        $elem = new ZendX_JQuery_Form_Element_Spinner("spinner1", array('label' => 'Spinner:', 'attribs' => array('class' => 'flora')));
        $elem->setJQueryParams(array('min' => 0, 'max' => 1000, 'start' => 100));

        $subForm1->addElement($elem);

        $subForm1->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl')),
            array('TabPane', array('jQueryParams' => array('containerId' => 'mainForm', 'title' => 'Slider'))),
        ));

        $form->addSubForm($subForm1, "form1");

        $output = $form->render($view);
        $this->assertContains('id="tabContainer"', $output);
        $this->assertContains('href="#tabContainer-frag-1"', $output);
        $this->assertContains('id="tabContainer-frag-1"', $output);
    }

    /**
     * @group ZF-12175
     */
    public function testUiWidgetContainerRenderWithContent()
    {
        // Setup view
        $view = new Zend_View();
        ZendX_JQuery::enableView($view);

        // Create jQuery Form
        $form = new ZendX_JQuery_Form(
            array(
                 'method'     => Zend_Form::METHOD_GET,
                 'attribs'    => array(
                     'id' => 'mainForm',
                 ),
                 'decorators' => array(
                     'FormElements',
                     array(
                         'HtmlTag',
                         array(
                             'tag' => 'dl',
                         ),
                     ),
                     array(
                         'TabContainer',
                         array(
                             'id'        => 'tabContainer',
                             'placement' => 'prepend',
                             'separator' => '',
                         ),
                     ),
                     'Form',
                 )
            )
        );

        // Add sub form
        $subForm = new ZendX_JQuery_Form(
            array(
                 'decorators' => array(
                     'FormElements',
                     array(
                         'HtmlTag',
                         array(
                             'tag' => 'dl',
                         ),
                     ),
                     array(
                         'TabPane',
                         array(
                             'jQueryParams' => array(
                                 'containerId' => 'mainForm',
                                 'title'       => 'Slider',
                             ),
                         ),
                     ),
                 )
            )
        );
        $form->addSubForm($subForm, 'subform');

        // Add spinner element to subform
        $subForm->addElement(
            'spinner',
            'spinner',
            array(
                 'label'   => 'Spinner:',
                 'attribs' => array(
                     'class' => 'flora',
                 ),
                 'jQueryParams' => array(
                     'min'   => 0,
                      'max'   => 1000,
                      'start' => 100,
                 ),
            )
        );

        // Add submit button to main form
        $form->addElement(
            'submit',
            'submit',
            array(
                 'label' => 'Send',
            )
        );

        $this->assertSame(
            $this->_getExpected('uiwidgetcontainer/with_content.html'),
            $form->render($view)
        );
    }

    /**
     * @group ZF-8055
     */
    public function testUiWidgetDialogContainerRenderBug()
    {
        $view = new Zend_View();
        ZendX_JQuery::enableView($view);

        // Create new jQuery Form
        $form = new ZendX_JQuery_Form();
        $form->setView($view);
        $form->setAction('formdemo.php');
        $form->setAttrib('id', 'mainForm');

        // Use a TabContainer for your form:
        $form->setDecorators(array(
            'FormElements',
            'Form',
            array('DialogContainer', array(
                'id'          => 'tabContainer',
                'style'       => 'width: 600px;',
                'jQueryParams' => array(
                    'tabPosition' => 'top'
                ),
            )),
        ));

        $subForm1 = new ZendX_JQuery_Form('subform1');
        $subForm1->setView($view);

        // Add Element Spinner
        $elem = new ZendX_JQuery_Form_Element_Spinner("spinner1", array('label' => 'Spinner:', 'attribs' => array('class' => 'flora')));
        $elem->setJQueryParams(array('min' => 0, 'max' => 1000, 'start' => 100));

        $subForm1->addElement($elem);

        $subForm1->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl')),
        ));

        $form->addSubForm($subForm1, "form1");

        $output = $form->render($view);

        $this->assertContains('<div id="tabContainer" style="width: 600px;"><form', $output);
    }

    public function testRenderWidgetElementShouldEnableJQueryHelper()
    {
        $view = new Zend_View();

        $widget = new ZendX_JQuery_Form_Element_Spinner("spinner1", array("label" => "Spinner"));
        $widget->setView($view);

        $view->jQuery()->disable();
        $view->jQuery()->uiDisable();

        $widget->render();

        $this->assertTrue($view->jQuery()->isEnabled());
        $this->assertTrue($view->jQuery()->uiIsEnabled());
    }

    public function testSettingWidgetPlacement()
    {
        $view = new Zend_View();
        $widget = new ZendX_JQuery_Form_Element_Spinner("spinner1");
        $widget->setView($view);
        $widget->getDecorator('UiWidgetElement')->setOption('separator', '[SEP]');

        $widget->getDecorator('UiWidgetElement')->setOption('placement', 'APPEND');
        $html = $widget->render();
        $this->assertContains('[SEP]<input type="text" name="spinner1" id="spinner1" value="">', $html);

        $widget->getDecorator('UiWidgetElement')->setOption('placement', 'PREPEND');
        $html = $widget->render();
        $this->assertContains('<input type="text" name="spinner1" id="spinner1" value="">[SEP]', $html);
    }
}

if (PHPUnit_MAIN_METHOD == 'ZendX_JQuery_Form_DecoratorTest::main') {
    ZendX_JQuery_Form_DecoratorTest::main();
}
