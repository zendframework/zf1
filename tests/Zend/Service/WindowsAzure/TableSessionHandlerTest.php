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
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_WindowsAzure_TableSessionHandlerTest::main');
}

/**
 * Test helpers
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once dirname(__FILE__) . '/../../../TestConfiguration.php.dist';
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Service_WindowsAzure_SessionHandler */
require_once 'Zend/Service/WindowsAzure/SessionHandler.php';

/** Zend_Service_WindowsAzure_Storage_Table */
require_once 'Zend/Service/WindowsAzure/Storage/Table.php';

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @version    $Id$
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_WindowsAzure_TableSessionHandlerTest extends PHPUnit_Framework_TestCase
{
    public function __construct()
    {
    }
    
    public static function main()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_SESSIONHANDLER_RUNTESTS) {
            $suite  = new PHPUnit_Framework_TestSuite("Zend_Service_WindowsAzure_TableSessionHandlerTest");
            $result = PHPUnit_TextUI_TestRunner::run($suite);
        }
    }
    
    /**
     * Test setup
     */
    protected function setUp()
    {
    }
    
    /**
     * Test teardown
     */
    protected function tearDown()
    {
        $storageClient = $this->createStorageInstance();
        for ($i = 1; $i <= self::$uniqId; $i++)
        {
            try { $storageClient->deleteTable(TESTS_ZEND_SERVICE_WINDOWSAZURE_SESSIONHANDLER_TABLENAME_PREFIX . $i); } catch (Exception $e) { }
        }
    }
    
    protected function createStorageInstance()
    {
        $storageClient = null;
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_SESSIONHANDLER_RUNONPROD) {
            $storageClient = new Zend_Service_WindowsAzure_Storage_Table(TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_HOST_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_PROD, false, Zend_Service_WindowsAzure_RetryPolicy_RetryPolicyAbstract::retryN(10, 250));
        } else {
            $storageClient = new Zend_Service_WindowsAzure_Storage_Table(TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_HOST_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_DEV, true, Zend_Service_WindowsAzure_RetryPolicy_RetryPolicyAbstract::retryN(10, 250));
        }
        
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_USEPROXY) {
            $storageClient->setProxy(TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_USEPROXY, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY_PORT, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY_CREDENTIALS);
        }

        return $storageClient;
    }
    
    protected function createSessionHandler($storageInstance, $tableName)
    {
        $sessionHandler = new Zend_Service_WindowsAzure_SessionHandler(
            $storageInstance,
            $tableName
        );
        return $sessionHandler;
    }
    
    protected static $uniqId = 0;
    
    protected function generateName()
    {
        self::$uniqId++;
        return TESTS_ZEND_SERVICE_WINDOWSAZURE_SESSIONHANDLER_TABLENAME_PREFIX . self::$uniqId;
    }
    
    /**
     * Test register
     */
    public function testRegister()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_SESSIONHANDLER_RUNTESTS) {
            $storageClient = $this->createStorageInstance();
            $tableName = $this->generateName();
            $sessionHandler = $this->createSessionHandler($storageClient, $tableName);
            $result = $sessionHandler->register();
            
            $this->assertTrue($result);
        }
    }
    
    /**
     * Test open
     */
    public function testOpen()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_SESSIONHANDLER_RUNTESTS) {
            $storageClient = $this->createStorageInstance();
            $tableName = $this->generateName();
            $sessionHandler = $this->createSessionHandler($storageClient, $tableName);
            $result = $sessionHandler->open();

            $this->assertTrue($result);
            
            
            $verifyResult = $storageClient->listTables();
            $this->assertEquals($tableName, $verifyResult[0]->Name);
        }
    }
    
    /**
     * Test close
     */
    public function testClose()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_SESSIONHANDLER_RUNTESTS) {
            $storageClient = $this->createStorageInstance();
            $tableName = $this->generateName();
            $sessionHandler = $this->createSessionHandler($storageClient, $tableName);
            $sessionHandler->open();
            $result = $sessionHandler->close();
            
            $this->assertTrue($result);
        }
    }
    
    /**
     * Test read
     */
    public function testRead()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_SESSIONHANDLER_RUNTESTS) {
            $storageClient = $this->createStorageInstance();
            $tableName = $this->generateName();
            $sessionHandler = $this->createSessionHandler($storageClient, $tableName);
            $sessionHandler->open();
            
            $sessionId = $this->session_id();
            $sessionData = serialize( 'PHPAzure' );
            $sessionHandler->write($sessionId, $sessionData);
            
            $result = unserialize( $sessionHandler->read($sessionId) );
            
            $this->assertEquals('PHPAzure', $result);
        }
    }
    
    /**
     * Test write
     */
    public function testWrite()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_SESSIONHANDLER_RUNTESTS) {
            $storageClient = $this->createStorageInstance();
            $tableName = $this->generateName();
            $sessionHandler = $this->createSessionHandler($storageClient, $tableName);
            $sessionHandler->open();
            
            $sessionId = $this->session_id();
            $sessionData = serialize( 'PHPAzure' );
            $sessionHandler->write($sessionId, $sessionData);
            
            
            $verifyResult = $storageClient->retrieveEntities($tableName);
            $this->assertEquals(1, count($verifyResult));
        }
    }
    
    /**
     * Test write large
     */
    public function testWriteLarge()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_SESSIONHANDLER_RUNTESTS) {
            $storageClient = $this->createStorageInstance();
            $tableName = $this->generateName();
            $sessionHandler = $this->createSessionHandler($storageClient, $tableName);
            $sessionHandler->open();
            
            $sessionId = $this->session_id();
            
            $sessionData = '';
            for ($i = 0; $i < Zend_Service_WindowsAzure_SessionHandler::MAX_TS_PROPERTY_SIZE; $i++) {
            	$sessionData .= 'a';
            }
            $sessionData = serialize( $sessionData );
            
            $exceptionThrown = false;
            try {
            	$sessionHandler->write($sessionId, $sessionData);
            } catch (Exception $ex) {
            	$exceptionThrown = true;
            }
            
            $this->assertTrue($exceptionThrown);
        }
    }
    
    /**
     * Test destroy
     */
    public function testDestroy()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_SESSIONHANDLER_RUNTESTS) {
            $storageClient = $this->createStorageInstance();
            $tableName = $this->generateName();
            $sessionHandler = $this->createSessionHandler($storageClient, $tableName);
            $sessionHandler->open();
            
            $sessionId = $this->session_id();
            $sessionData = serialize( 'PHPAzure' );
            $sessionHandler->write($sessionId, $sessionData);
            
            $result = $sessionHandler->destroy($sessionId);
            $this->assertTrue($result);
            
            $verifyResult = $storageClient->retrieveEntities($tableName);
            $this->assertEquals(0, count($verifyResult));
        }
    }
    
    /**
     * Test gc
     */
    public function testGc()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_SESSIONHANDLER_RUNTESTS) {
            $storageClient = $this->createStorageInstance();
            $tableName = $this->generateName();
            $sessionHandler = $this->createSessionHandler($storageClient, $tableName);
            $sessionHandler->open();
            
            $sessionId = $this->session_id();
            $sessionData = serialize( 'PHPAzure' );
            $sessionHandler->write($sessionId, $sessionData);
            
            sleep(1); // let time() tick
            
            $result = $sessionHandler->gc(0);
            $this->assertTrue($result);
            
            $verifyResult = $storageClient->retrieveEntities($tableName);
            $this->assertEquals(0, count($verifyResult));
        }
    }

    protected function session_id()
    {
        return md5(self::$uniqId);
    }
}

// Call Zend_Service_WindowsAzure_TableSessionHandlerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Service_WindowsAzure_TableSessionHandlerTest::main") {
    Zend_Service_WindowsAzure_TableSessionHandlerTest::main();
}
