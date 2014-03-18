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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';
require_once 'Zend/Cache/Backend/File.php';

/**
 * Zend_Log
 */
require_once 'Zend/Log.php';
require_once 'Zend/Log/Writer/Null.php';

/**
 * Common tests for backends
 */
require_once 'CommonExtendedBackendTest.php';

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class Zend_Cache_FileBackendTest extends Zend_Cache_CommonExtendedBackendTest {

    protected $_instance;
    protected $_instance2;
    protected $_cache_dir;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct('Zend_Cache_Backend_File', $data, $dataName);
    }

    public function setUp($notag = false)
    {
        $this->mkdir();
        $this->_cache_dir = $this->getTmpDir() . DIRECTORY_SEPARATOR;
        $this->_instance = new Zend_Cache_Backend_File(array(
            'cache_dir' => $this->_cache_dir,
        ));

        $logger = new Zend_Log(new Zend_Log_Writer_Null());
        $this->_instance->setDirectives(array('logger' => $logger));

        parent::setUp($notag);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->_instance);
    }

    public function testSetDeprecatedHashedDirectoryUmask()
    {
        try {
            $cache = new Zend_Cache_Backend_File(array(
                'cache_dir'              => $this->_cache_dir,
                'hashed_directory_umask' => 0700,
            ));
            $this->fail("Missing expected E_USER_NOTICE error");
        } catch (PHPUnit_Framework_Error $e) {
            if ($e->getCode() != E_USER_NOTICE) {
                throw $e;
            }

            $this->assertContains('hashed_directory_umask', $e->getMessage());
        }
    }

    public function testSetDeprecatedCacheFileUmask()
    {
        try {
            $cache = new Zend_Cache_Backend_File(array(
                    'cache_dir'        => $this->_cache_dir,
                    'cache_file_umask' => 0700,
            ));
            $this->fail("Missing expected E_USER_NOTICE error");
        } catch (PHPUnit_Framework_Error $e) {
            if ($e->getCode() != E_USER_NOTICE) {
                throw $e;
            }

            $this->assertContains('cache_file_umask', $e->getMessage());
        }
    }

    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Backend_File(array());
    }

    public function testConstructorWithABadFileNamePrefix()
    {
        try {
            $class = new Zend_Cache_Backend_File(array(
                'file_name_prefix' => 'foo bar'
            ));
        } catch (Zend_Cache_Exception $e) {
            return;
        }
        $this->fail('Zend_Cache_Exception was expected but not thrown');
    }

    public function testGetWithANonExistingCacheIdAndANullLifeTime()
    {
        $this->_instance->setDirectives(array('lifetime' => null));
        $this->assertFalse($this->_instance->load('barbar'));
    }

    public function testSaveCorrectCallWithHashedDirectoryStructure()
    {
        $this->_instance->setOption('hashed_directory_level', 2);
        $res = $this->_instance->save('data to cache', 'foo', array('tag1', 'tag2'));
        $this->assertTrue($res);
    }

    public function testCleanModeAllWithHashedDirectoryStructure()
    {
        $this->_instance->setOption('hashed_directory_level', 2);
        $this->assertTrue($this->_instance->clean('all'));
        $this->assertFalse($this->_instance->test('bar'));
        $this->assertFalse($this->_instance->test('bar2'));
    }

    public function testSaveWithABadCacheDir()
    {
        $this->_instance->setOption('cache_dir', '/foo/bar/lfjlqsdjfklsqd/');
        $res = $this->_instance->save('data to cache', 'foo', array('tag1', 'tag2'));
        $this->assertFalse($res);
    }

}
