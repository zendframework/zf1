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
require_once 'Zend/Cache/Backend/WinCache.php';

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
class Zend_Cache_WinCacheBackendTest extends Zend_Cache_CommonExtendedBackendTest {

    protected $_instance;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct('Zend_Cache_Backend_WinCache', $data, $dataName);
    }

    public function setUp($notag = true)
    {
        $this->_instance = new Zend_Cache_Backend_WinCache(array());
        parent::setUp($notag);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->_instance);
    }

    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Backend_WinCache();
    }

    public function testCleanModeOld() {
        $this->_instance->setDirectives(array('logging' => false));
        $this->_instance->clean('old');
        // do nothing, just to see if an error occured
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testCleanModeMatchingTags() {
        $this->_instance->setDirectives(array('logging' => false));
        $this->_instance->clean('matchingTag', array('tag1'));
        // do nothing, just to see if an error occured
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testCleanModeNotMatchingTags() {
        $this->_instance->setDirectives(array('logging' => false));
        $this->_instance->clean('notMatchingTag', array('tag1'));
        // do nothing, just to see if an error occured
        $this->_instance->setDirectives(array('logging' => true));
    }

    // Because of limitations of this backend...
    public function testGetWithAnExpiredCacheId() {
    	$this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }
    
    public function testCleanModeMatchingTags2() {
    	$this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }
    
    public function testCleanModeNotMatchingTags2() {
    	$this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }
    
    public function testCleanModeNotMatchingTags3() {
    	$this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }
    
    public function testGetIdsMatchingTags() {
    	$this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }
    
    public function testGetIdsMatchingTags2() {
    	$this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }
    
    public function testGetIdsMatchingTags3() {
    	$this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }
    
    public function testGetIdsMatchingTags4() {
    	$this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }
    
    public function testGetIdsNotMatchingTags() {
    	$this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }
    
    public function testGetIdsNotMatchingTags2() {
    	$this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }
    
    public function testGetIdsNotMatchingTags3() {
    	$this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }
    
    public function testGetTags() {
    	$this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }

    public function testSaveCorrectCall()
    {
        $this->_instance->setDirectives(array('logging' => false));
        parent::testSaveCorrectCall();
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testSaveWithNullLifeTime()
    {
        $this->_instance->setDirectives(array('logging' => false));
        parent::testSaveWithNullLifeTime();
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testSaveWithSpecificLifeTime()
    {

        $this->_instance->setDirectives(array('logging' => false));
        parent::testSaveWithSpecificLifeTime();
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testGetMetadatas($notag = true)
    {
        parent::testGetMetadatas($notag);
    }

}


