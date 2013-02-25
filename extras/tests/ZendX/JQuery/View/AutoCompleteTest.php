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

require_once "ZendX/JQuery/View/Helper/AutoComplete.php";

class ZendX_JQuery_View_AutoCompleteTest extends ZendX_JQuery_View_jQueryTestCase
{
    public function testCallingInViewEnablesJQueryHelper()
    {
        $element = $this->view->autoComplete("element", "", array('data' => array('test')));

        $this->assertTrue($this->jquery->isEnabled());
        $this->assertTrue($this->jquery->uiIsEnabled());
    }

    public function testShouldAppendToJqueryHelper()
    {
        $element = $this->view->autoComplete("elem1", "Default", array('option' => 'true', 'data' => array('test')), array());

        $jquery = $this->view->jQuery()->__toString();
        $this->assertContains('autocomplete(', $jquery);
        $this->assertContains('"option":"true"', $jquery);
    }

    /**
     * @expectedException ZendX_JQuery_Exception
     */
    public function testShouldAllowAutoCompleteOnlyWithSourceOption()
    {
        $element = $this->view->autoComplete("elem1");
    }

    public function testShouldCreateInputField()
    {
        $element = $this->view->autoComplete("elem1", "Default", array('source' => array('Test')));

        $this->assertEquals(array('$("#elem1").autocomplete({"source":["Test"]});'), $this->view->jQuery()->getOnLoadActions());
        $this->assertContains("<input", $element);
        $this->assertContains('id="elem1"', $element);
        $this->assertContains('value="Default"', $element);
    }
}