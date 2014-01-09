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
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_DeveloperGarden_SendSmsTest::main');
}

/**
 * @see Zend_Service_DeveloperGarden_SendSms
 */
require_once 'Zend/Service/DeveloperGarden/SendSms.php';

/**
 * Zend_Service_DeveloperGarden test case
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Service_DeveloperGarden_SendSmsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Service_DeveloperGarden_SendSms_Mock
     */
    protected $_service = null;

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED') ||
            TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED !== true) {
            $this->markTestSkipped('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED is not enabled');
        }

        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN', 'Unknown');
        }
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD', 'Unknown');
        }
        $config = array(
            'username' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN,
            'password' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD,
        );
        $this->service = new Zend_Service_DeveloperGarden_SendSms_Mock($config);
        // limit to mock env
        $this->assertTrue(
            $this->service->setEnvironment(Zend_Service_DeveloperGarden_SendSms_Mock::ENV_MOCK)
            instanceof
            Zend_Service_DeveloperGarden_SendSms_Mock
        );
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Response_Exception
     */
    public function testSmsMockInValidSender()
    {
        $sms = $this->service->createSms(
            '+49-171-2345678',
            'Zend Framework is very cool',
            'Zend.Framework'
        );

        $this->assertTrue(
            $sms instanceof Zend_Service_DeveloperGarden_Request_SendSms_SendSMS
        );

        $this->assertTrue(
            $this->service->send($sms) instanceof Zend_Service_DeveloperGarden_Response_SendSms_SendSMSResponse
        );
    }


    /**
     * @expectedException Zend_Service_DeveloperGarden_Response_Exception
     */
    public function testFlashSmsMockInValidSender()
    {
        $sms = $this->service->createFlashSms(
            '+49-171-2345678',
            'Zend Framework is very cool',
            'Zend.Framework'
        );

        $this->assertTrue(
            $sms instanceof Zend_Service_DeveloperGarden_Request_SendSms_SendFlashSMS
        );

        $this->assertTrue(
            $this->service->send($sms)
            instanceof
            Zend_Service_DeveloperGarden_Response_SendSms_SendFlashSMSResponse
        );
    }

    public function testSmsMockValid()
    {
        $sms = $this->service->createSms(
            '+49-171-2345678',
            'Zend Framework is very cool',
            'ZFTest'
        );

        $this->assertTrue(
            $sms instanceof Zend_Service_DeveloperGarden_Request_SendSms_SendSMS
        );

        $result = $this->service->send($sms);
        $this->assertTrue(
            $result instanceof Zend_Service_DeveloperGarden_Response_SendSms_SendSMSResponse
        );

        $this->assertTrue($result->isValid());
    }

    public function testFlashSmsMockValid()
    {
        $sms = $this->service->createFlashSms(
            '+49-171-2345678',
            'Zend Framework is very cool',
            'ZFTest'
        );

        $this->assertTrue(
            $sms instanceof Zend_Service_DeveloperGarden_Request_SendSms_SendFlashSMS
        );

        $result = $this->service->send($sms);
        $this->assertTrue(
            $result instanceof Zend_Service_DeveloperGarden_Response_SendSms_SendFlashSMSResponse
        );

        $this->assertTrue($result->isValid());
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testWrongSmsType()
    {
        $sms = new Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType(
            $this->service->getEnvironment()
        );
        $this->assertTrue(
            $sms->setNumber('+49-171-2345678')
            instanceof Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType
        );
        $this->assertTrue(
            $sms->setMessage('Zend Framework is very cool')
            instanceof Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType
        );
        $this->assertTrue(
            $sms->setOriginator('ZFTest')
            instanceof Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType
        );

        $this->assertTrue(
            $sms instanceof Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType
        );

        $this->assertNull($this->service->send($sms));
    }
}

class Zend_Service_DeveloperGarden_Request_SendSms_WrongSmsType
    extends Zend_Service_DeveloperGarden_Request_SendSms_SendSmsAbstract
{
    protected $_smsType = 999999;
}

class Zend_Service_DeveloperGarden_SendSms_Mock
    extends Zend_Service_DeveloperGarden_SendSms
{

}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_DeveloperGarden_SendSmsTest::main') {
    Zend_Service_DeveloperGarden_SendSmsTest::main();
}
