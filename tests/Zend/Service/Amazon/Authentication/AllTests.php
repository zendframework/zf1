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
 * @package    Zend_Service_Amazon_Authentication
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 11973 2008-10-15 16:00:56Z matthew $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_Amazon_Authentication_AllTests::main');
}

/**
 * @see Zend_Service_Amazon_SimpleDb_OfflineTest
 */
require_once 'Zend/Service/Amazon/SimpleDb/OfflineTest.php';

/**
 * @see Zend_Service_Amazon_SimpleDb_OnlineTest
 */
require_once 'Zend/Service/Amazon/SimpleDb/OnlineTest.php';

/**
 * @category   Zend
 * @package    Zend_Service_Amazon_SimpleDb
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_Authentication_AllTests
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Creates and returns this test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service - Amazon - Authentication');

        if (defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')
            && constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')
            && defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEY')
            && defined('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY')
        ) {
            $suite->addTestSuite('Zend_Service_Amazon_Authentication_S3Test');
            $suite->addTestSuite('Zend_Service_Amazon_Authentication_V1Test');
            $suite->addTestSuite('Zend_Service_Amazon_Authentication_V2Test');
        }

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_Amazon_Authentication_AllTests::main') {
    Zend_Service_Amazon_Authentication_AllTests::main();
}
