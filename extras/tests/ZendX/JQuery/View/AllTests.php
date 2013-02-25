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

require_once dirname(__FILE__)."/../../../TestHelper.php";

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'ZendX_JQuery_View_AllTests::main');
}

require_once "Zend/Registry.php";
require_once "Zend/View.php";
require_once "ZendX/JQuery.php";
require_once "ZendX/JQuery/View/Helper/JQuery.php";

require_once "AccordionContainerTest.php";
require_once "AjaxLinkTest.php";
require_once "AutoCompleteTest.php";
require_once "ColorPickerTest.php";
require_once "DatePickerTest.php";
require_once "DialogContainerTest.php";
require_once "jQueryTest.php";
require_once "SliderTest.php";
require_once "SpinnerTest.php";
require_once "TabContainerTest.php";

class ZendX_JQuery_View_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - ZendX_JQuery - View Helpers');

        $suite->addTestSuite('ZendX_JQuery_View_jQueryTest');
        $suite->addTestSuite('ZendX_JQuery_View_AccordionContainerTest');
        $suite->addTestSuite('ZendX_JQuery_View_AjaxLinkTest');
        $suite->addTestSuite('ZendX_JQuery_View_AutoCompleteTest');
        $suite->addTestSuite('ZendX_JQuery_View_ColorPickerTest');
        $suite->addTestSuite('ZendX_JQuery_View_DatePickerTest');
        $suite->addTestSuite('ZendX_JQuery_View_DialogContainerTest');
        $suite->addTestSuite('ZendX_JQuery_View_SliderTest');
        $suite->addTestSuite('ZendX_JQuery_View_SpinnerTest');
        $suite->addTestSuite('ZendX_JQuery_View_TabContainerTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'ZendX_JQuery_View_AllTests::main') {
    ZendX_JQuery_View_AllTests::main();
}