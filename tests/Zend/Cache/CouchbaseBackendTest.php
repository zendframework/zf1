<?php

/**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Backend/Couchbase.php';

/**
 * Common tests for backends
 */
require_once 'CommonExtendedBackendTest.php';

class Zend_Cache_Backend_CouchbaseBackendTest extends Zend_Cache_CommonExtendedBackendTest
{
	/**
	 *
	 * @var Zend_Cache_Backend_Couchbase
	 */
	protected $_backend;

	function setUp()
	{
		parent::setUp();
		$this->_backend = new Zend_Cache_Backend_Couchbase();
	}

	function tearDown()
	{
		$this->_backend->clean('all');
	}

	function testExtendZendCacheBackendFile()
	{
		$this->assertInstanceOf('Zend_Cache_Backend_Couchbase', $this->_backend);
	}

	function testSaveCouchbase()
	{
		$data = 'foo';
		$data1 = 'foo1';
		$id = '125';
		$success = $this->_backend->save($data, $id);
		$this->assertTrue($success);
		$success = $this->_backend->save($data1, $id);
		$this->assertTrue($success);
		$loaded = $this->_backend->load($id);
		$this->assertEquals($data1, $loaded);
	}

	function testTestIDFromCouch()
	{
		$data = 'foo';
		$id = '125';
		$success = $this->_backend->save($data, $id);
		$this->assertTrue($success);
		$loaded = $this->_backend->load($id);
		$this->assertEquals($data, $loaded);
		$remove = $this->_backend->remove($id);
		$this->assertTrue($remove);
		$loaded = $this->_backend->test($id);
		$this->assertFalse($loaded);
	}

	function testLoadIDFromCouchbase()
	{
		$data = 'foo';
		$id = '125';
		$success = $this->_backend->save($data, $id);
		$this->assertTrue($success);
		$remove = $this->_backend->remove($id);
		$this->assertTrue($remove);
		$loaded = $this->_backend->load($id);
		$this->assertFalse($loaded);
	}

	function testIncrementDecrementFromCouchbase()
	{
		$id = '130';
		$success = $this->_backend->increment($id);
		$this->assertEquals(1, $success);
		$loaded = $this->_backend->load($id);
		$this->assertEquals(1, $loaded);
		$success = $this->_backend->increment($id);
		$this->assertEquals(2, $success);
		$success = $this->_backend->increment($id);
		$this->assertEquals(3, $success);
		$success = $this->_backend->increment($id, 2);
		$this->assertEquals(5, $success);
		$success = $this->_backend->increment($id, 2, 0);
		$this->assertEquals(7, $success);
	}

	function testCleanCouchbase()
	{
		$id = '125';
		$data = 'foo';
		$success = $this->_backend->save($data, $id);
		$this->assertTrue($success);
		$loaded = $this->_backend->load($id);
		$this->assertEquals($data, $loaded);

		$this->_backend->clean('all');
		$loaded = $this->_backend->load($id);
		$this->assertFalse($loaded);
	}

	function testTouchCouchbase()
	{
		$id = '125';
		$data = 'foo';
		$success = $this->_backend->save($data, $id);
		$this->assertTrue($success);
		$result = $this->_backend->touch($id, 1000);
		$this->assertTrue($result);
	}

	function testGetCapabilities()
	{
		$expected = [
			'automatic_cleaning' => false,
			'tags' => false,
			'expired_read' => false,
			'priority' => false,
			'infinite_lifetime' => false,
			'get_list' => false
		];
		$this->assertEquals($expected, $this->_backend->getCapabilities());
	}

	function testGetFillingPercentage()
	{
		$this->assertEquals([], $this->_backend->getFillingPercentage());
	}

	function testGetIds()
	{
		$this->assertEquals([], $this->_backend->getIds());
	}

	function testGetTags()
	{
		$this->assertEquals([], $this->_backend->getTags());
	}

	function testGetIdsMatchingTags()
	{
		$this->assertEquals([], $this->_backend->getIdsMatchingTags());
	}

	function testGetIdsNotMatchingTags()
	{
		$this->assertEquals([], $this->_backend->getIdsNotMatchingTags());
	}

	function testGetIdsMatchingAnyTags()
	{
		$this->assertEquals([], $this->_backend->getIdsMatchingAnyTags());
	}

	function testIsAutomaticCleaningAvailable()
	{
		$this->assertFalse($this->_backend->isAutomaticCleaningAvailable());
	}

	function testGetMetadatas()
	{
		$this->assertEquals([], $this->_backend->getMetadatas('foo'));
	}

}