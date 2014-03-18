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
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_DeveloperGarden_AllTests::main');
}

require_once 'Zend/Service/DeveloperGarden/SecurityTokenServerTest.php';
require_once 'Zend/Service/DeveloperGarden/BaseUserServiceTest.php';
require_once 'Zend/Service/DeveloperGarden/IpLocationTest.php';
require_once 'Zend/Service/DeveloperGarden/LocalSearchTest.php';
require_once 'Zend/Service/DeveloperGarden/SmsValidationTest.php';
require_once 'Zend/Service/DeveloperGarden/SendSmsTest.php';
require_once 'Zend/Service/DeveloperGarden/ConferenceCallTest.php';
require_once 'Zend/Service/DeveloperGarden/VoiceCallTest.php';

/**
 * Zend_Service_DeveloperGarden test suite
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Service_DeveloperGarden_AllOnlineTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service - DeveloperGarden - Online');

        $suite->addTestSuite('Zend_Service_DeveloperGarden_SecurityTokenServerTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_BaseUserServiceTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_IpLocationTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_LocalSearchTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_SendSmsTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_SmsValidationTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_VoiceCallTest');
        $suite->addTestSuite('Zend_Service_DeveloperGarden_ConferenceCallTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_DeveloperGarden_AllOnlineTests::main') {
    Zend_Service_DeveloperGarden_AllOnlineTests::main();
}
