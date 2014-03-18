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
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Log_Formatter_AllTests::main');
}

require_once 'Zend/Log/Formatter/FirebugTest.php';
require_once 'Zend/Log/Formatter/SimpleTest.php';
require_once 'Zend/Log/Formatter/XmlTest.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 * @group      Zend_Log_Formatter
 */
class Zend_Log_Formatter_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Log_Formatter');

        $suite->addTestSuite('Zend_Log_Formatter_FirebugTest');
        $suite->addTestSuite('Zend_Log_Formatter_SimpleTest');
        $suite->addTestSuite('Zend_Log_Formatter_XmlTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Log_Formatter_AllTests::main') {
    Zend_Log_Formatter_AllTests::main();
}
