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
require_once "ZendX/JQuery/View/Helper/TabContainer.php";

class ZendX_JQuery_View_TabContainerTest extends ZendX_JQuery_View_jQueryTestCase
{
    public function testCallingInViewEnablesJQueryHelper()
    {
        $element = $this->view->tabContainer();

        $this->assertTrue($this->jquery->isEnabled());
        $this->assertTrue($this->jquery->uiIsEnabled());
    }

    public function testShouldAppendToJqueryHelper()
    {
        $this->view->tabContainer()->addPane("elem1", "test1", "test1");
        $element = $this->view->tabContainer("elem1", array('option' => 'true'), array());

        $jquery = $this->view->jQuery()->__toString();
        $this->assertContains('tabs(', $jquery);
        $this->assertContains('"option":"true"', $jquery);
    }

    public function testShouldAllowAddingTabs()
    {
        $tabs = $this->view->tabContainer()->addPane("container1", "elem1", "Text1")
                        ->addPane("container1", "elem2", "Text2")
                        ->tabContainer("container1", array(), array());

        $this->assertEquals(array('$("#container1").tabs({});'), $this->jquery->getOnLoadActions());
        $this->assertContains("elem1", $tabs);
        $this->assertContains("Text1", $tabs);
        $this->assertContains("elem2", $tabs);
        $this->assertContains("Text2", $tabs);
        $this->assertContains('href="#container1-frag-1"', $tabs);
        $this->assertContains('href="#container1-frag-2"', $tabs);
    }

    public function testShoudAllowAddingTabsFromUrls()
    {
        $tabs = $this->view->tabContainer()->addPane("container1", "elem1", '', array('contentUrl' => 'blub.html'))
                        ->addPane("container1", "elem2", '', array('contentUrl' => 'cookie.html'))
                        ->tabContainer("container1", array(), array());

        $this->assertEquals(array('$("#container1").tabs({});'), $this->jquery->getOnLoadActions());
        $this->assertContains("elem1", $tabs);
        $this->assertContains("elem2", $tabs);
        $this->assertContains('href="blub.html"', $tabs);
        $this->assertContains('href="cookie.html"', $tabs);
    }

    public function testShouldAllowCaptureTabContent()
    {
        $this->view->tabPane()->captureStart("container1", "elem1");
        echo "Lorem Ipsum!";
        $this->view->tabPane()->captureEnd("container1");

        $this->view->tabPane()->captureStart("container1", "elem2", array('contentUrl' => 'foo.html'));
        echo "This is captured, but not displayed: contentUrl overrides this output.";
        $this->view->tabPane()->captureEnd("container1");

        $tabs = $this->view->tabContainer("container1", array(), array());

        $this->assertEquals(array('$("#container1").tabs({});'), $this->jquery->getOnLoadActions());
        $this->assertContains('elem1', $tabs);
        $this->assertContains('elem2', $tabs);
        $this->assertContains('Lorem Ipsum!', $tabs);
        $this->assertContains('href="foo.html"', $tabs);
        $this->assertNotContains('This is captured, but not displayed: contentUrl overrides this output.', $tabs);
    }

    public function testShouldAllowUsingTabPane()
    {
        $this->view->tabPane("container1", "Lorem Ipsum!", array('title' => 'elem1'));
        $this->view->tabPane("container1", '', array('title' => 'elem2', 'contentUrl' => 'foo.html'));

        $tabs = $this->view->tabContainer("container1", array(), array());

        $this->assertEquals(array('$("#container1").tabs({});'), $this->jquery->getOnLoadActions());
        $this->assertContains('elem1', $tabs);
        $this->assertContains('elem2', $tabs);
        $this->assertContains('Lorem Ipsum!', $tabs);
        $this->assertContains('href="foo.html"', $tabs);
        $this->assertNotContains('This is captured, but not displayed: contentUrl overrides this output.', $tabs);
    }

    public function testPaneCaptureLockExceptionNoNestingAllowed()
    {
        $this->view->tabPane()->captureStart('pane1', 'Label1');
        try {
            $this->view->tabPane()->captureStart('pane1', 'Label1');
            $this->fail();
        } catch(ZendX_JQuery_View_Exception $e) {

        }
    }

    public function testPaneCaptureLockExceptionNoEndWithoutStartPossible()
    {
        try {
            $this->view->tabPane()->captureEnd('pane3');
            $this->fail();
        } catch(ZendX_JQuery_View_Exception $e) {

        }
    }
}
