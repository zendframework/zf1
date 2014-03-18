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
 * @package    Zend_ProgressBar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_ProgressBar_Adapter_AllTests::main');
}

require_once 'Zend/ProgressBar/Adapter/ConsoleTest.php';
require_once 'Zend/ProgressBar/Adapter/JsPushTest.php';
require_once 'Zend/ProgressBar/Adapter/JsPullTest.php';

/**
 * @category   Zend
 * @package    Zend_ProgressBar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_ProgressBar
 */
class Zend_ProgressBar_Adapter_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_ProgressBar_Adapter');
        $suite->addTestSuite('Zend_ProgressBar_Adapter_ConsoleTest');
        $suite->addTestSuite('Zend_ProgressBar_Adapter_JsPushTest');
        $suite->addTestSuite('Zend_ProgressBar_Adapter_JsPullTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_ProgressBar_Adapter_AllTests::main') {
    Zend_ProgressBar_Adapter_AllTests::main();
}
