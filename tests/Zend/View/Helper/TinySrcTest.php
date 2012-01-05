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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

// Call Zend_View_Helper_TinySrcTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_View_Helper_TinySrcTest::main');
}

/**
 * @see Zend_View_Helper_TinySrc
 */
require_once 'Zend/View/Helper/TinySrc.php';

/**
 * @see Zend_View
 */
require_once 'Zend/View.php';

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_TinySrcTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_TinySrc
     */
    public $helper;

    /**
     * @var Zend_View
     */
    public $view;

    /**
     * Main
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->helper = new Zend_View_Helper_TinySrc();
        $this->view   = new Zend_View();
        $this->view->doctype()->setDoctype(strtoupper("XHTML1_STRICT"));
        $this->helper->setView($this->view);
    }

    public function testCallingHelperMethodWithNoArgumentsReturnsHelperInstance()
    {
        $test = $this->helper->tinySrc();
        $this->assertSame($this->helper, $test);
    }

    public function testHelperUsesServerAndBaseUrlFromHelpersByDefault()
    {
        $base   = $this->view->getHelper('baseUrl');
        $base->setBaseUrl('/foo/bar');

        $server = $this->view->getHelper('serverUrl');
        $server->setScheme('https')
               ->setHost('example.com:8080');

        $test = $this->helper->getBaseUrl();
        $this->assertEquals('https://example.com:8080/foo/bar/', $test);
    }

    public function testAllowsSettingDefaultFormat()
    {
        $this->helper->setDefaultFormat('png')
                     ->setBaseUrl('http://example.com');
        $image = $this->helper->tinySrc('foo.jpg');
        $this->assertContains('/png/', $image);
    }

    public function testSettingInvalidDefaultFormatRaisesException()
    {
        $this->setExpectedException('Zend_View_Exception', 'Invalid format');
        $this->helper->setDefaultFormat('gif');
    }

    public function testPassingNullValueToDefaultFormatClearsSetFormat()
    {
        $this->helper->setDefaultFormat('png')
                     ->setBaseUrl('http://example.com');
        $this->helper->setDefaultFormat(null);
        $image = $this->helper->tinySrc('foo.jpg');
        $this->assertNotContains('/png/', $image);
    }

    public function testAllowsPassingDefaultWidth()
    {
        $this->helper->setBaseUrl('http://example.com')
                     ->setDefaultDimensions('-5');
        $image = $this->helper->tinySrc('foo.jpg');
        $this->assertContains('/-5/', $image);
    }

    /**
     * @dataProvider invalidDimensions
     */
    public function testRaisesExceptionOnInvalidDefaultWidthValue($dim)
    {
        $this->setExpectedException('Zend_View_Exception', 'Invalid dimension');
        $this->helper->setDefaultDimensions($dim);
    }

    public function testAllowsPassingDefaultWidthAndHeight()
    {
        $this->helper->setBaseUrl('http://example.com')
                     ->setDefaultDimensions('5', 'x20');
        $image = $this->helper->tinySrc('foo.jpg');
        $this->assertContains('/5/x20/', $image);
    }

    /**
     * @dataProvider invalidDimensions
     */
    public function testRaisesExceptionOnInvalidDefaultHeightValue($dim)
    {
        $this->setExpectedException('Zend_View_Exception', 'Invalid dimension');
        $this->helper->setDefaultDimensions('10', $dim);
    }

    public function testPassingNullAsDefaultWidthValueClearsBothWidthAndHeight()
    {
        $this->helper->setBaseUrl('http://example.com')
                     ->setDefaultDimensions('5', 'x20');
        $this->helper->setDefaultDimensions(null, 'x20');
        $image = $this->helper->tinySrc('foo.jpg');
        $this->assertNotContains('/5/x20/', $image);
        $this->assertNotContains('/5/', $image);
        $this->assertNotContains('/x20/', $image);
    }

    public function testPassingNullAsDefaultHeightValueClearsHeight()
    {
        $this->helper->setBaseUrl('http://example.com')
                     ->setDefaultDimensions('5', 'x20');
        $this->helper->setDefaultDimensions('5');
        $image = $this->helper->tinySrc('foo.jpg');
        $this->assertNotContains('/5/x20/', $image);
        $this->assertContains('/5/', $image);
    }

    public function testCreatesImageTagByDefault()
    {
        $this->helper->setBaseUrl('http://example.com');
        $image = $this->helper->tinySrc('foo.jpg');
        $this->assertContains('<img src="', $image);
    }

    public function testImageTagObeysDoctype()
    {
        $this->view->doctype('XHTML1_STRICT');
        $this->helper->setBaseUrl('http://example.com');
        $image = $this->helper->tinySrc('foo.jpg');
        $this->assertContains('/>', $image);
    }

    public function testAllowsSpecifyingTagCreation()
    {
        $this->helper->setCreateTag(false);
        $this->helper->setBaseUrl('http://example.com');
        $image = $this->helper->tinySrc('foo.jpg');
        $this->assertNotContains('<img src="', $image);
    }

    public function testPassingOptionsToHelperMethodOverridesDefaults()
    {
        $this->helper->setBaseUrl('http://example.com')
                     ->setCreateTag(false)
                     ->setDefaultDimensions(320, 480);
        $image = $this->helper->tinySrc('foo.jpg', array(
            'base_url'   => 'https://example.org:8080/public',
            'format'     => 'png',
            'width'      => 160,
            'height'     => null,
            'create_tag' => true,
        ));
        $this->assertContains('<img width="160" src="http://i.tinysrc.mobi/png/160/https://example.org:8080/public/foo.jpg"', $image);
    }

    public function testUnknownOptionsPassedToHelperMethodAreTreatedAsImageAttributes()
    {
        $this->helper->setBaseUrl('http://example.com');
        $image = $this->helper->tinySrc('foo.jpg', array(
            'alt'   => 'Alt text for image',
        ));
        $this->assertContains('alt="Alt text for image"', $image);
    }

    public function invalidDimensions()
    {
        return array(
            array('foo'),
            array(true),
            array(array()),
            array(new stdClass),
        );
    }
}

// Call Zend_View_Helper_TinySrcTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Zend_View_Helper_TinySrcTest::main') {
    Zend_View_Helper_TinySrcTest::main();
}
