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
 * @package    Zend_Cloud_StorageService
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

// Call Zend_Cloud_StorageService_Adapter_NirvanixTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Cloud_StorageService_Adapter_NirvanixTest::main");
}

/**
 * @see Zend_Cloud_StorageService_TestCase
 */
require_once 'Zend/Cloud/StorageService/TestCase.php';
/**
 * @see Zend_Cloud_StorageService_Adapter_Nirvanix
 */
require_once 'Zend/Cloud/StorageService/Adapter/Nirvanix.php';

/**
 * @category   Zend
 * @package    Zend_Cloud
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Cloud_StorageService_Adapter_NirvanixTest extends Zend_Cloud_StorageService_TestCase
{
	protected $_clientType = 'Zend_Service_Nirvanix';

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

    public function testFetchItemStream()
    {
        // The Nirvanix client library doesn't support streams
        $this->markTestSkipped('The Nirvanix client library doesn\'t support streams.');
    }

    public function testStoreItemStream()
    {
        // The Nirvanix client library doesn't support streams
        $this->markTestSkipped('The Nirvanix client library doesn\'t support streams.');
    }

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->_waitPeriod = 5;
    }

    protected function _getConfig()
    {
        if (!defined('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_ENABLED')
            || !constant('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_ENABLED')
            || !defined('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_USERNAME')
            || !defined('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_ACCESSKEY')
            || !defined('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_PASSWORD')
            || !defined('TESTS_ZEND_CLOUD_STORAGE_NIRVANIX_DIRECTORY')
        ) {
            $this->markTestSkipped("Windows Azure access not configured, skipping test");
        }

        $config = new Zend_Config(array(
            Zend_Cloud_StorageService_Factory::STORAGE_ADAPTER_KEY       => 'Zend_Cloud_StorageService_Adapter_Nirvanix',
            Zend_Cloud_StorageService_Adapter_Nirvanix::USERNAME         => constant('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_USERNAME'),
            Zend_Cloud_StorageService_Adapter_Nirvanix::APP_KEY          => constant('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_ACCESSKEY'),
            Zend_Cloud_StorageService_Adapter_Nirvanix::PASSWORD         => constant('TESTS_ZEND_SERVICE_NIRVANIX_ONLINE_PASSWORD'),
            Zend_Cloud_StorageService_Adapter_Nirvanix::REMOTE_DIRECTORY => constant('TESTS_ZEND_CLOUD_STORAGE_NIRVANIX_DIRECTORY'),
        ));

        return $config;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Cloud_StorageService_Adapter_NirvanixTest::main') {
    Zend_Cloud_StorageService_Adapter_NirvanixTest::main();
}
