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
 * @package    Zend_Http_UserAgent
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: JsonTest.php 12081 2008-10-22 19:07:55Z norm2782 $
 */

require_once 'Zend/Config.php';
require_once 'Zend/Http/UserAgent.php';
require_once 'Zend/Http/UserAgent/Mobile.php';
require_once 'Zend/Http/UserAgent/Storage/NonPersistent.php';

require_once dirname(__FILE__) . '/TestAsset/TestPluginLoader.php';
require_once dirname(__FILE__) . '/TestAsset/DesktopDevice.php';
require_once dirname(__FILE__) . '/TestAsset/InvalidDevice.php';
require_once dirname(__FILE__) . '/TestAsset/PopulatedStorage.php';

/**
 * @category   Zend
 * @package    Zend_Http_UserAgent
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Http
 * @group      Zend_Http_UserAgent
 */
class Zend_Http_UserAgentTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->server                         = array();
        $this->server['os']                   = 'Windows_NT';
        $this->server['http_accept']          = '*/*';
        $this->server['http_accept_language'] = 'fr-FR';
        $this->server['http_accept_encoding'] = 'gzip, deflate';
        $this->server['http_user_agent']      = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)';
        $this->server['http_host']            = 'zfmobile';
        $this->server['http_connection']      = 'Keep-Alive';
        $this->server['http_cookie']          = 'ZDEDebuggerPresent=php,phtml,php3';
        $this->server['server_signature']     = '';
        $this->server['server_software']      = 'Apache/2.2.12 (Win32) mod_ssl/2.2.12 OpenSSL/0.9.8k';
        $this->server['server_name']          = 'zfmobile';
        $this->server['server_addr']          = '127.0.0.1';
        $this->server['server_port']          = '80';
        $this->server['remote_addr']          = '127.0.0.1';
        $this->server['server_protocol']      = 'HTTP/1.1';

        $this->config                         = array(
            'server' => &$this->server,
            'storage'               => array(
                'adapter'           => 'NonPersistent',
            ),
            'wurflapi'              => array(
                'wurfl_lib_dir'     => constant('TESTS_ZEND_HTTP_USERAGENT_WURFL_LIB_DIR'),
                'wurfl_config_file' => constant('TESTS_ZEND_HTTP_USERAGENT_WURFL_CONFIG_FILE'),
            ),
        );
    }

    public function testMatchUserAgentSimple()
    {
        $config = $this->config;
        $config['server']['server_software'] = 'Apache/2';
        $userAgent = new Zend_Http_UserAgent($config);
        $device    = $userAgent->getDevice();

        $this->assertEquals('desktop', $userAgent->getBrowserType());
        $this->assertEquals('Internet Explorer', $device->getFeature('browser_name'));
        $this->assertEquals('7.0', $device->getFeature('browser_version'));
        $this->assertEquals('Internet Explorer', $device->getFeature('browser_compatibility'));
        $this->assertEquals('MSIE', $device->getFeature('browser_engine'));
        $this->assertEquals('Windows XP', $device->getFeature('device_os_name'));
        $this->assertEquals('Windows NT 5.1', $device->getFeature('device_os_token'));
        $this->assertEquals('apache', $device->getFeature('server_os'));
        $this->assertEquals('2', $device->getFeature('server_os_version'));
    }

    public function testMatchUserAgentServer()
    {
        $config = $this->config;
        $config['server']['os']              = 'Windows_NT';
        $config['server']['http_accept']     = '*/*';
        $config['server']['http_user_agent'] = 'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 5.1)';
        $config['server']['server_software'] = 'Apache/99';
        $config['user_agent']                = $config['server']["http_user_agent"];
        $userAgent = new Zend_Http_UserAgent($config);
        $device    = $userAgent->getDevice();

        $this->assertEquals('desktop', $userAgent->getBrowserType());
        $this->assertEquals('Internet Explorer', $device->getFeature('browser_name'));
        $this->assertEquals('9.0', $device->getFeature('browser_version'));
        $this->assertEquals('Internet Explorer', $device->getFeature('browser_compatibility'));
        $this->assertEquals('MSIE', $device->getFeature('browser_engine'));
        $this->assertEquals('Windows XP', $device->getFeature('device_os_name'));
        $this->assertEquals('Windows NT 5.1', $device->getFeature('device_os_token'));
        $this->assertEquals('apache', $device->getFeature('server_os'));
        $this->assertEquals('99', $device->getFeature('server_os_version'));
    }

    public function testUserAgentDefineIdentificationSequence()
    {
        if (!constant('TESTS_ZEND_HTTP_USERAGENT_WURFL_LIB_DIR')) {
            $this->markTestSkipped('Depends on WURFL support');
        }
        $config = $this->config;
        $config['user_agent'] = 'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleW1ebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/4A102 Safari/419.3';

        $userAgent = new Zend_Http_UserAgent($config);
        $device    = $userAgent->getDevice();
        $this->assertType('Zend_Http_UserAgent_Mobile', $device);
        $this->assertEquals('mobile', $userAgent->getBrowserType());
        $this->assertEquals('Safari', $userAgent->getDevice()->getFeature('mobile_browser'));
        $this->assertEquals('iPhone OS', $userAgent->getDevice()->getFeature('device_os'));
        $this->assertEquals('true', $userAgent->getDevice()->getFeature('has_qwerty_keyboard'));
        $this->assertEquals('touchscreen', $userAgent->getDevice()->getFeature('pointing_method'));
        $this->assertEquals('false', $userAgent->getDevice()->getFeature('is_tablet'));
        $this->assertEquals('iPhone', $userAgent->getDevice()->getFeature('model_name'));
        $this->assertEquals('Apple', $userAgent->getDevice()->getFeature('brand_name'));
    }

    public function testUserAgentDefineStorage()
    {
        $config = array(
            'storage' => array('adapter' => 'NonPersistent'),
            'server'  => $this->server,
        );
        $oUserAgent      = new Zend_Http_UserAgent($config);
        $browser         = $oUserAgent->getUserAgent();
        $this->assertType('Zend_Http_UserAgent_Storage_NonPersistent', $oUserAgent->getStorage($browser));
    }

    public function testUserAgentFeatureAdapter()
    {
        $config = $this->config;
        $config['mobile']['features']['path']      = dirname(__FILE__) . '/TestAsset/Device/Browser/Features/Adapter.php';
        $config['mobile']['features']['classname'] = 'Device_Browser_Features_Adapter';
        $config['user_agent'] = 'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleW1ebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/4A102 Safari/419.3';

        $userAgent = new Zend_Http_UserAgent($config);

        $config = $userAgent->getConfig();
        $this->assertContains('Device/Browser/Features/Adapter.php', $config['mobile']['features']['path']);
    }

    public function testSetDefaultConfigAlone()
    {
        $config['server'] = $this->server;
        $userAgent = new Zend_Http_UserAgent($config);
        $config = $userAgent->getConfig();
        $this->assertEquals(Zend_Http_UserAgent::DEFAULT_IDENTIFICATION_SEQUENCE, $config['identification_sequence']);
        $this->assertEquals(Zend_Http_UserAgent::DEFAULT_PERSISTENT_STORAGE_ADAPTER, $config['storage']['adapter']);
    }

    public function testSetDefaultConfigStorage()
    {
        $config     = array('identification_sequence' => 'Test');
        $oUserAgent = new Zend_Http_UserAgent($config);

        $test = $oUserAgent->getConfig();
        $this->assertEquals('Test', $test['identification_sequence']);
        $this->assertEquals(Zend_Http_UserAgent::DEFAULT_PERSISTENT_STORAGE_ADAPTER, $test['storage']['adapter']);
    }

    public function testSetDefaultConfigBoth()
    {
        $config = array(
            'identification_sequence'    => 'Test',
            'storage' => array('adapter' => 'NonPersistent'),
        );
        $oUserAgent = new Zend_Http_UserAgent($config);
        $test       = $oUserAgent->getConfig();
        $this->assertEquals('Test', $test['identification_sequence']);
        $this->assertEquals('NonPersistent', $test['storage']['adapter']);
    }

    public function testDeviceClassNameMatchesBrowserTypeIfUserAgentMatches()
    {
        if (!constant('TESTS_ZEND_HTTP_USERAGENT_WURFL_LIB_DIR')) {
            $this->markTestSkipped('Depends on WURFL support');
        }
        $this->config['browser_type'] = 'MoBiLe';
        $this->config['user_agent']   = 'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleW1ebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/4A102 Safari/419.3';
        $userAgent = new Zend_Http_UserAgent($this->config);
        $className = get_class($userAgent->getDevice());
        $this->assertEquals('Zend_Http_UserAgent_Mobile', $className);
    }

    public function testDeviceClassNameMatchesDesktopTypeIfUserAgentDoesNotMatch()
    {
        $config = array(
            'browser_type' => 'MoBiLe',
        );
        $userAgent = new Zend_Http_UserAgent($config);
        $className = get_class($userAgent->getDevice());
        $this->assertEquals('Zend_Http_UserAgent_Desktop', $className);
    }

    public function testUserAgentFromServerSuperglobalWhenNotProvided()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'UserAgentTest2';
        $ua = new Zend_Http_UserAgent();
        $this->assertEquals('UserAgentTest2', $ua->getServerValue('http_user_agent'));
        $this->assertEquals('UserAgentTest2', $ua->getUserAgent());
    }

    public function testAllowsPassingUserAgentDirectly()
    {
        $this->config['user_agent'] = 'UserAgentTest2';
        $ua = new Zend_Http_UserAgent($this->config);
        $this->assertEquals('UserAgentTest2', $ua->getUserAgent());
    }

    public function testAllowsSettingUserAgentManually()
    {
        $ua = new Zend_Http_UserAgent();
        $ua->setUserAgent('userAgentTest');
        $this->assertEquals('userAgentTest', $ua->getServerValue('HTTP_USER_AGENT'));
        $this->assertEquals('userAgentTest', $ua->getUserAgent());
    }

    public function testUsesHttpAcceptConstantValueByDefault()
    {
        unset($this->server['http_accept']);
        $ua = new Zend_Http_UserAgent();
        $this->assertEquals(Zend_Http_UserAgent::DEFAULT_HTTP_ACCEPT, $ua->getHttpAccept());
        $this->assertEquals(Zend_Http_UserAgent::DEFAULT_HTTP_ACCEPT, $ua->getServerValue('HTTP_ACCEPT'));
    }

    public function testUsesServerHttpAcceptValueWhenPresent()
    {
        $_SERVER['HTTP_ACCEPT'] = 'HttpAcceptTest2';
        $ua = new Zend_Http_UserAgent();
        $this->assertEquals('HttpAcceptTest2', $ua->getHttpAccept());
        $this->assertEquals('HttpAcceptTest2', $ua->getServerValue('HTTP_ACCEPT'));
    }

    public function testAllowsPassingHttpAcceptValueViaConfiguration()
    {
        $this->config['http_accept'] = 'HttpAcceptTest';
        $ua = new Zend_Http_UserAgent($this->config);
        $this->assertEquals('HttpAcceptTest', $ua->getHttpAccept());
        $this->assertEquals('HttpAcceptTest', $ua->getServerValue('HTTP_ACCEPT'));
    }

    public function testAllowsSettingHttpAcceptManually()
    {
        $ua = new Zend_Http_UserAgent();
        $ua->setHttpAccept('httpAcceptTest');
        $this->assertEquals('httpAcceptTest', $ua->getHttpAccept());
        $this->assertEquals('httpAcceptTest', $ua->getServerValue('HTTP_ACCEPT'));
    }

    public function testCanSetConfigWithConfigObject()
    {
        $config = new Zend_Config($this->config);
        $ua     = new Zend_Http_UserAgent($config);
        $test   = $ua->getConfig();
        $this->assertEquals($config->storage->adapter, $test['storage']['adapter']);
    }

    public function testCanSetConfigWithTraversableObject()
    {
        $config = new ArrayObject($this->config);
        $ua     = new Zend_Http_UserAgent($config);
        $test   = $ua->getConfig();
        $this->assertEquals($config['storage'], $test['storage']);
    }

    public function invalidConfigs()
    {
        return array(
            array(true),
            array(1),
            array(1.0),
            array(new stdClass),
        );
    }

    /**
     * @dataProvider invalidConfigs
     */
    public function testSettingConfigWithInvalidTypeRaisesException($arg)
    {
        $this->setExpectedException('Zend_Http_UserAgent_Exception', 'expected array');
        $ua = new Zend_Http_UserAgent($arg);
    }

    public function testAllowsSettingServerWithArrayObject()
    {
        $server = new ArrayObject($this->server);
        $ua = new Zend_Http_UserAgent(array('server' => $server));
        $this->assertEquals($server['os'], $ua->getServerValue('os'));
    }

    public function testAllowsSettingServerWithTraversableObject()
    {
        $server = new ArrayIterator($this->server);
        $ua = new Zend_Http_UserAgent(array('server' => $server));
        $this->assertEquals($this->server['os'], $ua->getServerValue('os'));
    }

    /**
     * @dataProvider invalidConfigs
     */
    public function testSettingServerWithInvalidTypeRaisesException($arg)
    {
        $this->setExpectedException('Zend_Http_UserAgent_Exception', 'array or object implementing Traversable');
        $ua = new Zend_Http_UserAgent(array('server' => $arg));
    }

    public function testAllowsSettingPluginLoaderUsingClassname()
    {
        $ua = new Zend_Http_UserAgent();
        $ua->setPluginLoader('device', 'Zend_Http_TestAsset_TestPluginLoader');
        $loader = $ua->getPluginLoader('device');
        $this->assertType('Zend_Http_TestAsset_TestPluginLoader', $loader);
    }

    public function testSpecifyingInvalidPluginLoaderClassNameRaisesException()
    {
        $ua = new Zend_Http_UserAgent();
        $this->setExpectedException('Zend_Http_UserAgent_Exception', 'extending Zend_Loader_PluginLoader');
        $ua->setPluginLoader('device', 'Zend_Http_TestAsset_InvalidPluginLoader');
    }

    public function invalidLoaders()
    {
        return array(
            array(true),
            array(1),
            array(1.0),
            array(array()),
        );
    }

    /**
     * @dataProvider invalidLoaders
     */
    public function testSpecifyingInvalidTypeToPluginLoaderRaisesException($arg)
    {
        $ua = new Zend_Http_UserAgent();
        $this->setExpectedException('Zend_Http_UserAgent_Exception', 'class or object');
        $ua->setPluginLoader('device', $arg);
    }

    public function testSpecifyingNonPluginLoaderObjectRaisesException()
    {
        $ua = new Zend_Http_UserAgent();
        $this->setExpectedException('Zend_Http_UserAgent_Exception', 'extending Zend_Loader_PluginLoader');
        $ua->setPluginLoader('device', $this);
    }

    public function testSpecifyingInvalidTypeWhenSettingPluginLoaderRaisesException()
    {
        $ua = new Zend_Http_UserAgent();
        $this->setExpectedException('Zend_Http_UserAgent_Exception', 'plugin loader type');
        $ua->setPluginLoader('__bogus__', new Zend_Loader_PluginLoader());
    }

    public function testAllowsSpecifyingPluginLoadersViaConfiguration()
    {
        $this->config['plugin_loader'] = array(
            'device'  => 'Zend_Http_TestAsset_TestPluginLoader',
            'storage' => 'Zend_Http_TestAsset_TestPluginLoader',
        );
        $ua = new Zend_Http_UserAgent($this->config);
        $deviceLoader = $ua->getPluginLoader('device');
        $this->assertType('Zend_Http_TestAsset_TestPluginLoader', $deviceLoader);
        $storageLoader = $ua->getPluginLoader('storage');
        $this->assertType('Zend_Http_TestAsset_TestPluginLoader', $storageLoader);
        $this->assertNotSame($deviceLoader, $storageLoader);
    }

    public function testAllowsSpecifyingCustomDeviceClassesViaConfiguration()
    {
        $this->config['desktop'] = array(
            'device' => array(
                'classname' => 'Zend_Http_TestAsset_DesktopDevice',
            ),
        );
        $this->config['user_agent'] = 'desktop';
        $ua     = new Zend_Http_UserAgent($this->config);
        $device = $ua->getDevice();
        $this->assertType('Zend_Http_TestAsset_DesktopDevice', $device);
    }

    public function testAllowsSpecifyingCustomDeviceViaPrefixPath()
    {
        $this->config['desktop'] = array(
            'device' => array(
                'path'   => dirname(__FILE__) . '/TestAsset/Device',
                'prefix' => 'Zend_Http_TestAsset_Device',
            ),
        );
        $this->config['user_agent'] = 'desktop';
        $ua     = new Zend_Http_UserAgent($this->config);
        $device = $ua->getDevice();
        $this->assertType('Zend_Http_TestAsset_Device_Desktop', $device);
    }

    public function testShouldRaiseExceptionOnInvalidDeviceClass()
    {
        $this->config['desktop'] = array(
            'device' => array(
                'classname' => 'Zend_Http_TestAsset_InvalidDevice',
            ),
        );
        $this->config['user_agent'] = 'desktop';

        $ua     = new Zend_Http_UserAgent($this->config);
        $this->setExpectedException('Zend_Http_UserAgent_Exception', 'Zend_Http_UserAgent_Device');
        $ua->getDevice();
    }

    public function testStorageContainsSerializedUserAgent()
    {
        $this->config['desktop'] = array(
            'device' => array(
                'classname' => 'Zend_Http_TestAsset_DesktopDevice',
            ),
        );
        $this->config['user_agent'] = 'desktop';
        $ua         = new Zend_Http_UserAgent($this->config);

        // prime storage by retrieving device
        $device     = $ua->getDevice();
        $storage    = $ua->getStorage();
        $serialized = $storage->read();

        $test       = unserialize($serialized);
        $this->assertEquals($ua->getBrowserType(), $test['browser_type']);
        $this->assertEquals($ua->getConfig(), $test['config']);
        $this->assertEquals('Zend_Http_TestAsset_DesktopDevice', $test['device_class']);
        $this->assertEquals($ua->getUserAgent(), $test['user_agent']);
        $this->assertEquals($ua->getHttpAccept(), $test['http_accept']);

        $test   = unserialize($test['device']);
        $this->assertEquals($device->getAllFeatures(), $test['_aFeatures']);
        $this->assertEquals($device->getBrowser(), $test['_browser']);
        $this->assertEquals($device->getBrowserVersion(), $test['_browserVersion']);
        $this->assertEquals($device->getUserAgent(), $test['_userAgent']);
        $this->assertEquals($device->getImages(), $test['_images']);
    }

    public function testCanPopulateFromStorage()
    {
        $this->config['storage']['adapter'] = 'Zend_Http_TestAsset_PopulatedStorage';
        $this->config['user_agent'] = 'desktop';
        $ua         = new Zend_Http_UserAgent($this->config);
        $storage    = $ua->getStorage();
        $this->assertType('Zend_Http_TestAsset_PopulatedStorage', $storage);
        $device = $ua->getDevice();
        $this->assertType('Zend_Http_TestAsset_DesktopDevice', $device);
    }

    public function testCanClearStorage()
    {
        $this->config['desktop'] = array(
            'device' => array(
                'classname' => 'Zend_Http_TestAsset_DesktopDevice',
            ),
        );
        $this->config['user_agent'] = 'desktop';
        $ua         = new Zend_Http_UserAgent($this->config);

        // Prime storage by retrieving device
        $device     = $ua->getDevice();
        $storage    = $ua->getStorage();
        $this->assertType('Zend_Http_UserAgent_Storage', $storage);
        $this->assertFalse($storage->isEmpty());
        $ua->clearStorage();
        $this->assertTrue($storage->isEmpty());
    }

    public function testServerIsImmutableOnceDeviceRetrieved()
    {
        $config = $this->config;
        $userAgent = new Zend_Http_UserAgent($config);
        $device    = $userAgent->getDevice();

        $this->setExpectedException('Zend_Http_UserAgent_Exception', 'immutable');
        $userAgent->setServerValue('HTTP_ACCEPT', 'application/json');
    }

    public function testBrowserTypeIsImmutableOnceDeviceRetrieved()
    {
        $config = $this->config;
        $userAgent = new Zend_Http_UserAgent($config);
        $device    = $userAgent->getDevice();

        $this->setExpectedException('Zend_Http_UserAgent_Exception', 'immutable');
        $userAgent->setBrowserType('mobile');
    }

    public function testHttpAcceptIsImmutableOnceDeviceRetrieved()
    {
        $config = $this->config;
        $userAgent = new Zend_Http_UserAgent($config);
        $device    = $userAgent->getDevice();

        $this->setExpectedException('Zend_Http_UserAgent_Exception', 'immutable');
        $userAgent->setHttpAccept('application/json');
    }

    public function testUserAgentIsImmutableOnceDeviceIsRetrieved()
    {
        $config = $this->config;
        $userAgent = new Zend_Http_UserAgent($config);
        $device    = $userAgent->getDevice();

        $this->setExpectedException('Zend_Http_UserAgent_Exception', 'immutable');
        $userAgent->setUserAgent('userAgentTest');
    }

    public function testStorageIsImmutableOnceDeviceIsRetrieved()
    {
        $config = $this->config;
        $userAgent = new Zend_Http_UserAgent($config);
        $device    = $userAgent->getDevice();

        $this->setExpectedException('Zend_Http_UserAgent_Exception', 'immutable');
        $userAgent->setStorage(new Zend_Http_UserAgent_Storage_NonPersistent());
    }

    public function testAllowsPassingStorageConfigurationOptions()
    {
        $config = $this->config;
        $config['storage']['adapter'] = 'Session';
        $config['storage']['options'] = array(
            'browser_type' => 'foobar',
            'member'       => 'data',
        );
        $userAgent = new Zend_Http_UserAgent($config);
        $storage   = $userAgent->getStorage();
        $this->assertEquals('.foobar', $storage->getNamespace());
        $this->assertEquals('data', $storage->getMember());
    }

    /**
     * @group ZF-10595
     */
    public function testAGroupDefinedAndSerialized()
    {
        $config    = $this->config;
        $userAgent = new Zend_Http_UserAgent($config);
        $device    = $userAgent->getDevice();

        $userAgent = unserialize(serialize($userAgent));
        $device    = $userAgent->getDevice();
        $groups = $device->getAllGroups();
    }

    /**
     * @group ZF-10665
     */
    public function testDontDieOnSerialization()
    {
        $config    = $this->config;
        $userAgent = new Zend_Http_UserAgent($config);

        // If this code doesn't throw a fatal error the test passed.
        $userAgent->setUserAgent('userAgentTest');
        $userAgent->serialize();
    }
}
