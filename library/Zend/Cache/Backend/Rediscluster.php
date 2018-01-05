<?php

/**
 * Copyright (c) 2011-2013, Carl Oscar Aaro
 * All rights reserved.
 *
 * New BSD License
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  * Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 *  * Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 *  * Neither the name of Carl Oscar Aaro nor the names of its
 *    contributors may be used to endorse or promote products derived from this
 *    software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 **/

/**
 * Redis cache backend for Zend Framework. Extends Zend_Cache_Backend
 * Supports tags and cleaning modes (except CLEANING_MODE_NOT_MATCHING_TAG)
 * Uses the PHP module phpredis by Nicolas Favre-Felix available at https://github.com/nicolasff/phpredis
 *
 * @category Zend
 * @author Carl Oscar Aaro <carloscar@agigen.se>
 */

/**
 * @see Zend_Cache_Backend_Interface
 */
require_once 'Zend/Cache/Backend/ExtendedInterface.php';

/**
 * @see Zend_Cache_Backend
 */
require_once 'Zend/Cache/Backend.php';

class Zend_Cache_Backend_Rediscluster extends Zend_Cache_Backend implements Zend_Cache_Backend_ExtendedInterface
{
	/**
	 * Default Values
	 */
	const DEFAULT_HOST = '127.0.0.1';
	const DEFAULT_PORT = 30001;
	const DEFAULT_TIMEOUT = 1.5;
	const DEFAULT_READ_TIMEOUT = 2.0;
	const DEFAULT_FAILOVER = 'distribute';
	const OPT_SLAVE_FAILOVER = 5;
	const REDIS_FAILOVER_DISTRIBUTE = 2;
	const REDIS_FAILOVER_ERROR = 1;
	const COMPRESS_PREFIX = ":\x1f\x8b";

	/**
	 * Log message
	 */
	const METHOD_UNSUPPORTED_BY_REDISCLUSTER_BACKEND = 'method unsupported by Zend_Cache_Backend_RedisCluster';
	const TAGS_UNSUPPORTED_BY_REDISCLUSTER_BACKEND = 'tags unsupported by Zend_Cache_Backend_RedisCluster';

	/**
	 * options
	 */
	protected $_options = [
		'servers' => [
			[
				'host' => self::DEFAULT_HOST,
				'port' => self::DEFAULT_PORT
			],
		],
		'timeout' => self::DEFAULT_TIMEOUT,
		'read_timeout' => self::DEFAULT_READ_TIMEOUT,
		'key_prefix' => '',
		'write_control' => false,
	];

	/**
	 * @var int
	 */
	protected $_compressThreshold = 20480;

	/**
	 * @var string
	 */
	protected $_compressionLib;

	/**
	 * Redis object
	 *
	 * @var mixed redis object
	 */
	protected $_redis = null;

	/**
	 * @var int
	 */
	protected $_compressData = 3;

	/**
	 * Constructor
	 *
	 * @param array $options associative array of options
	 */
	public function __construct(array $options = [])
	{
		if (!extension_loaded('redis')) {
			Zend_Cache::throwException('The redis extension must be loaded for using this backend !');
		}
		parent::__construct($options);
		$serverHosts = [];
		foreach ($this->_options['servers'] as $server) {
			$serverHosts[] = $server['host'] . ':' . $server['port'];
		}
		$serverHosts[] = $this->_options['timeout'];
		$serverHosts[] = $this->_options['read_timeout'];
		$this->_redis = new RedisCluster(null, $serverHosts);
		$this->_redis->setOption(self::OPT_SLAVE_FAILOVER, self::REDIS_FAILOVER_DISTRIBUTE);

		if (isset($options['compression_lib'])) {
			$this->_compressionLib = $options['compression_lib'];
		} else if (function_exists('snappy_compress')) {
			$this->_compressionLib = 'snappy';
		} else {
			$this->_compressionLib = 'gzip';
		}

		$this->_compressPrefix = substr($this->_compressionLib, 0, 2) . self::COMPRESS_PREFIX;
	}

