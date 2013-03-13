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

require_once "jQueryTestCase.php";
require_once "ZendX/JQuery/View/Helper/DialogContainer.php";

class ZendX_JQuery_View_DialogContainerTest extends ZendX_JQuery_View_jQueryTestCase
{
    public function testCallingInViewEnablesJQueryHelper()
    {
        $element = $this->view->dialogContainer("element", "");

        $this->assertTrue($this->jquery->isEnabled());
        $this->assertTrue($this->jquery->uiIsEnabled());
    }

    public function testShouldAppendToJqueryHelper()
    {
        $element = $this->view->dialogContainer("elem1", "", array("option" => "true"));

        $jquery = $this->jquery->__toString();
        $this->assertContains('dialog(', $jquery);
        $this->assertContains('"option":"true"', $jquery);
    }

    public function testShouldCreateDivContainer()
    {
        $element = $this->view->dialogContainer("elem1", "", array(), array());

        $this->assertEquals(array('$("#elem1").dialog({});'), $this->jquery->getOnLoadActions());
        $this->assertContains("<div", $element);
        $this->assertContains('id="elem1"', $element);
        $this->assertContains("</div>", $element);
    }

    /**
     * @group ZF-4685
     */
    public function testUsingJsonExprForResizeShouldBeValidJsCallbackRegression()
    {
        $params = array(
            "resize" => new Zend_Json_Expr("doMyThingAtResize"),
        );

        $this->view->dialogContainer("dialog1", "Some text", $params);

        $actions = $this->jquery->getOnLoadActions();
        $this->assertEquals(array('$("#dialog1").dialog({"resize":doMyThingAtResize});'), $actions);
    }
}
