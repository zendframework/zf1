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
require_once "ZendX/JQuery/View/Helper/Spinner.php";

class ZendX_JQuery_View_SpinnerTest extends ZendX_JQuery_View_jQueryTestCase
{
    public function testCallingInViewEnablesJQueryHelper()
    {
        $element = $this->view->spinner("element", "");

        $this->assertTrue($this->jquery->isEnabled());
        $this->assertTrue($this->jquery->uiIsEnabled());
    }

    public function testShouldAppendToJqueryHelper()
    {
        $element = $this->view->spinner("elem1", "", array("option" => "true"));

        $jquery = $this->view->jQuery()->__toString();
        $this->assertContains('spinner(', $jquery);
        $this->assertContains('"option":"true"', $jquery);
    }

    public function testShouldCreateInputElement()
    {
        $element = $this->view->spinner("elem1");

        $this->assertEquals(array('$("#elem1").spinner({});'), $this->jquery->getOnLoadActions());
        $this->assertContains('<input', $element);
        $this->assertContains('id="elem1"', $element);
    }

    public function testNumericValueShouldBecomeStartOptionParameterIfNoneGiven()
    {
        $element = $this->view->spinner("elem1", "100");
        $this->assertEquals(array('$("#elem1").spinner({"start":"100"});'), $this->jquery->getOnLoadActions());
    }
}