	/**
	 * Test if a cache is available for the given id and (if yes) return it (false else)
	 *
	 * @param string $id cache id
	 * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
	 * @return string|false cached datas
	 */
	public function load($id, $doNotTestCacheValidity = true)
	{
		if (!$this->_redis) {
			return false;
		}
		$data = $this->_redis->get($id);
		if ($data != false) {
			return $this->_decodeData($data);
		}
		return false;
	}

	/**
	 * Test if a cache is available or not (for the given id)
	 *
	 * @param string $id cache id
	 * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
	 */
	public function test($id)
	{
		if (!$this->_redis) {
			return false;
		}
		$tmp = $this->_redis->exists($id);
		return $tmp;
	}

	/**
	 * Save some string data into a cache record
	 *
	 * Note : $data is always "string" (serialization is done by the
	 * core not by the backend)
	 *
	 * @param  string $data Data to cache
	 * @param  string $id Cache id
	 * @param  mixed $tags Array of strings, the cache record will be tagged by each string entry, if false, key
	 *                                  can only be read if $doNotTestCacheValidity is true
	 * @param  int|bool $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
	 * @return boolean true if no problem
	 */
	public function save($data, $id, $tags = [], $specificLifetime = false)
	{
		if (!$this->_redis) {
			return false;
		}

		$compressedData = $this->_encodeData($data, $this->_compressData);

		$lifetime = $this->getLifetime($specificLifetime);
		if ($lifetime === null) {
			$return = $this->_redis->set($id, $compressedData);
		} else {
			$return = $this->_redis->setex($id, $lifetime, $compressedData);
		}
		if ($return === false) {
			$rsCode = $this->_redis->getLastError();
			$this->_log("RedisCluster::set() failed: [{$rsCode}]");
		}
		if (count($tags) > 0) {
			$this->_log(self::METHOD_UNSUPPORTED_BY_REDISCLUSTER_BACKEND);
		}

		return $return;
	}

	/**
	 * Remove a cache record
	 *
	 * @param  string $id Cache id
	 * @return boolean True if no problem
	 */
	public function remove($id)
	{
		return (boolean)$this->_redis->del($id);
	}

