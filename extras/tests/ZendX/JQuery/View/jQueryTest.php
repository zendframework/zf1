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
 * @copyright   Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id$
 */

require_once 'jQueryTestCase.php';

class ZendX_JQuery_View_jQueryTest extends ZendX_JQuery_View_jQueryTestCase
{
    public function testHelperSuccessfulCallForward()
    {
        $jquery = new ZendX_JQuery_View_Helper_JQuery;
        $jquery->addJavascript('alert();');
    }

    /**
     * @expectedException Zend_View_Exception
     */
    public function testHelperFailingCallForward()
    {
        $jquery = new ZendX_JQuery_View_Helper_JQuery();
        $jquery->addAsdf();
    }

    public function testShouldCallHelperWithoutExceptions()
    {
        $jquery = $this->view->jQuery();
        $this->assertTrue($jquery instanceof ZendX_JQuery_View_Helper_JQuery_Container);
    }

    public function testShouldAllowSpecifyingJQueryVersion()
    {
        $this->jquery->setVersion('1.2.3');
        $this->assertEquals('1.2.3', $this->jquery->getVersion());
    }

    public function testShouldUseDefaultSupportedVersionWhenNotSpecifiedOtherwise()
    {
        $this->assertEquals(ZendX_JQuery::DEFAULT_JQUERY_VERSION, $this->jquery->getVersion());
        $this->assertEquals(ZendX_JQuery::DEFAULT_JQUERY_VERSION, $this->jquery->getCdnVersion());
    }

    /**
     * Behaviour changed in 1.8
     *
     * @group ZF-5667
     */
    public function testUsingCdnShouldNotEnableHelperAnymore()
    {
        $this->jquery->setCdnVersion();
        $this->assertFalse($this->jquery->isEnabled());
    }

    public function testShouldBeNotEnabledByDefault()
    {
        $this->assertFalse($this->jquery->isEnabled());
    }

    public function testUsingLocalPath()
    {
        $this->jquery->setLocalPath('/js/jquery.min.js');
        $this->assertFalse($this->jquery->useCDN());
        $this->assertFalse($this->jquery->isEnabled());
        $this->assertTrue($this->jquery->useLocalPath());
        $this->assertContains('/js/jquery.min.js', $this->jquery->getLocalPath());

        $render = $this->jquery->__toString();
        $this->assertNotContains('/js/jquery.min.js', $render);
    }

    public function testUiDisabledDefault()
    {
         $this->assertFalse($this->jquery->uiIsEnabled());
    }

    public function testUiUsingCdnByDefault()
    {
         $this->assertFalse($this->jquery->useUiLocal());
         $this->assertTrue($this->jquery->useUiCdn());
         $this->assertNull($this->jquery->getUiPath());
    }

    public function testGetUiVersionReturnsDefaultSupportedVersionIfNotSpecifiedOtherwise()
    {
        $this->assertEquals(ZendX_JQuery::DEFAULT_UI_VERSION, $this->jquery->getUiVersion());
        $this->assertEquals(ZendX_JQuery::DEFAULT_UI_VERSION, $this->jquery->getUiVersion());
    }

    public function testShouldAllowEnableUi()
    {
        $this->jquery->uiEnable();

        $render = $this->jquery->__toString();
        $this->assertContains('jquery-ui', $render);
        $this->assertContains($this->jquery->getUiVersion(), $render);
    }

    public function testShouldAllowSetUiVersion()
    {
         $this->jquery->setUiVersion('1.5.1');
         $this->assertContains('1.5.1', $this->jquery->getUiVersion());
    }

    public function testShouldAllowSetLocalUiPath()
    {
         $this->jquery->setUiLocalPath('/js/jquery-ui.min.js');

         $this->assertTrue($this->jquery->useUiLocal());
         $this->assertFalse($this->jquery->useUiCdn());
         $this->assertContains('/js/jquery-ui.min.js', $this->jquery->getUiPath());
    }

    public function testNoConflictShouldBeDisabledDefault()
    {
        $this->assertFalse(ZendX_JQuery_View_Helper_JQuery::getNoConflictMode());
    }

