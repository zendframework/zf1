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
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_WindowsAzure_QueueStorageTest::main');
}

/**
 * Test helpers
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once dirname(__FILE__) . '/../../../TestConfiguration.php.dist';
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Service_WindowsAzure_Storage_Queue */
require_once 'Zend/Service/WindowsAzure/Storage/Queue.php';

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @version    $Id$
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_WindowsAzure_QueueStorageTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNTESTS)  {
            $suite  = new PHPUnit_Framework_TestSuite("Zend_Service_WindowsAzure_QueueStorageTest");
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
            try { $storageClient->deleteQueue(TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_PREFIX . $i); } catch (Exception $e) { }
        }
    }

    protected function createStorageInstance()
    {
        $storageClient = null;
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNONPROD) {
            $storageClient = new Zend_Service_WindowsAzure_Storage_Queue(TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_HOST_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_PROD, false, Zend_Service_WindowsAzure_RetryPolicy_RetryPolicyAbstract::retryN(10, 250));
        } else {
            $storageClient = new Zend_Service_WindowsAzure_Storage_Queue(TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_HOST_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_DEV, true, Zend_Service_WindowsAzure_RetryPolicy_RetryPolicyAbstract::retryN(10, 250));
        }
        
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_USEPROXY) {
            $storageClient->setProxy(TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_USEPROXY, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY_PORT, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_PROXY_CREDENTIALS);
        }

        return $storageClient;
    }
    
    protected static $uniqId = 0;
    
    protected function generateName()
    {
        self::$uniqId++;
        return TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_PREFIX . self::$uniqId;
    }
    
    /**
     * Test queue exists
     */
    public function testQueueExists()
    {
    	if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNTESTS) {
            $queueName1 = $this->generateName();
            $queueName2 = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createQueue($queueName1);
            $storageClient->createQueue($queueName2);

            $result = $storageClient->queueExists($queueName1);
            $this->assertTrue($result);
            
            $result = $storageClient->queueExists(md5(time()));
            $this->assertFalse($result);
        }
    }
    
    /**
     * Test create queue
     */
    public function testCreateQueue()
    {
    	if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNTESTS) {
            $queueName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $result = $storageClient->createQueue($queueName);
            $this->assertEquals($queueName, $result->Name);
        }
    }
    
    /**
     * Test create queue if not exists
     */
    public function testCreateQueueIfNotExists()
    {
    	if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNTESTS) {
            $queueName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            
            $result = $storageClient->queueExists($queueName);
            $this->assertFalse($result);
            
            $storageClient->createQueueIfNotExists($queueName);
            
            $result = $storageClient->queueExists($queueName);
            $this->assertTrue($result);
            
            $storageClient->createQueueIfNotExists($queueName);
        }
    }
    
    /**
     * Test set queue metadata
     */
    public function testSetQueueMetadata()
    {
    	if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNTESTS) {
            $queueName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createQueue($queueName);
            
            $storageClient->setQueueMetadata($queueName, array(
                'createdby' => 'PHPAzure',
            ));
            
            $metadata = $storageClient->getQueueMetadata($queueName);
            $this->assertEquals('PHPAzure', $metadata['createdby']);
        }
    }
    
    /**
     * Test get queue
     */
    public function testGetQueue()
    {
    	if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNTESTS) {
            $queueName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createQueue($queueName);
            
            $queue = $storageClient->getQueue($queueName);
            $this->assertEquals($queueName, $queue->Name);
            $this->assertEquals(0, $queue->ApproximateMessageCount);
        }
    }
    
    /**
     * Test list queues
     */
    public function testListQueues()
    {
    	if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNTESTS) {
            $queueName1 = 'testlist1';
            $queueName2 = 'testlist2';
            $queueName3 = 'testlist3';
            $storageClient = $this->createStorageInstance();
            $storageClient->createQueue($queueName1);
            $storageClient->createQueue($queueName2);
            $storageClient->createQueue($queueName3);
            $result1 = $storageClient->listQueues('testlist');
            $result2 = $storageClient->listQueues('testlist', 1);
    
            // cleanup first
            $storageClient->deleteQueue($queueName1);
            $storageClient->deleteQueue($queueName2);
            $storageClient->deleteQueue($queueName3);
            
            $this->assertEquals(3, count($result1));
            $this->assertEquals($queueName2, $result1[1]->Name);
            
            $this->assertEquals(1, count($result2));
        }
    }
    
    /**
     * Test list queues with metadata
     */
    public function testListQueuesWithMetadata()
    {
    	if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNTESTS) {
            $queueName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createQueue($queueName, array(
                'createdby' => 'PHPAzure',
                'ownedby' => 'PHPAzure',
            ));
            
            $result = $storageClient->listQueues($queueName, null, null, 'metadata');
            
            $this->assertEquals('PHPAzure', $result[0]->Metadata['createdby']);
            $this->assertEquals('PHPAzure', $result[0]->Metadata['ownedby']);
        }
    }
    
    /**
     * Test put message
     */
    public function testPutMessage()
    {
    	if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNTESTS) {
            $queueName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createQueue($queueName);
            $storageClient->putMessage($queueName, 'Test message', 120);
            
            sleep(5); // wait for the message to appear in the queue...
            
            $messages = $storageClient->getMessages($queueName);
            $this->assertEquals(1, count($messages));
            $this->assertEquals('Test message', $messages[0]->MessageText);
        }
    }
    
    /**
     * Test get messages
     */
    public function testGetMessages()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNTESTS) {
            $queueName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createQueue($queueName);
            $storageClient->putMessage($queueName, 'Test message 1', 120);
            $storageClient->putMessage($queueName, 'Test message 2', 120);
            $storageClient->putMessage($queueName, 'Test message 3', 120);
            $storageClient->putMessage($queueName, 'Test message 4', 120);
            
            sleep(5); // wait for the messages to appear in the queue...
            
            $messages1 = $storageClient->getMessages($queueName, 2);
            $messages2 = $storageClient->getMessages($queueName, 2);
            $messages3 = $storageClient->getMessages($queueName);
            
            $this->assertEquals(2, count($messages1));
            $this->assertEquals(2, count($messages2));
            $this->assertEquals(0, count($messages3));
        }
    }
    
    /**
     * Test peek messages
     */
    public function testPeekMessages()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNTESTS) {
            $queueName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createQueue($queueName);
            $storageClient->putMessage($queueName, 'Test message 1', 120);
            $storageClient->putMessage($queueName, 'Test message 2', 120);
            $storageClient->putMessage($queueName, 'Test message 3', 120);
            $storageClient->putMessage($queueName, 'Test message 4', 120);
            
            sleep(5); // wait for the messages to appear in the queue...
            
            $messages1 = $storageClient->peekMessages($queueName, 4);
            $hasMessages = $storageClient->hasMessages($queueName);
            $messages2 = $storageClient->getMessages($queueName, 4);
            
            $this->assertEquals(4, count($messages1));
            $this->assertTrue($hasMessages);
            $this->assertEquals(4, count($messages2));
        }
    }
    
    /**
     * Test dequeuecount
     */
    public function testDequeueCount()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNTESTS) {
            $queueName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createQueue($queueName);
            $storageClient->putMessage($queueName, 'Test message 1', 120);
            
            sleep(5); // wait for the message to appear in the queue...
            
            $expectedDequeueCount = 3;
            for ($i = 0; $i < $expectedDequeueCount - 1; $i++) {
	            $storageClient->getMessages($queueName, 1, 1);
	            sleep(3);
            }
            
            $messages = $storageClient->getMessages($queueName, 1);
            
            $this->assertEquals($expectedDequeueCount, $messages[0]->DequeueCount);
        }
    }
    
    /**
     * Test clear messages
     */
    public function testClearMessages()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNTESTS) {
            $queueName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createQueue($queueName);
            $storageClient->putMessage($queueName, 'Test message 1', 120);
            $storageClient->putMessage($queueName, 'Test message 2', 120);
            $storageClient->putMessage($queueName, 'Test message 3', 120);
            $storageClient->putMessage($queueName, 'Test message 4', 120);
            
            sleep(5); // wait for the messages to appear in the queue...
            
            $messages1 = $storageClient->peekMessages($queueName, 4);
            $storageClient->clearMessages($queueName);
            
            sleep(5); // wait for the GC...
            
            $messages2 = $storageClient->peekMessages($queueName, 4);
            
            $this->assertEquals(4, count($messages1));
            $this->assertEquals(0, count($messages2));
        }
    }
    
    /**
     * Test delete message
     */
    public function testDeleteMessage()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_QUEUE_RUNTESTS) {
            $queueName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createQueue($queueName);
            $storageClient->putMessage($queueName, 'Test message 1', 120);
            $storageClient->putMessage($queueName, 'Test message 2', 120);
            $storageClient->putMessage($queueName, 'Test message 3', 120);
            $storageClient->putMessage($queueName, 'Test message 4', 120);
            
            sleep(5); // wait for the messages to appear in the queue...
            
            $messages1 = $storageClient->getMessages($queueName, 2, 10);
            foreach ($messages1 as $message)
            {
                $storageClient->deleteMessage($queueName, $message);
            }
            
            sleep(5); // wait for the GC...
            
            $messages2 = $storageClient->getMessages($queueName, 4);
            
            $this->assertEquals(2, count($messages1));
            $this->assertEquals(2, count($messages2));
        }
    }
}

// Call Zend_Service_WindowsAzure_QueueStorageTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Service_WindowsAzure_QueueStorageTest::main") {
    Zend_Service_WindowsAzure_QueueStorageTest::main();
}
