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
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Application_Resource_CacheManagerTest::main');
}

/**
 * Zend_Loader_Autoloader
 */
require_once 'Zend/Loader/Autoloader.php';

/**
 * Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * Zend_Application_Resource_Cachemanager
 */
require_once 'Zend/Application/Resource/Cachemanager.php';

/**
 * Zend_Cache_Backend
 */
require_once 'Zend/Cache/Backend.php';

/**
 * Zend_Cache_Core
 */
require_once 'Zend/Cache/Core.php';

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class Zend_Application_Resource_CacheManagerTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        Zend_Loader_Autoloader::resetInstance();
        $this->autoloader = Zend_Loader_Autoloader::getInstance();

        $this->application = new Zend_Application('testing');

        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
        $this->bootstrap = new ZfAppBootstrap($this->application);
    }

    public function tearDown()
    {
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        foreach ($loaders as $loader) {
            spl_autoload_unregister($loader);
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        Zend_Controller_Front::getInstance()->resetInstance();

        // Reset autoloader instance so it doesn't affect other tests
        Zend_Loader_Autoloader::resetInstance();
    }

    public function testInitializationCreatesCacheManagerInstance()
    {

        $resource = new Zend_Application_Resource_Cachemanager(array());
        $resource->init();
        $this->assertTrue($resource->getCachemanager() instanceof Zend_Cache_Manager);
    }

    public function testShouldReturnCacheManagerWhenComplete()
    {
        $resource = new Zend_Application_Resource_Cachemanager(array());
        $manager = $resource->init();
        $this->assertTrue($manager instanceof Zend_Cache_Manager);
    }

    public function testShouldMergeConfigsIfOptionsPassedForDefaultCacheTemplate()
    {
        $options = array(
            'page' => array(
                'backend' => array(
                    'options' => array(
                        'cache_dir' => '/foo'
                    )
                )
            )
        );
        $resource = new Zend_Application_Resource_Cachemanager($options);
        $manager = $resource->init();
        $cacheTemplate = $manager->getCacheTemplate('page');
        $this->assertEquals('/foo', $cacheTemplate['backend']['options']['cache_dir']);

    }

    public function testShouldCreateNewCacheTemplateIfConfigNotMatchesADefaultTemplate()
    {
        $options = array(
            'foo' => array(
                'backend' => array(
                    'options' => array(
                        'cache_dir' => '/foo'
                    )
                )
            )
        );
        $resource = new Zend_Application_Resource_Cachemanager($options);
        $manager = $resource->init();
        $cacheTemplate = $manager->getCacheTemplate('foo');
        $this->assertSame($options['foo'], $cacheTemplate);
    }

    public function testShouldNotMeddleWithFrontendOrBackendCapitalisation()
    {
        $options = array(
            'foo' => array(
                'backend' => array(
                    'name' => 'BlackHole'
                )
            )
        );
        $resource = new Zend_Application_Resource_Cachemanager($options);
        $manager = $resource->init();
        $cacheTemplate = $manager->getCacheTemplate('foo');
        $this->assertEquals('BlackHole', $cacheTemplate['backend']['name']);
    }

    public function testEmptyBackendOptionsShouldNotResultInError()
    {
        $options = array(
            'foo' => array(
                'frontend' => array(
                    'name' => 'Core',
                    'options' => array(
                        'lifetime' => 7200,
                    ),
                ),
                'backend' => array(
                    'name' => 'black.hole',
                ),
            ),
        );
        $resource = new Zend_Application_Resource_Cachemanager($options);
        $manager = $resource->init();
        $cache = $manager->getCache('foo');
        $this->assertTrue($cache instanceof Zend_Cache_Core);
    }

    /**
     * @group ZF-9738
     */
    public function testZendServer()
    {
        if (!function_exists('zend_disk_cache_store')) {
            $this->markTestSkipped('ZendServer is required for this test');
        }

        $options = array(
            'foo' => array(
                'frontend' => array(
                    'name' => 'Core',
                    'options' => array(
                        'lifetime' => 7200,
                    ),
                ),
                'backend' => array(
                    'name' => 'ZendServer_Disk',
                ),
            ),
        );
        $resource = new Zend_Application_Resource_Cachemanager($options);
        $manager = $resource->init();
        $cache = $manager->getCache('foo')->getBackend();
        $this->assertTrue($cache instanceof Zend_Cache_Backend_ZendServer_Disk);
    }

    /**
     * @group ZF-9737
     */
    public function testCustomFrontendBackendNaming()
    {
        $options = array(
            'zf9737' => array(
                'frontend' => array(
                    'name'                 => 'custom-naming',
                    'customFrontendNaming' => false),
                'backend' => array('name'                    => 'Zend_Cache_Backend_Custom_Naming',
                                   'customBackendNaming'     => true),
                'frontendBackendAutoload' => true)
        );

        $resource = new Zend_Application_Resource_Cachemanager($options);
        $manager  = $resource->init();
        $cache    = $manager->getCache('zf9737');
        $this->assertTrue($cache->getBackend() instanceof Zend_Cache_Backend_Custom_Naming);
        $this->assertTrue($cache instanceof Zend_Cache_Frontend_CustomNaming);
    }

    /**
     * @group GH-103
     */
    public function testLoggerFactory()
    {
        $options = array(
            'page' => array(
                'frontend' => array(
                    'options' => array(
                        'logging' => true,
                        'logger'  => array(
                            new Zend_Log_Writer_Mock()
                        )
                    )
                )
            )
        );

        $resource = new Zend_Application_Resource_Cachemanager($options);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();

        $page = $resource->getCacheManager()->getCache('page');
        $page->getBackend()->clean(Zend_Cache::CLEANING_MODE_OLD);

        $event = current($options['page']['frontend']['options']['logger'][0]->events);

        $this->assertTrue(is_array($event));
        $this->assertTrue(array_key_exists('message', $event));
        $this->assertContains('Zend_Cache_Backend_Static', $event['message']);
    }
}


class Zend_Cache_Backend_Custom_Naming extends Zend_Cache_Backend
{
}

class Zend_Cache_Frontend_CustomNaming extends Zend_Cache_Core
{
}

if (PHPUnit_MAIN_METHOD == 'Zend_Application_Resource_CacheManagerTest::main') {
    Zend_Application_Resource_CacheManagerTest::main();
}