    public function testUsingNoConflictMode()
    {
        ZendX_JQuery_View_Helper_JQuery::enableNoConflictMode();
        $this->jquery->setCDNVersion('1.2.6');
        $this->jquery->enable();
        $render = $this->jquery->__toString();

        $this->assertContains('var $j = jQuery.noConflict();', $render);
    }

    public function testDefaultRenderModeShouldIncludeAllBlocks()
    {
        $this->assertEquals(ZendX_JQuery::RENDER_ALL, $this->jquery->getRenderMode());
    }

    public function testShouldAllowSettingRenderMode()
    {
        $this->jquery->setRenderMode(1);
        $this->assertEquals(1, $this->jquery->getRenderMode());
        $this->jquery->setRenderMode(2);
        $this->assertEquals(2, $this->jquery->getRenderMode());
        $this->jquery->setRenderMode(4);
        $this->assertEquals(4, $this->jquery->getRenderMode());
    }

    public function testShouldAllowUsingAddOnLoadStack()
    {
        $this->jquery->addOnLoad('$(document).alert();');
        $this->assertEquals(array('$(document).alert();'), $this->jquery->getOnLoadActions());
    }

    public function testShouldAllowStackingMultipleOnLoad()
    {
        $this->jquery->addOnLoad('1');
        $this->jquery->addOnLoad('2');
        $this->assertEquals(2, count($this->jquery->getOnLoadActions()));
    }

    public function testAddOnLoadEnablesJQuery()
    {
        $this->assertFalse($this->jquery->isEnabled());
        $this->jquery->addOnLoad('1');
        $this->assertTrue($this->jquery->isEnabled());
    }

    public function testShouldAllowCaptureOnLoad()
    {
        $this->jquery->onLoadCaptureStart();
        echo '$(document).alert();';
        $this->jquery->onLoadCaptureEnd();
        $this->assertEquals(array('$(document).alert();'), $this->jquery->getOnLoadActions());
    }

    public function testShouldAllowCaptureJavascript()
    {
        $this->jquery->javascriptCaptureStart();
        echo '$(document).alert();';
        $this->jquery->javascriptCaptureEnd();
        $this->assertEquals(array('$(document).alert();'), $this->jquery->getJavascript());

        $this->jquery->clearJavascript();
        $this->assertEquals(array(), $this->jquery->getJavascript());
    }

    /**
     * @expectedException Zend_Exception
     */
    public function testShouldDisallowNestingCapturesWithException()
    {
        $this->jquery->javascriptCaptureStart();
        $this->jquery->javascriptCaptureStart();
    }

    /**
     * @expectedException Zend_Exception
     */
    public function testShouldDisallowNestingCapturesWithException2()
    {
        $this->jquery->onLoadCaptureStart();
        $this->jquery->onLoadCaptureStart();

        $this->setExpectedException('Zend_Exception');
    }

    public function testAddJavascriptFiles()
    {
        $this->jquery->addJavascriptFile('/js/test.js');
        $this->jquery->addJavascriptFile('/js/test2.js');
        $this->jquery->addJavascriptFile('http://example.com/test3.js');

        $this->assertEquals(array('/js/test.js', '/js/test2.js', 'http://example.com/test3.js'), $this->jquery->getJavascriptFiles());
    }

    public function testAddedJavascriptFilesCanBeCleared()
    {
        $this->jquery->addJavascriptFile('/js/test.js');
        $this->jquery->addJavascriptFile('/js/test2.js');
        $this->jquery->addJavascriptFile('http://example.com/test3.js');

        $this->jquery->clearJavascriptFiles();
        $this->assertEquals(array(), $this->jquery->getJavascriptFiles());
    }

    public function testAddedJavascriptFilesRender()
    {
        $this->jquery->addJavascriptFile('/js/test.js');
        $this->jquery->addJavascriptFile('/js/test2.js');
        $this->jquery->addJavascriptFile('http://example.com/test3.js');

        $this->jquery->enable();

        $render = $this->jquery->__toString();
        $this->assertContains('src="/js/test.js"', $render);
        $this->assertContains('src="/js/test2.js"', $render);
        $this->assertContains('src="http://example.com/test3.js', $render);
    }

    public function testAddStylesheet()
    {
        $this->jquery->addStylesheet('test.css');
        $this->jquery->addStylesheet('test2.css');

        $this->assertEquals(array('test.css', 'test2.css'), $this->jquery->getStylesheets());
    }

