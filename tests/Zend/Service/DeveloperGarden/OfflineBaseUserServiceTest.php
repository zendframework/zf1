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
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_DeveloperGarden_BaseUserServiceTest::main');
}

/**
 * @see Zend_Service_DeveloperGarden_BaseUserService
 */
require_once 'Zend/Service/DeveloperGarden/BaseUserService.php';

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
class Zend_Service_DeveloperGarden_OfflineBaseUserServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @todo add more tests for the ClientAbstract
     */

    /**
     * @var Zend_Service_DeveloperGarden_OfflineBaseUserService_Mock
     */
    protected $_service = null;

    protected $_limit = 10;

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
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
        $this->service = new Zend_Service_DeveloperGarden_OfflineBaseUserService_Mock($config);
    }

    public function testGetModuleIds()
    {
        $ids = $this->service->getModuleIds();
        $this->assertTrue(is_array($ids));
        $this->assertNotNull($ids);
        $this->assertGreaterThan(0, count($ids));
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testBuildModuleStringThrowsException()
    {
        $this->service->getBuildModuleString('foo', 'bar');
    }

    public function testBuildModuleStringSandbox()
    {
        $modules = array('Sms', 'localsearch', 'IPLocation', 'CCS', 'VoiceButler');
        foreach ($modules as $v) {
            $ret = $this->service->getBuildModuleString(
                $v,
                Zend_Service_DeveloperGarden_OfflineBaseUserService_Mock::ENV_SANDBOX
            );
            $this->assertNotNull($ret);
        }
    }

    public function testBuildModuleStringProduction()
    {
        $modules = array('Sms', 'localsearch', 'IPLocation', 'CCS', 'VoiceButler');
        foreach ($modules as $v) {
            $ret = $this->service->getBuildModuleString(
                $v,
                Zend_Service_DeveloperGarden_OfflineBaseUserService_Mock::ENV_PRODUCTION
            );
            $this->assertNotNull($ret);
        }
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testBuildModuleStringMockMustFail()
    {
        $modules = array('Sms', 'localsearch', 'IPLocation', 'CCS', 'VoiceButler');
        foreach ($modules as $v) {
            $ret = $this->service->getBuildModuleString(
                $v,
                Zend_Service_DeveloperGarden_OfflineBaseUserService_Mock::ENV_MOCK
            );
            $this->assertNotNull($ret);
        }
    }

    public function testModuleIds()
    {
        $moduleIds = $this->service->getModuleIds();
        $this->assertTrue(is_array($moduleIds));
        $this->assertEquals(10, count($moduleIds));
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testModuleIdsException()
    {
        $this->service->checkModuleId('WRONG');
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testBuildModuleStringException()
    {
        $this->service->buildModuleString(
            'WrongModule',
            Zend_Service_DeveloperGarden_OfflineBaseUserService_Mock::ENV_SANDBOX
        );
    }

    public function testGetCredentialOnSoapObject()
    {
        if (!extension_loaded('soap')) {
            $this->markTestSkipped('SOAP extension is not loaded');
        }

        $this->assertTrue(
            $this->service->getSoapClient()->getCredential() instanceof Zend_Service_DeveloperGarden_Credential
        );
    }

    public function testGetTokenServiceOnSoapObject()
    {
        if (!extension_loaded('soap')) {
            $this->markTestSkipped('SOAP extension is not loaded');
        }

        $this->assertTrue(
            $this->service->getSoapClient()->getTokenService() instanceof Zend_Service_DeveloperGarden_SecurityTokenServer
        );
    }
}

class Zend_Service_DeveloperGarden_OfflineBaseUserService_Mock
    extends Zend_Service_DeveloperGarden_BaseUserService
{
    /**
     * returns the correct module string
     *
     * @param string $module
     * @param integer $environment
     * @return string
     */
    public function getBuildModuleString($module, $environment)
    {
        return $this->_buildModuleString($module, $environment);
    }

    public function checkModuleId($moduleId)
    {
        return $this->_checkModuleId($moduleId);
    }

    public function buildModuleString($module, $environment)
    {
        return $this->_buildModuleString($module, $environment);
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_DeveloperGarden_OfflineBaseUserServiceTest::main') {
    Zend_Service_DeveloperGarden_OfflineBaseUserServiceTest::main();
}
