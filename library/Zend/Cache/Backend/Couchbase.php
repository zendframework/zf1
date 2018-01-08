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
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Backend
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Cache_Backend_Interface
 */
require_once 'Zend/Cache/Backend/ExtendedInterface.php';

/**
 * @see Zend_Cache_Backend
 */
require_once 'Zend/Cache/Backend.php';


/**
 * @package    Zend_Cache
 * @subpackage Zend_Cache_Backend
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cache_Backend_Couchbase extends Zend_Cache_Backend implements Zend_Cache_Backend_ExtendedInterface
{
	/**
	 * Default Values
	 */
	const DEFAULT_HOST = '127.0.0.1';
	const DEFAULT_BUCKET = 'tp24cache';
	const DEFAULT_PORT =  11211;
	const DEFAULT_PERSISTENT = true;
	const DEFAULT_WEIGHT  = 1;
	const DEFAULT_TIMEOUT = 1;
	const DEFAULT_RETRY_INTERVAL = 15;
	const DEFAULT_STATUS = true;
	const DEFAULT_FAILURE_CALLBACK = null;

	/**
	 * Log message
	 */
	const METHOD_UNSUPPORTED_BY_COUCHBASE_BACKEND = 'method unsupported by Zend_Cache_Backend_Couchbase';
	const TAGS_UNSUPPORTED_BY = 'tags unsupported by Zend_Cache_Backend_Couchbase';

	protected $_options = array(
		'servers' => array(array(
			'host' => self::DEFAULT_HOST,
			'persistent' => self::DEFAULT_PERSISTENT,
			'weight'  => self::DEFAULT_WEIGHT,
			'timeout' => self::DEFAULT_TIMEOUT,
			'retry_interval' => self::DEFAULT_RETRY_INTERVAL,
			'status' => self::DEFAULT_STATUS,
			'failure_callback' => self::DEFAULT_FAILURE_CALLBACK
		)),
		'port' => self::DEFAULT_PORT,
		'bucket' => self::DEFAULT_BUCKET,
		'compression' => false,
		'compatibility' => false,
	);

	/**
	 * Couchbase object
	 *
	 * @var mixed couchbase object
	 */
	protected $_couchbaseClient = null;
	protected $_couchbaseBucket = null;

	/**
	 * Constructor
	 *
	 * @param array $options associative array of options
	 * @throws Zend_Cache_Exception
	 * @return void
	 */
	public function __construct(array $options = [])
	{
		if (!extension_loaded('couchbase')) {
			Zend_Cache::throwException('The couchbase extension must be loaded for using this backend !');
		}
		parent::__construct($options);

		$serverHosts = [];
		foreach ($this->_options['servers'] as $server) {
			$serverHosts[] = $server['host'];
		}
		$connectionString = sprintf('couchbase://%s/', implode(',', $serverHosts));
		$this->_couchbaseClient = new CouchbaseCluster($connectionString);

		$this->_couchbaseBucket = $this->_couchbaseClient->openBucket($this->_options['bucket']);
	}

	/**
	 * Test if a cache is available for the given id and (if yes) return it (false else)
	 *
	 * @param  string  $id                     Cache id
	 * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
	 * @return string|false cached datas
	 */

	public function load($id, $doNotTestCacheValidity = false)
	{
		if (!$this->_couchbaseBucket) {
			return false;
		}
		try {
			$result = $this->_couchbaseBucket->get($id);
			if ($result && $result->error == null) {
				return $result->value;
			}
		} catch (Exception $exc) {
			return false;
		}
		return false;
	}

	/**
	 * Test if a cache is available or not (for the given id)
	 *
	 * @param  string $id Cache id
	 * @return mixed|false (a cache is not available)
	 */
	public function test($id)
	{
		if (!$this->_couchbaseBucket) {
			return false;
		}
		$result = $this->load($id);
		if ($result) {
			return $result;
		}
		return false;
	}

	/**
	 * Save some string datas into a cache record
	 *
	 * Note : $data is always "string" (serialization is done by the
	 * core not by the backend)
	 *
	 * @param  string $data             Datas to cache
	 * @param  string $id               Cache id
	 * @param  array  $tags             Array of strings, the cache record will be tagged by each string entry
	 * @param  int    $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
	 * @return boolean True if no problem
	 */
	public function save($data, $id, $tags = [], $specificLifetime = false)
	{
		if (!$this->_couchbaseBucket) {
			return false;
		}

		$lifetime = $this->getLifetime($specificLifetime);
		if ($lifetime == null) {
			$lifetime = $this->getLifetime(false);
		}
		$result = @$this->_couchbaseBucket->upsert($id, $data, ['expiry' => $lifetime]);

		if (count($tags) > 0) {
			$this->_log(self::TAGS_UNSUPPORTED_BY);
		}

		if ($result && $result->error != null) {
			return false;
		}
		return true;
	}


	/**
	 * Remove a cache record
	 *
	 * @param  string $id Cache id
	 * @return boolean True if no problem
	 */
	public function remove($id)
	{
		$result = $this->_couchbaseBucket->remove($id);
		if ($result && $result->error == null) {
			if ($result->value != null) {
				return false;
			}
		}
		return true;
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
	 * @param  array  $tags Array of tags
	 * @throws Zend_Cache_Exception
	 * @return boolean True if no problem
	 */
	public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = [])
	{
		if (!$this->_couchbaseBucket || $mode != Zend_Cache::CLEANING_MODE_ALL) {
			return false;
		}
		$manager = $this->_couchbaseBucket->manager();
		return (boolean)$manager->flush();
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
		$result = $this->_couchbaseBucket->touch($id,time() + $extraLifetime);
		if ($result && $result != null) {
			return true;
		}
		return false;
	}

	/**
	 * Incremental functionality
	 *
	 * @param $id
	 * @param int $offset
	 * @param int $initial
	 * @param bool $specificLifetime
	 * @return bool|mixed
	 */
	public function increment($id, $offset = 1, $initial = 0, $specificLifetime = false)
	{
		$key = $this->load($id);
		if (!$key) {
			$lifetime = $this->getLifetime($specificLifetime);
			$this->_couchbaseBucket->insert($id, $initial, ['expiry' => $lifetime]);
		}

		$result = $this->_couchbaseBucket->counter($id, $offset);
		if (!$result) {
			$lifetime = $this->getLifetime($specificLifetime);
			$this->_couchbaseBucket->insert($id, $initial, ['expiry' => $lifetime]);
			$result = $this->_couchbaseBucket->counter($id, $offset);
		}
		if ($result && $result != null) {
			return $result->value;
		}
		return false;
	}

	/**
	 * Return true if the automatic cleaning is available for the backend
	 *
	 * @return boolean
	 */
	public function isAutomaticCleaningAvailable()
	{
		return false;
	}

	/**
	 * Return an array of stored cache ids
	 *
	 * @return array array of stored cache ids (string)
	 */
	public function getIds()
	{
		$this->_log("getting the list of cache ids is unsupported by the Couchbase backend");
		return [];
	}

	/**
	 * Return an array of stored tags
	 *
	 * @return array array of stored tags (string)
	 */
	public function getTags()
	{
		$this->_log(self::METHOD_UNSUPPORTED_BY_COUCHBASE_BACKEND);
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
		$this->_log(self::METHOD_UNSUPPORTED_BY_COUCHBASE_BACKEND);
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
		$this->_log(self::METHOD_UNSUPPORTED_BY_COUCHBASE_BACKEND);
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
	public function getIdsMatchingAnyTags($tags = [])
	{
		$this->_log(self::METHOD_UNSUPPORTED_BY_COUCHBASE_BACKEND);
		return [];
	}

	/**
	 * Return the filling percentage of the backend storage
	 *
	 * @throws Zend_Cache_Exception
	 * @return int integer between 0 and 100
	 */
	public function getFillingPercentage()
	{
		$this->_log(self::METHOD_UNSUPPORTED_BY_COUCHBASE_BACKEND);
		return [];
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
		$this->_log(self::METHOD_UNSUPPORTED_BY_COUCHBASE_BACKEND);
		return [];
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
		return array(
			'automatic_cleaning' => false,
			'tags' => false,
			'expired_read' => false,
			'priority' => false,
			'infinite_lifetime' => false,
			'get_list' => false
		);
	}

}