	/**
	 * Clean some cache records
	 *
	 * Available modes are :
	 * 'all' (default)  => remove all cache entries ($tags is not used)
	 * 'old'            => unsupported
	 * 'matchingTag'    => unsupported
	 * 'notMatchingTag' => unsupported
	 * 'matchingAnyTag' => unsupported
	 *
	 * @param  string $mode Clean mode
	 * @param  array $tags Array of tags
	 * @throws Zend_Cache_Exception
	 * @return boolean True if no problem
	 */
	public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = [])
	{
		$success_times = true;
		$masters = $this->_redis->_masters();
		if ($mode == Zend_Cache::CLEANING_MODE_ALL) {
			foreach ($masters as $master) {
				$server = implode(',', $master);
				$success = $this->_redis->flushDB($server);
				!$success ?: $success_times++;
			}
			return $success_times == count($masters);
		} else {
			$this->_log(self::METHOD_UNSUPPORTED_BY_REDISCLUSTER_BACKEND);
		}
		return false;
	}

	/**
	 * Give (if possible) an extra lifetime to the given cache id
	 *
	 * @param string $id cache id
	 * @param int $extraLifetime
	 * @return boolean true if ok
	 */
	public function touch($id, $extraLifetime)
	{
		$ttl = $this->_redis->ttl($id);
		return $this->_redis->expire($id, $ttl + $extraLifetime);
	}

	/**
	 * Return an array of metadatas for the given cache id
	 *
	 * The array must include these keys :
	 * - expire : the expire timestamp
	 * - tags : a string array of tags
	 * - mtime : timestamp of last modification time
	 *
	 * @param string $id cache id
	 * @return array array of metadatas (false if the cache id is not found)
	 */
	public function getMetadatas($id)
	{
		$tmp = $this->_redis->get($id);
		if ($tmp === false) {
			return $tmp;
		}
		$ttl = $this->_redis->ttl($id);
		return [
			'expire' => $ttl,
			'tags' => [],
			'mtime' => null
		];
	}

	/**
	 * Increment cache id value
	 *
	 * @param string $id cache id
	 * @param int $offset
	 * @param int $initial
	 * @return boolean $specificLifetime or integer in seconds lifetime
	 */
	public function increment($id, $offset = 1, $initial = 0, $specificLifetime = false)
	{
		$result = $this->_redis->incrBy($id, $offset);
		return $result;
	}

	/**
	 * Decrement cache id value
	 *
	 * @param string $id cache id
	 * @param int $offset
	 * @param int $initial
	 * @return boolean $specificLifetime or integer in seconds lifetime
	 */
	public function decrement($id, $offset = 1, $initial = 0, $specificLifetime = false)
	{
		$result = $this->_redis->decrBy($id, $offset);
		return $result;
	}

	/**
	 * Remove items of given namespace
	 *
	 * @param string $namespace
	 * @return bool
	 */
	public function clearByNamespace($namespace)
	{
		$namespace = (string)$namespace;
		if ($namespace === '') {
			return false;
		}
		$this->_redis->del($this->_redis->keys($namespace . '*'));
		return true;
	}

	/**
	 * Get keys of given namespace
	 *
	 * @param string $namespace
	 * @return bool
	 */
	public function getKeysByNamespace($namespace)
	{
		$namespace = (string)$namespace;
		if ($namespace === '') {
			return false;
		}
		$keys = $this->_redis->keys($namespace . '*');
		return $keys;
	}

	/**
	 * Get Values of given namespace
	 *
	 * @param string $namespace
	 * @return mixed
	 */
	public function getValuesByNamespace($namespace)
	{
		$namespace = (string)$namespace;
		if ($namespace === '') {
			return false;
		}
		$keys = $this->_redis->keys($namespace . '*');
		$values = [];
		foreach ($keys as $key) {
			$values[$key] = $this->load($key);
		}
		return $values;
	}

	/**
	 * Removes TTL from a key
	 *
	 * @param string $id
	 * @return bool
	 */
	public function removeTTL($id)
	{
		return (boolean)$this->_redis->persist($id);
	}

	/**
	 * Return the filling percentage of the backend storage
	 *
	 * @throws Zend_Cache_Exception
	 * @return int integer between 0 and 100
	 */
	public function getFillingPercentage()
	{
		$this->_log(self::METHOD_UNSUPPORTED_BY_REDISCLUSTER_BACKEND);
		return [];
	}

	/**
	 * Return an array of stored cache ids
	 *
	 * @return array array of stored cache ids (string)
	 */
	public function getIds()
	{
		$this->_log(self::METHOD_UNSUPPORTED_BY_REDISCLUSTER_BACKEND);
		return [];
	}

	/**
	 * Return an array of stored tags
	 *
	 * @return array array of stored tags (string)
	 */
	public function getTags()
	{
		$this->_log(self::METHOD_UNSUPPORTED_BY_REDISCLUSTER_BACKEND);
		return [];
	}

	/**
	 * Return an array of stored cache ids which match given tags
	 *
	 * In case of multiple tags, a logical AND is made between tags
	 *
	 * @param array $tags array of tags
	 * @return array array of matching cache ids (string)
	 */
	public function getIdsMatchingTags($tags = [])
	{
		$this->_log(self::METHOD_UNSUPPORTED_BY_REDISCLUSTER_BACKEND);
		return [];
	}

	/**
	 * Return an array of stored cache ids which don't match given tags
	 *
	 * In case of multiple tags, a logical OR is made between tags
	 *
	 * @param array $tags array of tags
	 * @return array array of not matching cache ids (string)
	 */
	public function getIdsNotMatchingTags($tags = [])
	{
		$this->_log(self::METHOD_UNSUPPORTED_BY_REDISCLUSTER_BACKEND);
		return [];
	}

	/**
	 * Return an array of stored cache ids which match any given tags
	 *
	 * In case of multiple tags, a logical AND is made between tags
	 *
	 * @param array $tags array of tags
	 * @return array array of any matching cache ids (string)
	 */
	public function getIdsMatchingAnyTags($tags = array())
	{
		$this->_log(self::METHOD_UNSUPPORTED_BY_REDISCLUSTER_BACKEND);
		return [];
	}

	/**
	 * Return the value if is numeric or false if not
	 *
	 *
	 * @param string $id
	 * @return integer or boolean
	 */
	public function getCounterKey($id)
	{
		$tmp = $this->load($id);
		return is_numeric($tmp) ? $tmp : false;
	}

	/**
	 * Return the values of the given arrays
	 *
	 * @param array $ids
	 * @return array
	 */
	public function loadMulti($ids = [])
	{
		$data = [];
		$result = $this->_redis->mGet($ids);
		foreach ($ids as $key => $value) {
			$data[$value] = $result[$key];
		}
		return $data;
	}

	/**
	 * Save the given keys with values. If they have lifetime it saves
	 *
	 * @param array $data
	 * @param array $tags
	 * @param boolean $specificLifetime
	 * @return boolean
	 */
	public function saveMulti($data, $tags = [], $specificLifetime = false)
	{
		$result = false;
		if (is_array($data)) {
			$lifetime = $this->getLifetime($specificLifetime);

			foreach ($data as $key => $value) {
				$result = $this->save($value, $key, [], $lifetime);
				if ($result === false) {
					$rsCode = $this->_redis->getLastError();
					$this->_log("RedisCluster::set() failed: [{$rsCode}]");
				}
			}
			if (count($tags) > 0) {
				$this->_log(self::METHOD_UNSUPPORTED_BY_REDISCLUSTER_BACKEND);
			}
		}
		return $result;
	}

	/**
	 * Close connection with redis
	 *
	 */
	public function closeConnection()
	{
		$tmp = $this->_redis->close();
		return $tmp;
	}

	/**
	 * Return an associative array of capabilities (booleans) of the backend
	 *
	 * The array must include these keys :
	 * - automatic_cleaning (is automating cleaning necessary)
	 * - tags (are tags supported)
	 * - expired_read (is it possible to read expired cache records
	 *                 (for doNotTestCacheValidity option for example))
	 * - priority does the backend deal with priority when saving
	 * - infinite_lifetime (is infinite lifetime can work with this backend)
	 * - get_list (is it possible to get the list of cache ids and the complete list of tags)
	 *
	 * @return array associative of with capabilities
	 */
	public function getCapabilities()
	{
		return [
			'automatic_cleaning' => false,
			'tags' => false,
			'expired_read' => false,
			'priority' => false,
			'infinite_lifetime' => false,
			'get_list' => false
		];
	}

	/**
	 * @param string $data
	 * @param int $level
	 * @throws Exception
	 * @return string
	 */
	protected function _encodeData($data, $level)
	{
		if ($level && strlen($data) >= $this->_compressThreshold) {
			switch ($this->_compressionLib) {
				case 'snappy':
					$data = snappy_compress($data);
					break;
				case 'lzf':
					$data = lzf_compress($data);
					break;
				case 'gzip':
					$data = gzcompress($data, $level);
					break;
			}
			if (!$data) {
				throw new Exception("Could not compress cache data.");
			}
			return $this->_compressPrefix . $data;
		}
		return $data;
	}

	/**
	 * @param bool|string $data
	 * @return string
	 */
	protected function _decodeData($data)
	{
		if (substr($data, 2, 3) == self::COMPRESS_PREFIX) {
			switch (substr($data, 0, 2)) {
				case 'sn':
					return snappy_uncompress(substr($data, 5));
				case 'lz':
					return lzf_decompress(substr($data, 5));
				case 'gz':
				case 'zc':
					return gzuncompress(substr($data, 5));
			}
		}
		return $data;
	}
}
