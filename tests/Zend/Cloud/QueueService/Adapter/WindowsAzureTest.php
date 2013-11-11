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
 * @package    Zend_Cloud_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

// Call Zend_Cloud_QueueService_Adapter_WindowsAzureTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Cloud_QueueService_Adapter_WindowsAzureTest::main");
}

/**
 * @see Zend_Cloud_QueueServiceTestCase
 */
require_once 'Zend/Cloud/QueueService/TestCase.php';

/**
 * @see Zend_Cloud_QueueeService_Adapter_WindowsAzure
 */
require_once 'Zend/Cloud/QueueService/Adapter/WindowsAzure.php';

/**
 * @category   Zend
 * @package    Zend_Cloud
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cloud_QueueService_Adapter_WindowsAzureTest extends Zend_Cloud_QueueService_TestCase
{
    /**
     * Period to wait for propagation in seconds
     * Should be set by adapter
     *
     * @var int
     */
    protected $_waitPeriod = 10;
	protected $_clientType = 'Zend_Service_WindowsAzure_Storage_Queue';

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
        $this->_wait();
    }

    protected function _getConfig()
    {
        if (!defined('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ENABLED') ||
            !constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ENABLED') ||
            !defined('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ACCOUNTNAME') ||
            !defined('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ACCOUNTKEY')) {
            $this->markTestSkipped("Windows Azure access not configured, skipping test");
        }

        $config = new Zend_Config(array(
            Zend_Cloud_QueueService_Factory::QUEUE_ADAPTER_KEY => 'Zend_Cloud_QueueService_Adapter_WindowsAzure',
            Zend_Cloud_QueueService_Adapter_WindowsAzure::ACCOUNT_NAME => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ACCOUNTNAME'),
            Zend_Cloud_QueueService_Adapter_WindowsAzure::ACCOUNT_KEY => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_ACCOUNTKEY'),
            Zend_Cloud_QueueService_Adapter_WindowsAzure::HOST => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_QUEUE_HOST'),
            Zend_Cloud_QueueService_Adapter_WindowsAzure::PROXY_HOST => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_STORAGE_PROXY_HOST'),
            Zend_Cloud_QueueService_Adapter_WindowsAzure::PROXY_PORT => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_STORAGE_PROXY_PORT'),
            Zend_Cloud_QueueService_Adapter_WindowsAzure::PROXY_CREDENTIALS => constant('TESTS_ZEND_SERVICE_WINDOWSAZURE_ONLINE_STORAGE_PROXY_CREDENTIALS'),
        ));

        return $config;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Cloud_QueueService_Adapter_WindowsAzureTest::main') {
    Zend_Cloud_QueueService_Adapter_WindowsAzureTest::main();
}
