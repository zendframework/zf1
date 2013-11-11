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
 * @package    Zend_Cloud_QueueService
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

// Call Zend_Cloud_QueueService_Adapter_SqsTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Cloud_QueueService_Adapter_SqsTest::main");
}

/**
 * @see Zend_Cloud_QueueServiceTestCase
 */
require_once 'Zend/Cloud/QueueService/TestCase.php';

/**
 * @see Zend_Cloud_QueueeService_Adapter_Sqs
 */
require_once 'Zend/Cloud/QueueService/Adapter/Sqs.php';

/**
 * @category   Zend
 * @package    Zend_Cloud
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cloud_QueueService_Adapter_SqsTest extends Zend_Cloud_QueueService_TestCase
{
    /**
     * Period to wait for propagation in seconds
     * Should be set by adapter
     *
     * @var int
     */
    protected $_waitPeriod = 10;
	protected $_clientType = 'Zend_Service_Amazon_Sqs';

	/**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        // Isolate the tests from slow deletes
        $this->_wait();
    }

    public function testListQueues()
    {
        try {
            $queues = $this->_commonQueue->listQueues();
            $this->_wait();
            if (count($queues)) {
                foreach ($queues as $queue) {
                    $this->_commonQueue->deleteQueue($queue);
                    $this->_wait();
                }
            }

            $queueURL1 = $this->_commonQueue->createQueue('test-list-queue1');
            $this->assertNotNull($queueURL1);
            $this->_wait();

            $queueURL2 = $this->_commonQueue->createQueue('test-list-queue2');
            $this->assertNotNull($queueURL2);

            // Wait 30s after creation to ensure we can retrieve it
            $this->_wait(30);

            $queues = $this->_commonQueue->listQueues();
            $errorMessage = "Final queues are ";
            foreach ($queues as $queue) {
                $errorMessage .= $queue . ', ';
            }
            $errorMessage .= "\nHave queue URLs $queueURL1 and $queueURL2\n";
            $this->assertEquals(2, count($queues), $errorMessage);

            // PHPUnit does an identical comparison for assertContains(), so we just
            // use assertTrue and in_array()
            $this->assertTrue(in_array($queueURL1, $queues));
            $this->assertTrue(in_array($queueURL2, $queues));

            $this->_commonQueue->deleteQueue($queueURL1);
            $this->_commonQueue->deleteQueue($queueURL2);
        } catch (Exception $e) {
            if (isset($queueURL1)) {
                $this->_commonQueue->deleteQueue($queueURL1);
            }
            if (isset($queueURL2)) {
                $this->_commonQueue->deleteQueue($queueURL2);
            }
            throw $e;
        }
    }

    public function testStoreQueueMetadata() {
        $this->markTestSkipped('SQS does not currently support storing metadata');
    }

    protected function _getConfig()
    {
        if (!defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')
            || !constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')
            || !defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID')
            || !defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY')
        ) {
            $this->markTestSkipped("Amazon SQS access not configured, skipping test");
        }

        $config = new Zend_Config(array(
            Zend_Cloud_QueueService_Factory::QUEUE_ADAPTER_KEY => 'Zend_Cloud_QueueService_Adapter_Sqs',
            Zend_Cloud_QueueService_Adapter_Sqs::AWS_ACCESS_KEY => constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'),
            Zend_Cloud_QueueService_Adapter_Sqs::AWS_SECRET_KEY => constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY'),
            ));

        return $config;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Cloud_QueueService_Adapter_SqsTest::main') {
    Zend_Cloud_QueueService_Adapter_SqsTest::main();
}
