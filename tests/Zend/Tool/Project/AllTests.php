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
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Tool_Project_AllTests::main');
}

require_once 'Zend/Tool/Project/ProfileTest.php';
require_once 'Zend/Tool/Project/Context/RegistryTest.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Tool
 * @group      Zend_Tool_Project
 */
class Zend_Tool_Project_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Tool_Project');

        // suites here
        $suite->addTestSuite('Zend_Tool_Project_ProfileTest');
        $suite->addTestSuite('Zend_Tool_Project_Context_RepositoryTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Tool_Project_AllTests::main') {
    Zend_Tool_Project_AllTests::main();
}
