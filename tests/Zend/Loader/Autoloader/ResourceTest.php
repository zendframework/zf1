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
 * @package    Zend_Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Loader_Autoloader_ResourceTest::main');
}

/**
 * @see Zend_Loader_Autoloader
 */
require_once 'Zend/Loader/Autoloader.php';

/**
 * @see Zend_Loader_Autoloader_Resource
 */
require_once 'Zend/Loader/Autoloader/Resource.php';

/**
 * @see Zend_Loader_Autoloader_Interface
 */
require_once 'Zend/Loader/Autoloader/Interface.php';

/** Zend_Config */
require_once 'Zend/Config.php';

/**
 * @category   Zend
 * @package    Zend_Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Loader
 */
class Zend_Loader_Autoloader_ResourceTest extends PHPUnit_Framework_TestCase
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

        // Store original include_path
        $this->includePath = get_include_path();

        Zend_Loader_Autoloader::resetInstance();
        $this->autoloader = Zend_Loader_Autoloader::getInstance();

        // initialize 'error' member for tests that utilize error handling
        $this->error = null;

        $this->loader = new Zend_Loader_Autoloader_Resource(array(
            'namespace' => 'FooBar',
            'basePath'  => realpath(dirname(__FILE__) . '/_files'),
        ));
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

        // Retore original include_path
        set_include_path($this->includePath);

        // Reset autoloader instance so it doesn't affect other tests
        Zend_Loader_Autoloader::resetInstance();
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testAutoloaderInstantiationShouldRaiseExceptionWithoutNamespace()
    {
        $loader = new Zend_Loader_Autoloader_Resource(array('basePath' => dirname(__FILE__)));
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testAutoloaderInstantiationShouldRaiseExceptionWithoutBasePath()
    {
        $loader = new Zend_Loader_Autoloader_Resource(array('namespace' => 'Foo'));
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testAutoloaderInstantiationShouldRaiseExceptionWhenInvalidOptionsTypeProvided()
    {
        $loader = new Zend_Loader_Autoloader_Resource('foo');
    }

    public function testAutoloaderConstructorShouldAcceptZendConfigObject()
    {
        $config = new Zend_Config(array('namespace' => 'Foo', 'basePath' => dirname(__FILE__)));
        $loader = new Zend_Loader_Autoloader_Resource($config);
    }

    public function testAutoloaderShouldAllowRetrievingNamespace()
    {
        $this->assertEquals('FooBar', $this->loader->getNamespace());
    }

    public function testAutoloaderShouldAllowRetrievingBasePath()
    {
        $this->assertEquals(realpath(dirname(__FILE__) . '/_files'), $this->loader->getBasePath());
    }

    public function testNoResourceTypesShouldBeRegisteredByDefault()
    {
        $resourceTypes = $this->loader->getResourceTypes();
        $this->assertTrue(is_array($resourceTypes));
        $this->assertTrue(empty($resourceTypes));
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testInitialResourceTypeDefinitionShouldRequireNamespace()
    {
        $this->loader->addResourceType('foo', 'foo');
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testPassingNonStringPathWhenAddingResourceTypeShouldRaiseAnException()
    {
        $this->loader->addResourceType('foo', array('foo'), 'Foo');
    }

    public function testAutoloaderShouldAllowAddingArbitraryResourceTypes()
    {
        $this->loader->addResourceType('models', 'models', 'Model');
        $resources = $this->loader->getResourceTypes();
        $this->assertTrue(array_key_exists('models', $resources));
        $this->assertEquals($this->loader->getNamespace() . '_Model', $resources['models']['namespace']);
        $this->assertContains('/models', $resources['models']['path']);
    }

    public function testAutoloaderShouldAllowAddingResettingResourcePaths()
    {
        $this->loader->addResourceType('models', 'models', 'Model');
        $this->loader->addResourceType('models', 'apis');
        $resources = $this->loader->getResourceTypes();
        $this->assertNotContains('/models', $resources['models']['path']);
        $this->assertContains('/apis', $resources['models']['path']);
    }

    public function testAutoloaderShouldSupportAddingMultipleResourceTypesAtOnce()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
            'form'  => array('path' => 'forms', 'namespace' => 'Form'),
        ));
        $resources = $this->loader->getResourceTypes();
        $this->assertContains('model', array_keys($resources));
        $this->assertContains('form', array_keys($resources));
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testAddingMultipleResourceTypesShouldRaiseExceptionWhenReceivingNonArrayItem()
    {
        $this->loader->addResourceTypes(array('foo' => 'bar'));
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testAddingMultipleResourceTypesShouldRaiseExceptionWhenMissingResourcePath()
    {
        $this->loader->addResourceTypes(array('model' => array('namespace' => 'Model')));
    }

    public function testSetResourceTypesShouldOverwriteExistingResourceTypes()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
            'form'  => array('path' => 'forms', 'namespace' => 'Form'),
        ));

        $this->loader->setResourceTypes(array(
            'view'   => array('path' => 'views', 'namespace' => 'View'),
            'layout' => array('path' => 'layouts', 'namespace' => 'Layout'),
        ));

        $resources = $this->loader->getResourceTypes();
        $this->assertNotContains('model', array_keys($resources));
        $this->assertNotContains('form', array_keys($resources));
        $this->assertContains('view', array_keys($resources));
        $this->assertContains('layout', array_keys($resources));
    }

    public function testHasResourceTypeShouldReturnFalseWhenTypeNotDefined()
    {
        $this->assertFalse($this->loader->hasResourceType('model'));
    }

    public function testHasResourceTypeShouldReturnTrueWhenTypeIsDefined()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $this->assertTrue($this->loader->hasResourceType('model'));
    }

    public function testRemoveResourceTypeShouldRemoveResourceFromList()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
            'form'  => array('path' => 'forms', 'namespace' => 'Form'),
        ));
        $this->loader->removeResourceType('form');

        $resources = $this->loader->getResourceTypes();
        $this->assertContains('model', array_keys($resources));
        $this->assertNotContains('form', array_keys($resources));
    }

    public function testAutoloaderShouldAllowSettingDefaultResourceType()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $this->loader->setDefaultResourceType('model');
        $this->assertEquals('model', $this->loader->getDefaultResourceType());
    }

    public function testSettingDefaultResourceTypeToUndefinedTypeShouldHaveNoEffect()
    {
        $this->loader->setDefaultResourceType('model');
        $this->assertNull($this->loader->getDefaultResourceType());
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testLoadShouldRaiseExceptionWhenNotTypePassedAndNoDefaultSpecified()
    {
        $this->loader->load('Foo');
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testLoadShouldRaiseExceptionWhenResourceTypeDoesNotExist()
    {
        $this->loader->load('Foo', 'model');
    }

    public function testLoadShouldReturnObjectOfExpectedClass()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $object = $this->loader->load('ZendLoaderAutoloaderResourceTest', 'model');
        $this->assertTrue($object instanceof FooBar_Model_ZendLoaderAutoloaderResourceTest);
    }

    public function testSuccessiveCallsToLoadSameResourceShouldReturnSameObject()
    {
        $this->loader->addResourceTypes(array(
            'form' => array('path' => 'forms', 'namespace' => 'Form'),
        ));
        $object = $this->loader->load('ZendLoaderAutoloaderResourceTest', 'form');
        $this->assertTrue($object instanceof FooBar_Form_ZendLoaderAutoloaderResourceTest);
        $test   = $this->loader->load('ZendLoaderAutoloaderResourceTest', 'form');
        $this->assertSame($object, $test);
    }

    public function testAutoloadShouldAllowEmptyNamespacing()
    {
        $loader = new Zend_Loader_Autoloader_Resource(array(
            'namespace' => '',
            'basePath'  => realpath(dirname(__FILE__) . '/_files'),
        ));
        $loader->addResourceTypes(array(
            'service' => array('path' => 'services', 'namespace' => 'Service'),
        ));
        $test = $loader->load('ZendLoaderAutoloaderResourceTest', 'service');
        $this->assertTrue($test instanceof Service_ZendLoaderAutoloaderResourceTest);
    }

    public function testPassingClassOfDifferentNamespaceToAutoloadShouldReturnFalse()
    {
        $this->assertFalse($this->loader->autoload('Foo_Bar_Baz'));
    }

    public function testPassingClassWithoutBothComponentAndClassSegmentsToAutoloadShouldReturnFalse()
    {
        $this->assertFalse($this->loader->autoload('FooBar_Baz'));
    }

    public function testPassingClassWithUnmatchedResourceTypeToAutoloadShouldReturnFalse()
    {
        $this->assertFalse($this->loader->autoload('FooBar_Baz_Bat'));
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testMethodOverloadingShouldRaiseExceptionForNonGetterMethodCalls()
    {
        $this->loader->lalalalala();
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testMethodOverloadingShouldRaiseExceptionWhenRequestedResourceDoesNotExist()
    {
        $this->loader->getModel('Foo');
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testMethodOverloadingShouldRaiseExceptionWhenNoArgumentPassed()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $this->loader->getModel();
    }

    public function testMethodOverloadingShouldReturnObjectOfExpectedType()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $test = $this->loader->getModel('ZendLoaderAutoloaderResourceMethodOverloading');
        $this->assertTrue($test instanceof FooBar_Model_ZendLoaderAutoloaderResourceMethodOverloading);
    }

    /**
     * @group ZF-7473
     */
    public function testAutoloaderShouldReceiveNamespaceWithTrailingUnderscore()
    {
        $al = Zend_Loader_Autoloader::getInstance();
        $loaders = $al->getNamespaceAutoloaders('FooBar');
        $this->assertTrue(empty($loaders));
        $loaders = $al->getNamespaceAutoloaders('FooBar_');
        $this->assertFalse(empty($loaders));
        $loader = array_shift($loaders);
        $this->assertSame($this->loader, $loader);
    }

    /**
     * @group ZF-7501
     */
    public function testAutoloaderShouldTrimResourceTypePathsForTrailingPathSeparator()
    {
        $this->loader->addResourceType('models', 'models/', 'Model');
        $resources = $this->loader->getResourceTypes();
        $this->assertEquals($this->loader->getBasePath() . '/models', $resources['models']['path']);
    }

    /**
     * @group ZF-6727
     */
    public function testAutoloaderResourceGetClassPath()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $path = $this->loader->getClassPath('FooBar_Model_Class_Model');
        // if true we have // in path
        $this->assertFalse(strpos($path, '//'));
    }

    /**
     * @group ZF-8364
     * @group ZF-6727
     */
    public function testAutoloaderResourceGetClassPathReturnFalse()
    {
        $this->loader->addResourceTypes(array(
            'model' => array('path' => 'models', 'namespace' => 'Model'),
        ));
        $path = $this->loader->autoload('Something_Totally_Wrong');
        $this->assertFalse($path);
    }

    /**
     * @group ZF-10836
     */
    public function testConstructorAcceptsNamespaceKeyInAnyOrder()
    {
        // namespace is after resourceTypes - fails in ZF 1.11.1
        $data = array(
            'basePath'      => 'path/to/some/directory',
            'resourceTypes' => array(
                'acl' => array(
                    'path'      => 'acls/',
                    'namespace' => 'Acl',
                )
            ),
            'namespace'     => 'My'
        );
        $loader1 = new Zend_Loader_Autoloader_Resource($data);

        // namespace is defined before resourceTypes - always worked as expected
        $data = array(
            'basePath'      => 'path/to/some/directory',
            'namespace'     => 'My',
            'resourceTypes' => array(
                'acl' => array(
                    'path'      => 'acls/',
                    'namespace' => 'Acl',
                )
            )
        );
        $loader2 = new Zend_Loader_Autoloader_Resource($data);

        // Check that autoloaders are configured the same
        $this->assertEquals($loader1, $loader2);
    }

    /**
     * @group ZF-11219
     */
    public function testMatchesMultiLevelNamespaces()
    {
        $this->loader->setNamespace('Foo_Bar')
            ->setBasePath(dirname(__FILE__) . '/_files')
            ->addResourceType('model', 'models', 'Model');
        $path = $this->loader->getClassPath('Foo_Bar_Model_Baz');
        $this->assertEquals(dirname(__FILE__) . '/_files/models/Baz.php', $path, var_export($path, 1));
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Loader_Autoloader_ResourceTest::main') {
    Zend_Loader_Autoloader_ResourceTest::main();
}
