<?php


/**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Backend/Rediscluster.php';

/**
 * Common tests for backends
 */
require_once 'CommonExtendedBackendTest.php';

class Zend_Cache_Backend_RedisclusterBackendTest extends Zend_Cache_CommonExtendedBackendTest
{
	/**
	 *
	 * @var TP24_Cache_Backend_Rediscluster
	 */
	protected $_instance;

	public function __construct($name = null, array $data = array(), $dataName = '')
	{
		parent::__construct('Zend_Cache_Backend_Rediscluster', $data, $dataName);
	}

	public function setUp($notag = true)
	{
		$serverValid = array(
			'host'   => TESTS_ZEND_CACHE_REDISCLUSTER_HOST,
			'port'   => TESTS_ZEND_CACHE_REDISCLUSTER_PORT,
		);
		$options = array(
			'servers' => array($serverValid),
		);
		$this->_instance = new Zend_Cache_Backend_Rediscluster($options);
		parent::setUp($notag);
	}

	public function tearDown()
	{
		parent::tearDown();
		$this->_instance = null;
		// We have to wait after a memcached flush
//		sleep(1);
	}

	public function testConstructorCorrectCall()
	{
		$test = new Zend_Cache_Backend_Rediscluster();
	}

	function testBackendIsInstanceOfRedisCluster()
	{
		$this->assertInstanceOf('Zend_Cache_Backend_RedisCluster', $this->_instance);
	}

	function testSaveRedisCluster()
	{
		$data = 'foo';
		$data1 = 'foo1';
		$id = '125';
		$success = $this->_instance->save($data, $id);
		$this->assertTrue($success);
		$success = $this->_instance->save($data1, $id);
		$this->assertTrue($success);
		$loaded = $this->_instance->load($id);
		$this->assertEquals($data1, $loaded);
	}

	function testTestIDFromRedisCluster()
	{
		$data = 'foo';
		$id = '125';
		$success = $this->_instance->save($data, $id);
		$this->assertTrue($success);
		$loaded = $this->_instance->load($id);
		$this->assertEquals($data, $loaded);
		$loaded = $this->_instance->test($id);
		$this->assertTrue($loaded);
		$remove = $this->_instance->remove($id);
		$this->assertTrue($remove);
		$loaded = $this->_instance->test($id);
		$this->assertFalse($loaded);
	}

	function testLoadIDFromRedisCluster()
	{
		$data = 'foo';
		$id = '125';
		$success = $this->_instance->save($data, $id);
		$this->assertTrue($success);
		$remove = $this->_instance->remove($id);
		$this->assertTrue($remove);
		$loaded = $this->_instance->load($id);
		$this->assertFalse($loaded);
	}

	function testIncrementDecrementFromRedisCluster()
	{
		$id = '125';
		$id1 = '128';
		$id2 = '122';
		$success = $this->_instance->increment($id);
		$this->assertEquals(1, $success);
		$loaded = $this->_instance->load($id);
		$this->assertEquals(1, $loaded);
		$success = $this->_instance->increment($id);
		$this->assertEquals(2, $success);
		$success = $this->_instance->increment($id);
		$this->assertEquals(3, $success);
		$success = $this->_instance->increment($id, 2);
		$this->assertEquals(5, $success);
		$success = $this->_instance->increment($id, 2, 0);
		$this->assertEquals(7, $success);
		$success = $this->_instance->decrement($id, 2, 0);
		$this->assertEquals(5, $success);
		$success = $this->_instance->increment($id1, 2, 1);
		$this->assertEquals(2, $success);
		$success = $this->_instance->decrement($id2, 2, 0);
		$this->assertEquals(-2, $success);
	}

	function testClearByNamespace()
	{
		$data = 'foo';
		$id = '125_1';
		$id1 = '125_2';
		$this->_instance->save($data, $id);
		$this->_instance->save($data, $id1);
		$this->_instance->clearByNamespace('125_');
		$loaded = $this->_instance->load($id);
		$this->assertFalse($loaded);
		$loaded = $this->_instance->load($id1);
		$this->assertFalse($loaded);

		$keys = $this->_instance->clearByNamespace('');
		$this->assertFalse($keys);
	}