    public function testShouldRenderNothingOnDisable()
    {
        $this->jquery->setCDNVersion('1.2.6');
        $this->jquery->addJavascriptFile('test.js');
        $this->jquery->disable();
        $this->assertEquals(strlen(''), strlen($this->jquery->__toString()));
    }

    public function testShouldAllowBasicSetupWithCDN()
    {
        $this->jquery->enable();
        $this->jquery->setCDNVersion('1.2.3');
        $this->jquery->addJavascriptFile('test.js');

        $render = $this->jquery->__toString();

        $this->assertTrue($this->jquery->useCDN());
        $this->assertContains('jquery.min.js', $render);
        $this->assertContains('1.2.3', $render);
        $this->assertContains('test.js', $render);
        $this->assertContains('<script type="text/javascript"', $render);
    }

    public function testShouldAllowUseRenderMode()
    {
        $this->jquery->enable();
        $this->jquery->setCDNVersion('1.2.3');
        $this->jquery->addJavascriptFile('test.js');
        $this->jquery->addJavascript('helloWorld();');
        $this->jquery->addStylesheet('test.css');
        $this->jquery->addOnLoad('alert();');

        // CHeck CDN Usage
        $this->assertTrue($this->jquery->useCDN());

        // Test with Render No Parts
        $this->jquery->setRenderMode(0);
        $this->assertEquals(strlen(''), strlen(trim($this->jquery->__toString())));

        // Test Render Only Library
        $this->jquery->setRenderMode(ZendX_JQuery::RENDER_LIBRARY);
        $render = $this->jquery->__toString();
        $this->assertContains('1.2.3/jquery.min.js', $render);
        $this->assertNotContains('test.css', $render);
        $this->assertNotContains('test.js', $render);
        $this->assertNotContains('alert();', $render);
        $this->assertNotContains('helloWorld();', $render);

        // Test Render Only AddOnLoad
        $this->jquery->setRenderMode(ZendX_JQuery::RENDER_JQUERY_ON_LOAD);
        $render = $this->jquery->__toString();
        $this->assertNotContains('1.2.3/jquery.min.js', $render);
        $this->assertNotContains('test.css', $render);
        $this->assertNotContains('test.js', $render);
        $this->assertContains('alert();', $render);
        $this->assertNotContains('helloWorld();', $render);

        // Test Render Only Javascript
        $this->jquery->setRenderMode(ZendX_JQuery::RENDER_SOURCES);
        $render = $this->jquery->__toString();
        $this->assertNotContains('1.2.3/jquery.min.js', $render);
        $this->assertNotContains('test.css', $render);
        $this->assertContains('test.js', $render);
        $this->assertNotContains('alert();', $render);
        $this->assertNotContains('helloWorld();', $render);

        // Test Render Only Javascript
        $this->jquery->setRenderMode(ZendX_JQuery::RENDER_STYLESHEETS);
        $render = $this->jquery->__toString();
        $this->assertNotContains('1.2.3/jquery.min.js', $render);
        $this->assertContains('test.css', $render);
        $this->assertNotContains('test.js', $render);
        $this->assertNotContains('alert();', $render);
        $this->assertNotContains('helloWorld();', $render);

        // Test Render Library and AddOnLoad
        $this->jquery->setRenderMode(ZendX_JQuery::RENDER_LIBRARY | ZendX_JQuery::RENDER_JQUERY_ON_LOAD);
        $render = $this->jquery->__toString();
        $this->assertContains('1.2.3/jquery.min.js', $render);
        $this->assertNotContains('test.css', $render);
        $this->assertNotContains('test.js', $render);
        $this->assertContains('alert();', $render);
        $this->assertNotContains('helloWorld();', $render);

        // Test Render All
        $this->jquery->setRenderMode(ZendX_JQuery::RENDER_ALL);
        $render = $this->jquery->__toString();
        $this->assertContains('1.2.3/jquery.min.js', $render);
        $this->assertContains('test.css', $render);
        $this->assertContains('test.js', $render);
        $this->assertContains('alert();', $render);
        $this->assertContains('helloWorld();', $render);
    }

