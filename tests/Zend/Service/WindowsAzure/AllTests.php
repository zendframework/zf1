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
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @version    $Id$
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Service/WindowsAzure/Credentials/AllTests.php';
require_once 'Zend/Service/WindowsAzure/Diagnostics/AllTests.php';
require_once 'Zend/Service/WindowsAzure/RetryPolicyTest.php';
require_once 'Zend/Service/WindowsAzure/StorageTest.php';
require_once 'Zend/Service/WindowsAzure/BlobStorageTest.php';
require_once 'Zend/Service/WindowsAzure/BlobStreamTest.php';
require_once 'Zend/Service/WindowsAzure/BlobStorageSharedAccessTest.php';
require_once 'Zend/Service/WindowsAzure/TableEntityTest.php';
require_once 'Zend/Service/WindowsAzure/DynamicTableEntityTest.php';
require_once 'Zend/Service/WindowsAzure/TableEntityQueryTest.php';
require_once 'Zend/Service/WindowsAzure/TableStorageTest.php';
require_once 'Zend/Service/WindowsAzure/QueueStorageTest.php';
require_once 'Zend/Service/WindowsAzure/SessionHandlerTest.php';

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @version    $Id$
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_WindowsAzure_AllTests
{

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite();

        $suite->addTest(Zend_Service_WindowsAzure_Credentials_AllTests::suite());
        $suite->addTest(Zend_Service_WindowsAzure_Diagnostics_AllTests::suite());

        $suite->addTestSuite('Zend_Service_WindowsAzure_RetryPolicyTest');
        $suite->addTestSuite('Zend_Service_WindowsAzure_StorageTest');
        $suite->addTestSuite('Zend_Service_WindowsAzure_BlobStorageTest');
        $suite->addTestSuite('Zend_Service_WindowsAzure_BlobStorageSharedAccessTest');
        $suite->addTestSuite('Zend_Service_WindowsAzure_BlobStreamTest');
        $suite->addTestSuite('Zend_Service_WindowsAzure_TableEntityTest');
        $suite->addTestSuite('Zend_Service_WindowsAzure_DynamicTableEntityTest');
        $suite->addTestSuite('Zend_Service_WindowsAzure_TableEntityQueryTest');
        $suite->addTestSuite('Zend_Service_WindowsAzure_TableStorageTest');
        $suite->addTestSuite('Zend_Service_WindowsAzure_QueueStorageTest');
        $suite->addTestSuite('Zend_Service_WindowsAzure_SessionHandlerTest');
        return $suite;
    }
}

