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

require_once "ZendX/JQuery/View/Helper/AjaxLink.php";

class ZendX_JQuery_View_AjaxLinkTest extends ZendX_JQuery_View_jQueryTestCase
{

    public function testShouldBeCallable() {
        $link = $this->view->ajaxLink("Link to Inject", "inject.html");
        $this->assertContains('Link to Inject', $link);
        $this->assertContains('class="ajaxLink', $link);
        $this->assertNotContains('inject.html', $link);
        $this->assertNotContains('$.get', $link);

        $render = $this->jquery->__toString();
        $this->assertContains('inject.html', $render);
        $this->assertContains('$.get', $render);
    }

    public function testShouldBeCallableInline() {
        $link = $this->view->ajaxLink("Link to Inject", "inject.html", array('inline' => true));
        $this->assertContains('Link to Inject', $link);
        $this->assertNotContains('class="ajaxLink', $link);
        $this->assertContains('inject.html', $link);
        $this->assertContains('$.get', $link);

        $render = $this->jquery->__toString();
        $this->assertNotContains('inject.html', $render);
        $this->assertNotContains('$.get', $render);
    }

    public function testShouldAllowSendingParamsWithPost() {
        $link = $this->view->ajaxLink("Link to Inject2", "inject.php", array('update' => '#test', 'class' => 'someClass'), array('key' => 'value'));
        $this->assertContains('Link to Inject2', $link);
        $this->assertContains('class="someClass ajaxLink', $link);
        $this->assertNotContains('inject.php', $link);

        $render = $this->jquery->__toString();
        $this->assertContains('inject.php', $render);
        $this->assertContains('$.post', $render);
        $this->assertContains('#test', $render);
        $this->assertContains('{"key":"value"}', $render);
    }

    public function testShouldAllowSendingParamsWithGet() {
        $link = $this->view->ajaxLink("Link to Inject3", "inject123.php", array('method' => 'get', 'update' => '#test'), array('key' => 'value'));
        $this->assertContains('Link to Inject3', $link);
        $this->assertContains('class="ajaxLink', $link);
        $this->assertNotContains('inject123.php', $link);

        $render = $this->jquery->__toString();
        $this->assertContains('inject123.php', $render);
        $this->assertContains('$.get', $render);
        $this->assertContains('#test', $render);
        $this->assertContains('{"key":"value"}', $render);
    }

    public function testShouldAllowSpecifyingDataType() {
        $link = $this->view->ajaxLink("JSON Response with Callback", "inject.php", array('complete' => 'jsonCallback(data);', 'dataType' => 'json'), array('name' => 'Ludwig von Mises', 'email' => 'mises@vienna.at'));
        $this->assertContains('JSON Response with Callback', $link);
        $this->assertContains('class="ajaxLink', $link);
        $this->assertNotContains('inject.php', $link);
        $this->assertNotContains('{"name":"Ludwig von Mises","email":"mises@vienna.at"}', $link);

        $render = $this->jquery->__toString();
        $this->assertContains('inject.php', $render);
        $this->assertContains('function(data, textStatus) { jsonCallback(data); }', $render);
        $this->assertContains('"json");', $render);
        $this->assertContains('{"name":"Ludwig von Mises","email":"mises@vienna.at"}', $render);
    }

    public function testShouldWorkInNoConflictMode() {
        ZendX_JQuery_View_Helper_JQuery::enableNoConflictMode();
        $link = $this->view->ajaxLink("Link to Inject", "inject.html", array('update' => '#test', 'inline' => true, 'beforeSend' => 'hide'));

        $this->assertContains('$j.get', $link);
        $this->assertContains('$j("#test")', $link);
        $this->assertContains('$j(this).hide', $link);
    }

    public function testShouldAllowSwitchUpdateDataFunc() {
        $link = $this->view->ajaxLink("Link to Inject", "inject.html", array('update' => '#test', 'inline' => true, 'dataType' => 'text'));
        $this->assertContains('("#test").text(data);', $link);
    }

    static public function dataBeforeSendEffects()
    {
        return array(
            array('hide', 'hide();'),
            array('hideslow', 'hide("slow");'),
            array('hidefast', 'hide("fast");'),
            array('fadeout', 'fadeOut();'),
            array('fadeoutslow', 'fadeOut("slow");'),
            array('fadeoutfast', 'fadeOut("fast");'),
            array('slideup', 'slideUp(1000);'),
        );
    }

