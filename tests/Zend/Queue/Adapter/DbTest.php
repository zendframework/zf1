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
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/*
 * The adapter test class provides a universal test class for all of the
 * abstract methods.
 *
 * All methods marked not supported are explictly checked for for throwing
 * an exception.
 */

/** Zend_Queue */
require_once 'Zend/Queue.php';

/** Zend_Queue */
require_once 'Zend/Queue/Message.php';

/** Zend_Queue_Message_Test */
require_once 'MessageTestClass.php';

/** Base Adapter test class */
require_once dirname(__FILE__) . '/AdapterTest.php';

/**
 * @see Zend_Db_Select
 */
require_once 'Zend/Db/Select.php';

/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Queue
 */
class Zend_Queue_Adapter_DbTest extends Zend_Queue_Adapter_AdapterTest
{
    /**
     * Test setup
     */
    public function setUp()
    {
        if (!TESTS_ZEND_QUEUE_DB) {
            $this->markTestSkipped('TESTS_ZEND_QUEUE_DB is not enabled in TestConfiguration.php');
        }
        date_default_timezone_set('GMT');
        parent::setUp();
    }

    /**
     * getAdapterName() is an method to help make AdapterTest work with any
     * new adapters
     *
     * You must overload this method
     *
     * @return string
     */
    public function getAdapterName()
    {
        return 'Db';
    }

    /**
     * getAdapterName() is an method to help make AdapterTest work with any
     * new adapters
     *
     * You may overload this method.  The default return is
     * 'Zend_Queue_Adapter_' . $this->getAdapterName()
     *
     * @return string
     */
    public function getAdapterFullName()
    {
        return 'Zend_Queue_Adapter_' . $this->getAdapterName();
    }

    public function getTestConfig()
    {
        $driverOptions = array();
        if (defined('TESTS_ZEND_QUEUE_DB')) {
            require_once 'Zend/Json.php';
            $driverOptions = Zend_Json::decode(TESTS_ZEND_QUEUE_DB);
        }

        return array(
            'options'       => array(Zend_Db_Select::FOR_UPDATE => true),
            'driverOptions' => $driverOptions,
        );
    }

    // test the constants
    public function testConst()
    {
        $this->markTestSkipped('no constants to test');
    }

    // additional non-standard tests

    public function test_constructor2()
    {
        try {
            $config = $this->getTestConfig();
            /**
             * @see Zend_Db_Select
             */
            require_once 'Zend/Db/Select.php';
            $config['options'][Zend_Db_Select::FOR_UPDATE] = array();
            $queue = $this->createQueue(__FUNCTION__, $config);
            $this->fail('FOR_UPDATE accepted an array');
        } catch (Exception $e) {
            $this->assertTrue(true, 'FOR_UPDATE cannot be an array');
        }

        foreach (array('host', 'username', 'password', 'dbname') as $i => $arg) {
            try {
                $config = $this->getTestConfig();
                unset($config['driverOptions'][$arg]);
                $queue = $this->createQueue(__FUNCTION__, $config);
                $this->fail("$arg is required but was missing.");
            } catch (Exception $e) {
                $this->assertTrue(true, $arg . ' is required.');
            }
        }
    }
    
    /**
     * @group ZF-7650
     */
    public function testReceiveWillRetrieveZeroItems()
    {
        $options = $this->getTestConfig();
        $options['name'] = '/temp-queue/ZF7650';

        $queue = new Zend_Queue('Db', $options);
        $queue2 = $queue->createQueue('queue');

        $queue->send('My Test Message 1');
        $queue->send('My Test Message 2');

        $messages = $queue->receive(0);
        $this->assertEquals(0, count($messages));
    }
}

