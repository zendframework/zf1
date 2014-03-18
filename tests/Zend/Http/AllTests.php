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
 * @package    Zend_Http
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Http_AllTests::main');
}

require_once 'Zend/Http/ResponseTest.php';
require_once 'Zend/Http/CookieTest.php';
require_once 'Zend/Http/CookieJarTest.php';
require_once 'Zend/Http/Client/AllTests.php';
require_once 'Zend/Http/Header/AllTests.php';
require_once 'Zend/Http/UserAgent/AllTests.php';

/**
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Http
 */
class Zend_Http_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Http');

        $suite->addTestSuite('Zend_Http_ResponseTest');
        $suite->addTestSuite('Zend_Http_CookieTest');
        $suite->addTestSuite('Zend_Http_CookieJarTest');
        $suite->addTest(Zend_Http_Client_AllTests::suite());
        $suite->addTest(Zend_Http_Header_AllTests::suite());
        $suite->addTest(Zend_Http_UserAgent_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Http_AllTests::main') {
    Zend_Http_AllTests::main();
}
