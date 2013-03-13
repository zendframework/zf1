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
require_once "ZendX/JQuery/View/Helper/AccordionContainer.php";

class ZendX_JQuery_View_AccordionContainerTest extends ZendX_JQuery_View_jQueryTestCase
{
    public function testCallingInViewEnablesJQueryHelper()
    {
        $element = $this->view->accordionContainer();

        $this->assertTrue($this->jquery->isEnabled());
        $this->assertTrue($this->jquery->uiIsEnabled());
        $this->assertTrue($element instanceof ZendX_JQuery_View_Helper_AccordionContainer);
    }

    public function testShouldAppendToJqueryHelper()
    {
        $this->view->accordionContainer()->addPane("elem1", "test1", "test1");
        $element = $this->view->accordionContainer("elem1", array('option' => 'true'), array());

        $jquery = $this->view->jQuery()->__toString();
        $this->assertContains('accordion(', $jquery);
        $this->assertContains('"option":"true"', $jquery);
    }

    public function testShouldReturnEmptyStringIfEmpty()
    {
        $accordion = $this->view->accordionContainer("empty", array(), array());

        $this->assertEquals('', $accordion);
    }

    public function testShouldAllowAddingTabs()
    {
        $accordion = $this->view->accordionContainer()->addPane("container1", "elem1", "Text1")
                        ->addPane("container1", "elem2", "Text2")
                        ->accordionContainer("container1", array(), array());

        $this->assertEquals(array('$("#container1").accordion({});'), $this->jquery->getOnLoadActions());
        $this->assertContains("elem1", $accordion);
        $this->assertContains("Text1", $accordion);
        $this->assertContains("elem2", $accordion);
        $this->assertContains("Text2", $accordion);
    }

    public function testShouldAllowAddingMultipleTabs()
    {
        $this->view->accordionContainer()->addPane("container1", "elem1", "Text1")
             ->addPane("container2", "elem2", "Text2");

        $accordion = $this->view->accordionContainer("container1", array(), array());
        $accordion2 = $this->view->accordionContainer("container2", array(), array());

        $this->assertEquals(array('$("#container1").accordion({});', '$("#container2").accordion({});'), $this->jquery->getOnLoadActions());
        $this->assertNotContains("elem1", $accordion2);
        $this->assertNotContains("Text1", $accordion2);
        $this->assertContains("elem1", $accordion);
        $this->assertContains("Text1", $accordion);
        $this->assertNotContains("elem2", $accordion);
        $this->assertNotContains("Text2", $accordion);
        $this->assertContains("elem2", $accordion2);
        $this->assertContains("Text2", $accordion2);
    }

    public function testShouldAllowCaptureTabContent()
    {
        $this->view->accordionPane()->captureStart("container1", "elem1");
        echo "Lorem Ipsum!";
        $this->view->accordionPane()->captureEnd("container1");

        $this->view->accordionPane()->captureStart("container1", "elem2", array('contentUrl' => 'foo.html'));
        echo "This is captured and displayed: contentUrl does not exist for Accordion.";
        $this->view->accordionPane()->captureEnd("container1");

        $accordion = $this->view->accordionContainer("container1", array(), array());

        $this->assertEquals(array('$("#container1").accordion({});'), $this->jquery->getOnLoadActions());
        $this->assertContains('elem1', $accordion);
        $this->assertContains('elem2', $accordion);
        $this->assertContains('Lorem Ipsum!', $accordion);
        $this->assertNotContains('href="foo.html"', $accordion);
        $this->assertContains('This is captured and displayed: contentUrl does not exist for Accordion.', $accordion);
    }

    public function testShouldAllowUsingTabPane()
    {
        $this->view->accordionPane("container1", "Lorem Ipsum!", array('title' => 'elem1'));
        $this->view->accordionPane("container1", 'This is captured and displayed: contentUrl does not exist for Accordion.', array('title' => 'elem2', 'contentUrl' => 'foo.html'));
        $accordion = $this->view->accordionContainer("container1", array(), array());

        $this->assertEquals(array('$("#container1").accordion({});'), $this->jquery->getOnLoadActions());
        $this->assertContains('elem1', $accordion);
        $this->assertContains('elem2', $accordion);
        $this->assertContains('Lorem Ipsum!', $accordion);
        $this->assertNotContains('href="foo.html"', $accordion);
        $this->assertContains('This is captured and displayed: contentUrl does not exist for Accordion.', $accordion);
    }

    /**
     * @group ZF-6321
     */
    public function testAccordingHtmlRenderingWithUi15()
    {
        $this->view->jQuery()->setUiVersion("1.5.3");

        $this->view->accordionPane("container1", "foo", array('title' => 'foo'));
        $this->view->accordionPane("container1", 'bar', array('title' => 'bar'));
        $accordion = $this->view->accordionContainer("container1", array(), array());

        $this->assertEquals(
            '<ul id="container1">
<li class="ui-accordion-group"><a href="#" class="ui-accordion-header">foo</a><div class="ui-accordion-content">foo</div></li>
<li class="ui-accordion-group"><a href="#" class="ui-accordion-header">bar</a><div class="ui-accordion-content">bar</div></li>
</ul>
',
            $accordion
        );
    }

    /**
     * @group ZF-6321
     */
    public function testAccordingHtmlRenderingWithUi17()
    {
        $this->view->jQuery()->setUiVersion("1.7.0");

        $this->view->accordionPane("container1", "foo", array('title' => 'foo'));
        $this->view->accordionPane("container1", 'bar', array('title' => 'bar'));
        $accordion = $this->view->accordionContainer("container1", array(), array());

        $this->assertEquals(
            '<div id="container1">
<h3><a href="#">foo</a></h3><div>foo</div>
<h3><a href="#">bar</a></h3><div>bar</div>
</div>
',
            $accordion
        );
    }

    public function testAccordionSetWrongHtmlTemplate_ThrowsException()
    {
        $this->setExpectedException("ZendX_JQuery_View_Exception");

        $this->view->getHelper('accordionContainer')->setElementHtmlTemplate("foo");
    }

    public function testAccordionSetHtmlTemplate()
    {
        $this->view->getHelper('accordionContainer')->setElementHtmlTemplate("<h3>%s</h3><p>%s</p>");

        $this->view->accordionPane("container1", "foo", array('title' => 'foo'));
        $accordion = $this->view->accordionContainer("container1", array(), array());

        $this->assertEquals(
            '<div id="container1">
<h3>foo</h3><p>foo</p>
</div>
',
            $accordion
        );
    }
}
