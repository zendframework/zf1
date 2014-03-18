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
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_View_Helper_HtmlObjectTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_HtmlObjectTest::main");
}

require_once 'Zend/View.php';
require_once 'Zend/View/Helper/HtmlObject.php';

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_HtmlObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_HtmlObject
     */
    public $helper;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_HtmlObjectTest");
        PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->view = new Zend_View();
        $this->helper = new Zend_View_Helper_HtmlObject();
        $this->helper->setView($this->view);
    }

    public function tearDown()
    {
        unset($this->helper);
    }

    public function testViewObjectIsSet()
    {
        $this->assertTrue($this->helper->view instanceof Zend_View_Interface);
    }

    public function testMakeHtmlObjectWithoutAttribsWithoutParams()
    {
        $htmlObject = $this->helper->htmlObject('datastring', 'typestring');

        $this->assertContains('<object data="datastring" type="typestring">', $htmlObject);
        $this->assertContains('</object>', $htmlObject);
    }

    public function testMakeHtmlObjectWithAttribsWithoutParams()
    {
        $attribs = array('attribkey1' => 'attribvalue1',
                         'attribkey2' => 'attribvalue2');

        $htmlObject = $this->helper->htmlObject('datastring', 'typestring', $attribs);

        $this->assertContains('<object data="datastring" type="typestring" attribkey1="attribvalue1" attribkey2="attribvalue2">', $htmlObject);
        $this->assertContains('</object>', $htmlObject);
    }

    public function testMakeHtmlObjectWithoutAttribsWithParamsHtml()
    {
        $this->view->doctype(Zend_View_Helper_Doctype::HTML4_STRICT);

        $params = array('paramname1' => 'paramvalue1',
                        'paramname2' => 'paramvalue2');

        $htmlObject = $this->helper->htmlObject('datastring', 'typestring', array(), $params);

        $this->assertContains('<object data="datastring" type="typestring">', $htmlObject);
        $this->assertContains('</object>', $htmlObject);

        foreach ($params as $key => $value) {
            $param = '<param name="' . $key . '" value="' . $value . '">';

            $this->assertContains($param, $htmlObject);
        }
    }

    public function testMakeHtmlObjectWithoutAttribsWithParamsXhtml()
    {
        $this->view->doctype(Zend_View_Helper_Doctype::XHTML1_STRICT);

        $params = array('paramname1' => 'paramvalue1',
                        'paramname2' => 'paramvalue2');

        $htmlObject = $this->helper->htmlObject('datastring', 'typestring', array(), $params);

        $this->assertContains('<object data="datastring" type="typestring">', $htmlObject);
        $this->assertContains('</object>', $htmlObject);

        foreach ($params as $key => $value) {
            $param = '<param name="' . $key . '" value="' . $value . '" />';

            $this->assertContains($param, $htmlObject);
        }
    }

    public function testMakeHtmlObjectWithContent()
    {
        $htmlObject = $this->helper->htmlObject('datastring', 'typestring', array(), array(), 'testcontent');

        $this->assertContains('<object data="datastring" type="typestring">', $htmlObject);
        $this->assertContains('testcontent', $htmlObject);
        $this->assertContains('</object>', $htmlObject);
    }
}

// Call Zend_View_Helper_HtmlObjectTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_HtmlObjectTest::main") {
    Zend_View_Helper_HtmlObjectTest::main();
}