	function testGetKeysByNamespace()
	{
		$data = 'foo';
		$id = '125_1';
		$id1 = '125_2';
		$this->_instance->save($data, $id);
		$this->_instance->save($data, $id1);
		$keys = $this->_instance->getKeysByNamespace('125_');
		$expected_keys = [$id, $id1];
		$this->assertEquals(0, count(array_diff($expected_keys, $keys)));
		$this->assertEquals(0, count(array_diff($expected_keys, $keys)));

		$keys = $this->_instance->getKeysByNamespace('126_');
		$this->assertEquals([], $keys);

		$keys = $this->_instance->getKeysByNamespace('');
		$this->assertFalse($keys);
	}

	function testGetValuesByNamespace()
	{
		$data = 'foo';
		$id = '125_1';
		$id1 = '125_2';
		$this->_instance->save($data, $id);
		$this->_instance->save($data, $id1);
		$keys = $this->_instance->getValuesByNamespace('125_');
		$expected_keys = [$id => 'foo', $id1 => 'foo'];
		$this->assertEquals(0, count(array_diff($expected_keys, $keys)));
		$this->assertEquals(0, count(array_diff($expected_keys, $keys)));

		$keys = $this->_instance->getValuesByNamespace('126_');
		$this->assertEquals([], $keys);

		$keys = $this->_instance->getValuesByNamespace('');
		$this->assertFalse($keys);
	}

	function testRemoveTTL(){
		$data = 'foo';
		$id = '125';
		$success = $this->_instance->save($data, $id);
		$this->assertTrue($success);
		$success = $this->_instance->getMetadatas($id);
		$expected = [
			'expire' => 3600,
			'tags' => [],
			'mtime' => null
		];
		$this->assertEquals($expected, $success);

		$success = $this->_instance->removeTTL($id);
		$this->assertTrue($success);
		$success = $this->_instance->getMetadatas($id);
		$expected = [
			'expire' => -1,
			'tags' => [],
			'mtime' => null
		];
		$this->assertEquals($expected, $success);
	}

	function testSaveWithCustomTTL()
	{
		$data = 'foo';
		$id = '125';
		$customTTL = 300;
		$success = $this->_instance->save($data, $id, [], $customTTL);
		$this->assertTrue($success);
		$success = $this->_instance->getMetadatas($id);
		$expected = [
			'expire' => $customTTL,
			'tags' => [],
			'mtime' => null
		];
		$this->assertEquals($expected, $success);
	}

	function testGetCounterKey()
	{
		$id = '125';
		$success = $this->_instance->increment($id);
		$this->assertEquals(1, $success);
		$this->assertEquals(1, $this->_instance->getCounterKey($id));
	}

	function testGetCounterKeyReturnFalseWhenNotNumeric()
	{
		$id = '125';
		$data = 'foo';
		$success = $this->_instance->save($data, $id);
		$this->assertEquals(1, $success);
		$this->assertFalse($this->_instance->getCounterKey($id));
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
		$this->assertEquals($expected, $this->_instance->getCapabilities());
	}

	function testSaveMulti()
	{
		$id = '125';
		$data = 'foo';

		$id1 = '126';
		$data1 = 'foo1';

		$id2 = '127';
		$data2 = 'foo12';

		$toBeSaved = [
			$id => $data,
			$id1 => $data1,
			$id2 => $data2
		];

		$saveMultidata = $this->_instance->saveMulti($toBeSaved, []);
		$this->assertTrue($saveMultidata);
	}

	function testSaveMultiWithLifetime()
	{
		$ttl = 100;
		$id = '125';
		$data = 'foo';

		$id1 = '126';
		$data1 = 'foo1';

		$id2 = '127';
		$data2 = 'foo12';

		$toBeSaved = [
			$id => $data,
			$id1 => $data1,
			$id2 => $data2
		];

		$saveMultidata = $this->_instance->saveMulti($toBeSaved, [], $ttl);
		$this->assertTrue($saveMultidata);

		$success = $this->_instance->getMetadatas($id);
		$expected = [
			'expire' => $ttl,
			'tags' => [],
			'mtime' => null
		];
		$this->assertEquals($expected, $success);
	}

