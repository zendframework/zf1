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
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Http_Header_AllTests::main');
}

/**
 * @see Zend_Http_Header_HeaderValue
 */
require_once 'Zend/Http/Header/HeaderValueTest.php';

/**
 * @see Zend_Http_Header_SetCookie
 */
require_once 'Zend/Http/Header/SetCookieTest.php';

/**
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Http
 * @group      Zend_Http_Header
 */
class Zend_Http_Header_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Http - Header');

        $suite->addTestSuite('Zend_Http_Header_HeaderValueTest');
        $suite->addTestSuite('Zend_Http_Header_SetCookieTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Http_Header_AllTests::main') {
    Zend_Http_Header_AllTests::main();
}