    /**
     * @dataProvider dataBeforeSendEffects
     * @param string $effect
     * @param string $js
     */
    public function testShouldAllowUsingBeforeSendEffects($effect, $js)
    {
        $link = $this->view->ajaxLink("Link to Inject", "inject.html", array('update' => '#test', 'inline' => true, 'beforeSend' => $effect));
        $this->assertContains(sprintf('$(this).%s', $js), $link);

        ZendX_JQuery_View_Helper_JQuery::enableNoConflictMode();

        $link = $this->view->ajaxLink("Link to Inject", "inject.html", array('update' => '#test', 'inline' => true, 'beforeSend' => $effect));
        $this->assertContains(sprintf('$j(this).%s', $js), $link);
    }

    static public function dataCompleteEffects()
    {
        return array(
            array('show', 'show();'),
            array('showslow', 'show("slow");'),
            array('showfast', 'show("fast");'),
            array('shownormal', 'show("normal");'),
            array('fadein', 'fadeIn("normal");'),
            array('fadeinslow', 'fadeIn("slow");'),
            array('fadeinfast', 'fadeIn("fast");'),
            array('slidedown', 'slideDown("normal");'),
            array('slidedownslow', 'slideDown("slow");'),
            array('slidedownfast', 'slideDown("fast");'),
        );
    }

    /**
     * @dataProvider dataCompleteEffects
     * @param <type> $effect
     * @param <type> $js
     */
    public function testShouldAllowUsingCompleteEffects($effect, $js)
    {
        $link = $this->view->ajaxLink("Link to Inject", "inject.html", array('update' => '#test', 'inline' => true, 'complete' => $effect));
        $this->assertContains(sprintf('$("#test").%s', $js), $link);

        ZendX_JQuery_View_Helper_JQuery::enableNoConflictMode();

        $link = $this->view->ajaxLink("Link to Inject", "inject.html", array('update' => '#test', 'inline' => true, 'complete' => $effect));
        $this->assertContains(sprintf('$j("#test").%s', $js), $link);
    }

    public function testOptionsArrayAllowsForSettingAttributes() {
        $view = $this->getView();

        $html = $view->ajaxLink("Label1", "/some/url", array(
            'id' => 'ajaxLink1',
            'title' => 'Label1',
            'noscript' => true,
            'attribs' => array('class' => 'test', 'target' => '_blank')
        ));

        $this->assertContains('id="ajaxLink1"', $html);
        $this->assertContains('title="Label1"', $html);
        $this->assertContains('href="/some/url"', $html);
        $this->assertNotContains('href="#"', $html);
        $this->assertContains('class="test"', $html);
        $this->assertContains('target="_blank"', $html);
    }

    public function testSpecifyingIdDoesNotCreateAutomaticCallbackAndClassAttribute() {
        $view = $this->getView();

        $html = $view->ajaxLink("Label1", "/some/url", array(
            'id' => "someId"
        ));

        $this->assertNotContains('class=', $html);
        $this->assertContains('id="someId"', $html);
    }

    /**
     * @group ZF-5041
     */
    public function testXhtmlDoctypeDoesNotMakeAnchorInvalidHtml() {
        $view = $this->getView();
        $view->doctype('XHTML1_STRICT');

        $html = $view->ajaxLink("Label1", "/some/url", array('id' => "someId"));

        $this->assertNotContains("/>Label1</a>", $html);
        $this->assertContains(">Label1</a>", $html);
   }

   /** @group ZF-9926 */
   public function testDoNotUseSingleQuotesInJsAsItBreaksInlineLinks()
   {
       $view = $this->getView();

       $html = $view->ajaxLink('Label1', '/some/url', array(
           'method'     => 'post',
           'dataType'   => 'json',
           'noscript'   => true,
           'beforeSend' => 'if(!confirm("Are you sure?")) {return false;}$("#progress-bar").show();',
           'complete'   => '$("#progress-bar").hide();',
           'inline'     => true
       ));

       $this->assertContains('$.post("/some/url"', $html);
       $this->assertNotContains("'/some/url'", $html);
       $this->assertNotContains("'json'", $html);
   }
   /** @group ZF-9926 */
   public function testSingleQuotesAreEscapedInJsInlineLinks()
   {
       $view = $this->getView();

       $html = $view->ajaxLink('Label1', '/some/url', array(
           'method'     => 'post',
           'dataType'   => 'json',
           'noscript'   => true,
           'beforeSend' => "if(!confirm('Are you sure?')) {return false;}$('#progress-bar').show();",
           'complete'   => '$("#progress-bar").hide();',
           'inline'     => true
       ));

       $this->assertContains('&#39;Are you sure?&#39;', $html);
       $this->assertContains('&#39;#progress-bar&#39;', $html);
       $this->assertContains('"#progress-bar"', $html);
   }
}
