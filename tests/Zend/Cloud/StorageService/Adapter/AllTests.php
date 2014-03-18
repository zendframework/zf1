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
 * @package    Zend_Cloud_StorageService
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Cloud_StorageService_Adapter_AllTests::main');
}

/**
 * @see Zend_Cloud_StorageService_Adapter_WindowsAzureTest
 */
require_once 'Zend/Cloud/StorageService/Adapter/WindowsAzureTest.php';

/**
 * @see Zend_Cloud_StorageService_Adapter_S3Test
 */
require_once 'Zend/Cloud/StorageService/Adapter/S3Test.php';

/**
 * @see Zend_Cloud_StorageService_Adapter_RackspaceTest
 */
require_once 'Zend/Cloud/StorageService/Adapter/RackspaceTest.php';

/**
 * @see Zend_Cloud_StorageService_Adapter_FileSystemTest
 */
require_once 'Zend/Cloud/StorageService/Adapter/FileSystemTest.php';

/**
 * @category   Zend
 * @package    Zend_Cloud_StorageService_Adapter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cloud_StorageService_Adapter_AllTests
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Creates and returns this test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Cloud');

        $suite->addTestSuite('Zend_Cloud_StorageService_Adapter_WindowsAzureTest');
        $suite->addTestSuite('Zend_Cloud_StorageService_Adapter_RackspaceTest');
        $suite->addTestSuite('Zend_Cloud_StorageService_Adapter_FileSystemTest');
        $suite->addTestSuite('Zend_Cloud_StorageService_Adapter_S3Test');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Cloud_StorageService_Adapter_AllTests::main') {
    Zend_Cloud_StorageService_Adapter_AllTests::main();
}