	function testLoadMulti()
	{
		$id = '125';
		$data = 'foo';
		$success = $this->_instance->save($data, $id);
		$this->assertTrue($success);

		$id1 = '126';
		$data1 = 'foo1';
		$success1 = $this->_instance->save($data1, $id1);
		$this->assertTrue($success1);

		$id2 = '127';
		$data2 = 'foo12';
		$success2 = $this->_instance->save($data2, $id2);
		$this->assertTrue($success2);

		$getKeys = [$id, $id1, $id2, 'foo'];
		$loadMultidata = $this->_instance->loadMulti($getKeys);

		$expected = [
			$id => $data,
			$id1 => $data1,
			$id2 => $data2,
			'foo' => false
		];
		$this->assertEquals($expected, $loadMultidata);
	}

	function testCleanRedisCluster()
	{
		$id = '125';
		$data = 'foo';
		$success = $this->_instance->save($data, $id);
		$this->assertTrue($success);
		$loaded = $this->_instance->load($id);
		$this->assertEquals($data, $loaded);

		$this->_instance->clean('all');
		$loaded = $this->_instance->load($id);
		$this->assertFalse($loaded);
	}

	function testCloseConnection()
	{
		$id = '125';
		$data = 'foo';
		$success = $this->_instance->save($data, $id);
		$this->assertTrue($success);
		$success = $this->_instance->closeConnection();
		$this->assertTrue($success);
		$loaded = $this->_instance->load($id);
		$this->assertEquals($data, $loaded);
	}

	function testGetIds()
	{
		$this->assertEquals([], $this->_instance->getIds());
	}

	function testGetTags()
	{
		$this->assertEquals([], $this->_instance->getTags());
	}

	function testGetIdsMatchingTags()
	{
		$this->assertEquals([], $this->_instance->getIdsMatchingTags());
	}

	function testGetIdsNotMatchingTags()
	{
		$this->assertEquals([], $this->_instance->getIdsNotMatchingTags());
	}

	function testGetIdsMatchingAnyTags()
	{
		$this->assertEquals([], $this->_instance->getIdsMatchingAnyTags());
	}

	public function testCleanModeOld()
	{
		$this->_instance->setDirectives(array('logging' => false));
		$this->_instance->clean('old');
		// do nothing, just to see if an error occured
		$this->_instance->setDirectives(array('logging' => true));
	}

	public function testCleanModeMatchingTags()
	{
		$this->_instance->setDirectives(array('logging' => false));
		$this->_instance->clean('matchingTag', array('tag1'));
		// do nothing, just to see if an error occured
		$this->_instance->setDirectives(array('logging' => true));
	}

	public function testCleanModeNotMatchingTags()
	{
		$this->_instance->setDirectives(array('logging' => false));
		$this->_instance->clean('notMatchingTag', array('tag1'));
		// do nothing, just to see if an error occured
		$this->_instance->setDirectives(array('logging' => true));
	}

	public function testGetMetadatas($notag = false)
	{
		$data = 'foo';
		$id = '125';
		$success = $this->_instance->save($data, $id);
		$this->assertTrue($success);
		$success = $this->_instance->getMetadatas($id);
		$expected = [
			'expire' => 3600,
			'tags' => [],
			'mtime' => null
		];
		$this->assertEquals($expected, $success);

		$id = '126';
		$success = $this->_instance->save($data, $id, [], 10);
		$this->assertTrue($success);
		$success = $this->_instance->getMetadatas($id);
		$expected = [
			'expire' => 10,
			'tags' => [],
			'mtime' => null
		];
		$this->assertEquals($expected, $success);

		$success = $this->_instance->getMetadatas(225);
		$this->assertFalse($success);
	}

	public function testTouch()
	{
		$res = $this->_instance->getMetadatas('bar');
		$bool = $this->_instance->touch('bar', 30);
		$this->assertTrue($bool);
		$res2 = $this->_instance->getMetadatas('bar');
		$this->assertTrue(($res2['expire'] - $res['expire']) == 30);
	}

	// Because of limitations of this backend...
	public function testCleanModeMatchingTags2() {}
	public function testCleanModeNotMatchingTags2() {}
	public function testCleanModeNotMatchingTags3() {}
	public function testGetFillingPercentage() {}
	public function testGetFillingPercentageOnEmptyBackend() {}
	public function testGetWithAnExpiredCacheId() {}
	public function testTestWithAnExistingCacheId() {}
	public function testTestWithAnExistingCacheIdAndANullLifeTime() {}
}