    /**
     * @group ZF-5185
     */
    public function testClearAddOnLoadStack()
    {
        $this->jquery->addOnLoad('foo');
        $this->jquery->addOnLoad('bar');
        $this->jquery->addOnLoad('baz');

        $this->assertEquals(array('foo', 'bar', 'baz'), $this->jquery->getOnLoadActions());

        $this->jquery->clearOnLoadActions();
        $this->assertEquals(array(), $this->jquery->getOnLoadActions());
    }

    /**
     * @group ZF-5344
     */
    public function testNoConflictModeIsRecognizedInRenderingOnLoadStackEvent()
    {
        ZendX_JQuery_View_Helper_JQuery::enableNoConflictMode();
        $this->jquery->addOnLoad('foo');
        $this->jquery->addOnLoad('bar');
        $this->jquery->enable();

        $jQueryStack = $this->jquery->__toString();
        $this->assertContains('$j(document).ready(function()', $jQueryStack);

        ZendX_JQuery_View_Helper_JQuery::disableNoConflictMode();

        $jQueryStack =  $this->jquery->__toString();
        $this->assertNotContains('$j(document).ready(function()', $jQueryStack);
    }

    /**
     * @group ZF-5839
     */
    public function testStylesheetShouldRenderCorrectClosingBracketBasedOnHtmlDoctypeDefinition()
    {
        $this->jquery->addStylesheet('test.css');
        $this->view->doctype('HTML4_STRICT');

        $assert = '<link rel="stylesheet" href="test.css" type="text/css" media="screen">';
        $this->jquery->enable();
        $this->assertContains($assert, $this->jquery->__toString());

    }

    /**
     * @group ZF-5839
     */
    public function testStylesheetShouldRenderCorrectClosingBracketBasedOnXHtmlDoctypeDefinition()
    {
        $this->jquery->addStylesheet('test.css');
        $this->view->doctype('XHTML1_STRICT');

        $assert = '<link rel="stylesheet" href="test.css" type="text/css" media="screen" />';
        $this->jquery->enable();
        $this->assertContains($assert, $this->jquery->__toString());
    }

    /**
     * @group ZF-11592
     */
    public function testAddedStylesheetsCanBeCleared()
    {
        $this->jquery->addStylesheet('foo.css');
        $this->jquery->addStylesheet('bar.css');

        $this->jquery->clearStylesheets();

        $this->assertSame(array(), $this->jquery->getStylesheets());
    }

    /**
     * @group ZF-6078
     */
    public function testIncludeJQueryLibraryFromSslPath()
    {
        $this->jquery->setCdnSsl(true);
        $this->jquery->enable();

        $this->assertContains(ZendX_JQuery::CDN_BASE_GOOGLE_SSL, $this->jquery->__toString());
    }

    /**
     * @group ZF-6594
     */
    public function testJQueryGoogleCdnPathIsBuiltCorrectly()
    {
        $jQueryCdnPath = 'http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js';
        $this->jquery->setVersion('1.3.1');
        $this->jquery->enable();

        $this->assertContains($jQueryCdnPath, $this->jquery->__toString());
    }

    /**
     * @group ZF-6594
     */
    public function testJQueryUiGoogleCdnPathIsBuiltCorrectly()
    {
        $jQueryCdnPath = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js';
        $this->jquery->setVersion('1.3.1');
        $this->jquery->enable();
        $this->jquery->setUiVersion('1.7.1');
        $this->jquery->uiEnable();

        $this->assertContains($jQueryCdnPath, $this->jquery->__toString());
    }

    /**
     * @group ZF-6594
     */
    public function testJQueryGoogleCdnSslPathIsBuiltCorrectly()
    {
        $jQueryCdnPath = 'https://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js';
        $this->jquery->setCdnSsl(true);
        $this->jquery->setVersion('1.3.1');
        $this->jquery->enable();

        $this->assertContains($jQueryCdnPath, $this->jquery->__toString());
    }

    /**
     * @group ZF-6594
     */
    public function testJQueryUiGoogleCdnSslPathIsBuiltCorrectly()
    {
        $jQueryCdnPath = 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js';
        $this->jquery->setCdnSsl(true);
        $this->jquery->setVersion('1.3.1');
        $this->jquery->enable();
        $this->jquery->setUiVersion('1.7.1');
        $this->jquery->uiEnable();

        $this->assertContains($jQueryCdnPath, $this->jquery->__toString());
    }
}