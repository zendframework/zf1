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

require_once dirname(__FILE__)."/../../TestHelper.php";

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'ZendX_JQuery_View_AllTests::main');
}

require_once "Zend/Registry.php";
require_once "Zend/View.php";
require_once "ZendX/JQuery.php";
require_once "ZendX/JQuery/View/Helper/JQuery.php";

require_once "ZendX/JQuery/JQueryTest.php";
require_once "ZendX/JQuery/AutoCompleteActionHelperTest.php";
require_once "ZendX/JQuery/View/AllTests.php";
require_once "ZendX/JQuery/Form/AllTests.php";

class ZendX_JQuery_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - ZendX_JQuery');

        $suite->addTestSuite('ZendX_JQuery_JQueryTest');
        $suite->addTestSuite('ZendX_JQuery_View_AllTests');
        $suite->addTestSuite('ZendX_JQuery_Form_AllTests');
        $suite->addTestSuite('ZendX_JQuery_AutoCompleteActionHelperTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'ZendX_JQuery_AllTests::main') {
    ZendX_JQuery_AllTests::main();
}

?>