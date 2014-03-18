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
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_WindowsAzure_TableStorageTest::main');
}

/**
 * Test helpers
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once dirname(__FILE__) . '/../../../TestConfiguration.php.dist';
require_once 'PHPUnit/Framework/TestCase.php';

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
class Zend_Service_WindowsAzure_TableStorageTest extends PHPUnit_Framework_TestCase
{
    public function __construct()
    {
    }
    
    public static function main()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $suite  = new PHPUnit_Framework_TestSuite("Zend_Service_WindowsAzure_TableStorageTest");
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
            try { $storageClient->deleteTable(TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_TABLENAME_PREFIX . $i); } catch (Exception $e) { }
        }
    }
    
    protected function createStorageInstance()
    {
        $storageClient = null;
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNONPROD) {
            $storageClient = new Zend_Service_WindowsAzure_Storage_Table(TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_HOST_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_PROD, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_PROD, false, Zend_Service_WindowsAzure_RetryPolicy_RetryPolicyAbstract::retryN(10, 250));
        } else {
            $storageClient = new Zend_Service_WindowsAzure_Storage_Table(TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_HOST_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_ACCOUNT_DEV, TESTS_ZEND_SERVICE_WINDOWSAZURE_STORAGE_KEY_DEV, true, Zend_Service_WindowsAzure_RetryPolicy_RetryPolicyAbstract::retryN(10, 250));
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
        return TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_TABLENAME_PREFIX . self::$uniqId;
    }
    
    /**
     * Test create table
     */
    public function testCreateTable()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            
            $result = $storageClient->createTable($tableName);
            $this->assertEquals($tableName, $result->Name);
            
            $result = $storageClient->listTables();
            $this->assertEquals(1, count($result));
            $this->assertEquals($tableName, $result[0]->Name);
        }
    }
    
    /**
     * Test create table if not exists
     */
    public function testCreateTableIfNotExists()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            
            $result = $storageClient->tableExists($tableName);
            $this->assertFalse($result);
            
            $storageClient->createTableIfNotExists($tableName);
            
            $result = $storageClient->tableExists($tableName);
            $this->assertTrue($result);
            
            $storageClient->createTableIfNotExists($tableName);
        }
    }
    
    /**
     * Test table exists
     */
    public function testTableExists()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName1 = $this->generateName();
            $tableName2 = $this->generateName();
            $storageClient = $this->createStorageInstance();
            
            $storageClient->createTable($tableName1);
            $storageClient->createTable($tableName2);

            $result = $storageClient->tableExists($tableName2);
            $this->assertTrue($result);
            
            $result = $storageClient->tableExists(md5(time()));
            $this->assertFalse($result);
        }
    }
    
    /**
     * Test list tables
     */
    public function testListTables()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName1 = $this->generateName();
            $tableName2 = $this->generateName();
            $storageClient = $this->createStorageInstance();
            
            $storageClient->createTable($tableName1);
            $storageClient->createTable($tableName2);

            $result = $storageClient->listTables();
            $this->assertEquals(2, count($result));
            $this->assertEquals($tableName1, $result[0]->Name);
            $this->assertEquals($tableName2, $result[1]->Name);
        }
    }
    
    /**
     * Test delete table
     */
    public function testDeleteTable()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            
            $storageClient->createTable($tableName);
            $storageClient->deleteTable($tableName);
            
            $result = $storageClient->listTables();
            $this->assertEquals(0, count($result));
        }
    }
    
    /**
     * Test insert entity
     */
    public function testInsertEntity()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(1);
            $entity = $entities[0];
            
            $result = $storageClient->insertEntity($tableName, $entity);

            $this->assertNotEquals('0001-01-01T00:00:00', $result->getTimestamp());
            $this->assertNotEquals('', $result->getEtag());
            $this->assertEquals($entity, $result);
        }
    }
    
    /**
     * Test insert entity, with XML in content. This should not break the XML sent to Windows Azure.
     */
    public function testInsertEntity_Security_HtmlSpecialChars()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(1);
            $entity = $entities[0];
            $entity->FullName = 'XML <test>'; // this should work without breaking the XML
            
            $result = $storageClient->insertEntity($tableName, $entity);

            $this->assertNotEquals('0001-01-01T00:00:00', $result->getTimestamp());
            $this->assertNotEquals('', $result->getEtag());
            $this->assertEquals($entity, $result);
        }
    }
    
    /**
     * Test delete entity, not taking etag into account
     */
    public function testDeleteEntity_NoEtag()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(1);
            $entity = $entities[0];
            
            $result = $storageClient->insertEntity($tableName, $entity);
            
            $this->assertEquals($entity, $result);
            
            $storageClient->deleteEntity($tableName, $entity);
        }
    }
    
    /**
     * Test delete entity, taking etag into account
     */
    public function testDeleteEntity_Etag()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(1);
            $entity = $entities[0];
            
            $result = $storageClient->insertEntity($tableName, $entity);

            $this->assertEquals($entity, $result);

            // Set "old" etag
            $entity->setEtag('W/"datetime\'2009-05-27T12%3A15%3A15.3321531Z\'"');
            
            $exceptionThrown = false;
            try {
                $storageClient->deleteEntity($tableName, $entity, true);
            } catch (Exception $ex) {
                $exceptionThrown = true;
            }
            $this->assertTrue($exceptionThrown);
        }
    }
    
    /**
     * Test retrieve entity by id
     */
    public function testRetrieveEntityById()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS)  {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(1);
            $entity = $entities[0];
            
            $storageClient->insertEntity($tableName, $entity);
            
            $result = $storageClient->retrieveEntityById($tableName, $entity->getPartitionKey(), $entity->getRowKey(), 'TSTest_TestEntity');
            $this->assertEquals($entity, $result);
        }
    }
    
    /**
     * Test retrieve entity by id, havind less properties than the original entity.
     * Related to issue: http://phpazure.codeplex.com/workitem/5021
     */
    public function testRetrieveEntityById_DifferentProperties()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS)  {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(1);
            $entity = $entities[0];
            
            $storageClient->insertEntity($tableName, $entity);
            
            $storageClient->setThrowExceptionOnMissingData(false);
            
            $result = $storageClient->retrieveEntityById($tableName, $entity->getPartitionKey(), $entity->getRowKey(), 'TSTest_TestEntity2');
            $this->assertEquals($entity->FullName, $result->FullName);
        }
    }
    
    /**
     * Test retrieve entity by id (> 256 key characters)
     */
    public function testRetrieveEntityById_Large()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS)  {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(1);
            $entity = $entities[0];
            $entity->setPartitionKey(str_repeat('a', 200));
            $entity->setRowKey(str_repeat('a', 200));
            
            $storageClient->insertEntity($tableName, $entity);
            
            $result = $storageClient->retrieveEntityById($tableName, $entity->getPartitionKey(), $entity->getRowKey(), 'TSTest_TestEntity');
            $this->assertEquals($entity, $result);
        }
    }
    
    /**
     * Test retrieve entity by id, DynamicTableEntity
     */
    public function testRetrieveEntityById_DynamicTableEntity()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS)  {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(1);
            $entity = $entities[0];
            
            $storageClient->insertEntity($tableName, $entity);
            
            $result = $storageClient->retrieveEntityById($tableName, $entity->getPartitionKey(), $entity->getRowKey());
            $this->assertEquals($entity->FullName, $result->Name);
            $this->assertTrue($result instanceof Zend_Service_WindowsAzure_Storage_DynamicTableEntity);
        }
    }
    
    /**
     * Test update entity, not taking etag into account
     */
    public function testUpdateEntity_NoEtag()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(1);
            $entity = $entities[0];
            
            $storageClient->insertEntity($tableName, $entity);
            $entity->Age = 0;
            
            $result = $storageClient->updateEntity($tableName, $entity);

            $this->assertNotEquals('0001-01-01T00:00:00', $result->getTimestamp());
            $this->assertNotEquals('', $result->getEtag());
            $this->assertEquals(0, $result->Age);
            $this->assertEquals($entity, $result);
        }
    }
    
    /**
     * Test update entity, taking etag into account
     */
    public function testUpdateEntity_Etag()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(1);
            $entity = $entities[0];
            
            $storageClient->insertEntity($tableName, $entity);
            $entity->Age = 0;
            
            // Set "old" etag
            $entity->setEtag('W/"datetime\'2009-05-27T12%3A15%3A15.3321531Z\'"');
            
            $exceptionThrown = false;
            try {
                $storageClient->updateEntity($tableName, $entity, true);
            } catch (Exception $ex) {
                $exceptionThrown = true;
            }
            $this->assertTrue($exceptionThrown);
        }
    }
    
    /**
     * Test merge entity, not taking etag into account
     */
    public function testMergeEntity_NoEtag()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(1);
            $entity = $entities[0];
            
            $storageClient->insertEntity($tableName, $entity);
            
            $dynamicEntity = new Zend_Service_WindowsAzure_Storage_DynamicTableEntity($entity->getPartitionKey(), $entity->getRowKey());
            $dynamicEntity->Myproperty = 10;
            $dynamicEntity->Otherproperty = "Test";
            $dynamicEntity->Age = 0;
            
            $storageClient->mergeEntity($tableName, $dynamicEntity, false, array('Myproperty', 'Otherproperty')); // only update 'Myproperty' and 'Otherproperty'
            
            $result = $storageClient->retrieveEntityById($tableName, $entity->getPartitionKey(), $entity->getRowKey());

            $this->assertNotEquals('0001-01-01T00:00:00', $result->getTimestamp());
            $this->assertNotEquals('', $result->getEtag());
            $this->assertNotEquals(0, $result->Age);
            $this->assertEquals($entity->FullName, $result->Name);
            $this->assertEquals($dynamicEntity->Myproperty, $result->Myproperty);
            $this->assertEquals($dynamicEntity->Otherproperty, $result->Otherproperty);
        }
    }
    
    /**
     * Test merge entity, taking etag into account
     */
    public function testMergeEntity_Etag()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(1);
            $entity = $entities[0];
            
            $storageClient->insertEntity($tableName, $entity);
            
            $dynamicEntity = new Zend_Service_WindowsAzure_Storage_DynamicTableEntity($entity->getPartitionKey(), $entity->getRowKey());
            $dynamicEntity->Myproperty = 10;
            $dynamicEntity->Otherproperty = "Test";
            $dynamicEntity->Age = 0;
            
            // Set "old" etag
            $entity->setEtag('W/"datetime\'2009-05-27T12%3A15%3A15.3321531Z\'"');
            
            $exceptionThrown = false;
            try {
                $storageClient->mergeEntity($tableName, $dynamicEntity, true);
            } catch (Exception $ex) {
                $exceptionThrown = true;
            }
            $this->assertTrue($exceptionThrown);
        }
    }
    
    /**
     * Test retrieve entities, all
     */
    public function testRetrieveEntities_All()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(20);
            foreach ($entities as $entity)
            {
                $storageClient->insertEntity($tableName, $entity);
            }
            
            $result = $storageClient->retrieveEntities($tableName, 'TSTest_TestEntity');
            $this->assertEquals(20, count($result));
        }
    }
    
    /**
     * Test retrieve entities, all, DynamicTableEntity
     */
    public function testRetrieveEntities_All_DynamicTableEntity()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(20);
            foreach ($entities as $entity)
            {
                $storageClient->insertEntity($tableName, $entity);
            }
            
            $result = $storageClient->retrieveEntities($tableName);
            $this->assertEquals(20, count($result));
            
            foreach ($result as $item)
            {
                $this->assertTrue($item instanceof Zend_Service_WindowsAzure_Storage_DynamicTableEntity);
            }
        }
    }
    
    /**
     * Test retrieve entities, filtered
     */
    public function testRetrieveEntities_Filtered()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(5);
            foreach ($entities as $entity)
            {
                $storageClient->insertEntity($tableName, $entity);
            }
            
            $result = $storageClient->retrieveEntities($tableName, 'PartitionKey eq \'' . $entities[0]->getPartitionKey() . '\' and RowKey eq \'' . $entities[0]->getRowKey() . '\'', 'TSTest_TestEntity');
            $this->assertEquals(1, count($result));
        }
    }
    
    /**
     * Test retrieve entities, fluent interface
     */
    public function testRetrieveEntities_Fluent1()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(10);
            foreach ($entities as $entity)
            {
                $storageClient->insertEntity($tableName, $entity);
            }
            
            $result = $storageClient->retrieveEntities(
                $storageClient->select()
                              ->from($tableName)
                              ->where('Name eq ?', $entities[0]->FullName)
                              ->andWhere('RowKey eq ?', $entities[0]->getRowKey()),
                'TSTest_TestEntity'
            );
            
            $this->assertEquals(1, count($result));
            $this->assertEquals($entities[0], $result[0]);
        }
    }
    
    /**
     * Test retrieve entities, fluent interface
     */
    public function testRetrieveEntities_Fluent2()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(10);
            foreach ($entities as $entity)
            {
                $storageClient->insertEntity($tableName, $entity);
            }
            
            $result = $storageClient->retrieveEntities(
                $storageClient->select()
                              ->from($tableName)
                              ->where('Name eq ?', $entities[0]->FullName)
                              ->andWhere('PartitionKey eq ?', $entities[0]->getPartitionKey()),
                'TSTest_TestEntity'
            );
            
            $this->assertEquals(1, count($result));
            $this->assertEquals($entities[0], $result[0]);
        }
    }
    
    /**
     * Test retrieve entities, fluent interface, top specification
     */
    public function testRetrieveEntities_Fluent_Top()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(10);
            foreach ($entities as $entity)
            {
                $storageClient->insertEntity($tableName, $entity);
            }
            
            $result = $storageClient->retrieveEntities(
                $storageClient->select()->top(4)
                              ->from($tableName),
                'TSTest_TestEntity'
            );
            
            $this->assertEquals(4, count($result));
        }
    }
    
    /**
     * Test batch commit, success
     */
    public function testBatchCommit_Success()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(20);
            $entities1 = array_slice($entities, 0, 10);
            $entities2 = array_slice($entities, 10, 10);
            
            // Insert entities
            foreach ($entities1 as $entity)
            {
                $storageClient->insertEntity($tableName, $entity);
            }
            
            // Start batch
            $batch = $storageClient->startBatch();
            $this->assertTrue($batch instanceof Zend_Service_WindowsAzure_Storage_Batch);
            
            // Insert entities in batch
            foreach ($entities2 as $entity)
            {
                $storageClient->insertEntity($tableName, $entity);
            }
            
            // Delete entities
            foreach ($entities1 as $entity)
            {
                $storageClient->deleteEntity($tableName, $entity);
            }
            
            // Commit
            $batch->commit();
            
            // Verify
            $result = $storageClient->retrieveEntities($tableName);
            $this->assertEquals(10, count($result));
        }
    }
    
    /**
     * Test batch rollback, success
     */
    public function testBatchRollback_Success()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(10);
            
            // Start batch
            $batch = $storageClient->startBatch();
            $this->assertTrue($batch instanceof Zend_Service_WindowsAzure_Storage_Batch);
            
            // Insert entities in batch
            foreach ($entities as $entity)
            {
                $storageClient->insertEntity($tableName, $entity);
            }
            
            // Rollback
            $batch->rollback();
            
            // Verify
            $result = $storageClient->retrieveEntities($tableName);
            $this->assertEquals(0, count($result));
        }
    }
    
    /**
     * Test batch commit, fail updates
     */
    public function testBatchCommit_FailUpdates()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(10);
            foreach ($entities as $entity)
            {
                $storageClient->insertEntity($tableName, $entity);
            }
            
            // Make some entity updates with "old" etags
            $entities[0]->Age = 0;
            $entities[0]->setEtag('W/"datetime\'2009-05-27T12%3A15%3A15.3321531Z\'"');
            $entities[1]->Age = 0;
            $entities[1]->setEtag('W/"datetime\'2009-05-27T12%3A15%3A15.3321531Z\'"');
            $entities[2]->Age = 0;
            
            // Start batch
            $batch = $storageClient->startBatch();
            $this->assertTrue($batch instanceof Zend_Service_WindowsAzure_Storage_Batch);
            
            // Update entities in batch
            $storageClient->updateEntity($tableName, $entities[0], true);
            $storageClient->updateEntity($tableName, $entities[1], true);
            $storageClient->updateEntity($tableName, $entities[2], true);
            
            // Commit
            $exceptionThrown = false;
            try {
                $batch->commit();
            } catch (Exception $ex) {
                $exceptionThrown = true;
            }
            $this->assertTrue($exceptionThrown);
        }
    }
    
    /**
     * Test batch commit, fail partition
     */
    public function testBatchCommit_FailPartition()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(10);
            
            // Start batch
            $batch = $storageClient->startBatch();
            $this->assertTrue($batch instanceof Zend_Service_WindowsAzure_Storage_Batch);
            
            // Insert entities in batch
            foreach ($entities as $entity)
            {
                $entity->setPartitionKey('partition' . rand(1, 9));
                $storageClient->insertEntity($tableName, $entity);
            }
            
            // Commit
            $exceptionThrown = false;
            try {
                $batch->commit();
            } catch (Exception $ex) {
                $exceptionThrown = true;
            }
            $this->assertTrue($exceptionThrown);
            
            // Verify
            $result = $storageClient->retrieveEntities($tableName);
            $this->assertEquals(0, count($result));
        }
    }
    
    /**
     * Test continuation tokens
     */
    public function testContinuationTokens()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS) {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $numberOfEntities = 2500;
            $numberOfEntitiesPerBatch = 100;
            $entities = $this->_generateEntities($numberOfEntities);

            // Insert test data
            for ($i = 0; $i < $numberOfEntities; $i+=$numberOfEntitiesPerBatch) {
            	$batch = $storageClient->startBatch();
            
            	$entitiesTemp = array_slice($entities, $i, $numberOfEntitiesPerBatch);
	            foreach ($entitiesTemp as $entity)
	            {
	                $storageClient->insertEntity($tableName, $entity);
	            }
            	
            	$batch->commit();
            }
            
            // Verify
            $result = $storageClient->retrieveEntities($tableName);
            $this->assertEquals(2500, count($result));
            
            $result = $storageClient->retrieveEntities(
                $storageClient->select()
                              ->from($tableName)
                              ->where('Age ne 0')
            );
            $this->assertEquals(2500, count($result));
        }
    }
    
    /**
     * Test retrieve entity by id - curly brackets
     */
    public function testRetrieveEntityByIdCurlyBrackets()
    {
        if (TESTS_ZEND_SERVICE_WINDOWSAZURE_TABLE_RUNTESTS)  {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entities = $this->_generateEntities(1);
            $entity = $entities[0];
            $entity->setRowKey('-1305521559_{47418E06-58CC-40CA-AE7E-F2B0BD5FD885}');
            
            $storageClient->insertEntity($tableName, $entity);
            
            $result = $storageClient->retrieveEntityById($tableName, $entity->getPartitionKey(), $entity->getRowKey(), 'TSTest_TestEntity');
            $this->assertEquals($entity, $result);
        }
    }
    
    /**
     * Generate entities
     * 
     * @param int 		$amount Number of entities to generate
     * @return array 			Array of TSTest_TestEntity
     */
    protected function _generateEntities($amount = 1)
    {
        $returnValue = array();
        
        for ($i = 0; $i < $amount; $i++)
        {
            $entity = new TSTest_TestEntity('partition1', 'row' . ($i + 1));
            $entity->FullName = md5(uniqid(rand(), true));
            $entity->Age      = rand(1, 130);
            $entity->Visible  = rand(1,2) == 1;
            $entity->DateInService = new DateTime('now', new DateTimeZone('UTC'));
            
            $returnValue[] = $entity;
        }
        
        return $returnValue;
    }
}

/**
 * Test Zend_Service_WindowsAzure_Storage_TableEntity class
 */
class TSTest_TestEntity extends Zend_Service_WindowsAzure_Storage_TableEntity
{
    /**
     * @azure Name
     */
    public $FullName;
    
    /**
     * @azure Age Edm.Int64
     */
    public $Age;
    
    /**
     * @azure Visible Edm.Boolean
     */
    public $Visible = false;
    
    /**
     * @azure DateInService Edm.DateTime
     */
    public $DateInService;
}

/**
 * Test Zend_Service_WindowsAzure_Storage_TableEntity class
 */
class TSTest_TestEntity2 extends Zend_Service_WindowsAzure_Storage_TableEntity
{
    /**
     * @azure Name
     */
    public $FullName;
}

// Call Zend_Service_WindowsAzure_TableStorageTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Service_WindowsAzure_TableStorageTest::main") {
    Zend_Service_WindowsAzure_TableStorageTest::main();
}
