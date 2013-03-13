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
 * @version     $Id$
 */

require_once dirname(__FILE__)."/../../TestHelper.php";

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'ZendX_JQuery_View_DatePickerTest::main');
}

require_once "Zend/Registry.php";
require_once "Zend/View.php";
require_once "Zend/Form.php";
require_once "ZendX/JQuery.php";
require_once "ZendX/JQuery/Form.php";
require_once "Zend/Form/Element.php";
require_once "ZendX/JQuery/Form/Element/Spinner.php";

class ZendX_JQuery_JQueryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("ZendX_JQuery_JQueryTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testShouldAllowEnableView()
    {
        $view = new Zend_View();
        ZendX_JQuery::enableView($view);

        $this->assertTrue( false !== ($view->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper')) );
    }

    public function testShouldAllowEnableForm()
    {
        $form = new Zend_Form();

        $this->assertFalse( false !== ($form->getPluginLoader('element')->getPaths('ZendX_JQuery_Form_Element')) );
        $this->assertFalse( false !== ($form->getPluginLoader('decorator')->getPaths('ZendX_JQuery_Form_Decorator')) );

        ZendX_JQuery::enableForm($form);

        $this->assertTrue( false !== ($form->getPluginLoader('element')->getPaths('ZendX_JQuery_Form_Element')) );
        $this->assertTrue( false !== ($form->getPluginLoader('decorator')->getPaths('ZendX_JQuery_Form_Decorator')) );
    }

    public function testFormShouldEnableView()
    {
        $form = new Zend_Form();
        $view = new Zend_View();
        $form->setView($view);

        $this->assertFalse( false !== ($form->getView()->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper')) );

        ZendX_JQuery::enableForm($form);

        $this->assertTrue( false !== ($form->getView()->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper')) );
    }

    public function testFormEnableShouldIncludeSubforms()
    {
        $form = new Zend_Form();
        $subform = new Zend_Form();
        $form->addSubForm($subform, "subform1");

        $this->assertFalse( false !== ($form->getPluginLoader('element')->getPaths('ZendX_JQuery_Form_Element')) );
        $this->assertFalse( false !== ($form->getPluginLoader('decorator')->getPaths('ZendX_JQuery_Form_Decorator')) );

        ZendX_JQuery::enableForm($form);

        $this->assertTrue( false !== ($form->getPluginLoader('element')->getPaths('ZendX_JQuery_Form_Element')) );
        $this->assertTrue( false !== ($form->getPluginLoader('decorator')->getPaths('ZendX_JQuery_Form_Decorator')) );
    }

    public function testFormEnableShouldIncludeElementsOnRender()
    {
        $view = new Zend_View();
        $form = new Zend_Form();
        $element = new ZendX_JQuery_Form_Element_Spinner("spinner1");
        $form->setView($view);
        $form->addElement($element);

        ZendX_JQuery::enableForm($form);

        $this->assertFalse($form->getElement('spinner1')->getView() instanceof Zend_View);

        $form->render();
        $this->assertTrue($form->getElement('spinner1')->getView() instanceof Zend_View);
        $this->assertTrue( false !== ($form->getElement('spinner1')->getView()->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper')) );
    }

    public function testJQueryFormShouldHaveHelperPath()
    {
        $form = new ZendX_JQuery_Form();
        $this->assertTrue( false !== ($form->getPluginLoader('element')->getPaths('ZendX_JQuery_Form_Element')) );
        $this->assertTrue( false !== ($form->getPluginLoader('decorator')->getPaths('ZendX_JQuery_Form_Decorator')) );
    }

    public function testJQueryFormShouldAutomaticallyEnableView()
    {
        $form = new ZendX_JQuery_Form();

        $view = new Zend_View();
        $this->assertFalse( false !== ($view->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper')) );

        $form->setView($view);

        $this->assertTrue( false !== ($form->getView()->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper')) );
    }
}

if (PHPUnit_MAIN_METHOD == 'ZendX_JQuery_JQueryTest::main') {
    ZendX_JQuery_JQUeryTest::main();
}