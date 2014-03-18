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

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_View_Helper_Navigation_AllTests::main');
}

require_once 'Zend/Navigation/ContainerTest.php';
require_once 'Zend/Navigation/PageFactoryTest.php';
require_once 'Zend/Navigation/PageTest.php';
require_once 'Zend/Navigation/Page/AllTests.php';

require_once 'Zend/View/Helper/Navigation/BreadcrumbsTest.php';
require_once 'Zend/View/Helper/Navigation/LinksTest.php';
require_once 'Zend/View/Helper/Navigation/MenuTest.php';
require_once 'Zend/View/Helper/Navigation/NavigationTest.php';
require_once 'Zend/View/Helper/Navigation/SitemapTest.php';

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_Navigation_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_View_Helper_Navigation');

        $suite->addTestSuite('Zend_View_Helper_Navigation_BreadcrumbsTest');
        $suite->addTestSuite('Zend_View_Helper_Navigation_LinksTest');
        $suite->addTestSuite('Zend_View_Helper_Navigation_MenuTest');
        $suite->addTestSuite('Zend_View_Helper_Navigation_NavigationTest');
        $suite->addTestSuite('Zend_View_Helper_Navigation_SitemapTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_View_Helper_Navigation_AllTests::main') {
    Zend_View_Helper_Navigation_AllTests::main();
}
