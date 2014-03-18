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
 * @package    Zend_Tag
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Tag_Cloud_CloudTest::main');
}

require_once 'Zend/Config.php';
require_once 'Zend/Loader/PluginLoader.php';
require_once 'Zend/Tag/Cloud.php';
require_once 'Zend/Tag/ItemList.php';
require_once dirname(__FILE__) . '/_classes/CloudDummy.php';
require_once dirname(__FILE__) . '/_classes/TagDummy.php';

/**
 * @category   Zend
 * @package    Zend_Tag
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Tag
 * @group      Zend_Tag_Cloud
 */
class Zend_Tag_Cloud_CloudTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testGetAndSetItemList()
    {
        $cloud = $this->_getCloud();
        $this->assertTrue($cloud->getItemList() instanceof Zend_Tag_ItemList);

        $cloud->setItemList(new Zend_Tag_ItemListDummy);
        $this->assertTrue($cloud->getItemList() instanceof Zend_Tag_ItemListDummy);
    }

    public function testSetCloudDecoratorViaArray()
    {
        $cloud = $this->_getCloud();

        $cloud->setCloudDecorator(array('decorator' => 'CloudDummy', 'options' => array('foo' => 'bar')));
        $this->assertTrue($cloud->getCloudDecorator() instanceof Zend_Tag_Cloud_Decorator_Dummy_CloudDummy);
        $this->assertEquals('bar', $cloud->getCloudDecorator()->getFoo());
    }

    public function testGetAndSetCloudDecorator()
    {
        $cloud = $this->_getCloud();
        $this->assertTrue($cloud->getCloudDecorator() instanceof Zend_Tag_Cloud_Decorator_HtmlCloud);

        $cloud->setCloudDecorator(new Zend_Tag_Cloud_Decorator_Dummy_CloudDummy());
        $this->assertTrue($cloud->getCloudDecorator() instanceof Zend_Tag_Cloud_Decorator_Dummy_CloudDummy);
    }

    public function testSetInvalidCloudDecorator()
    {
        $cloud = $this->_getCloud();

        try {
            $cloud->setCloudDecorator(new stdClass());
            $this->fail('An expected Zend_Tag_Cloud_Exception was not raised');
        } catch (Zend_Tag_Cloud_Exception $e) {
            $this->assertEquals('Decorator is no instance of Zend_Tag_Cloud_Decorator_Cloud', $e->getMessage());
        }
    }

    public function testSetTagDecoratorViaArray()
    {
        $cloud = $this->_getCloud();

        $cloud->setTagDecorator(array('decorator' => 'TagDummy', 'options' => array('foo' => 'bar')));
        $this->assertTrue($cloud->getTagDecorator() instanceof Zend_Tag_Cloud_Decorator_Dummy_TagDummy);
        $this->assertEquals('bar', $cloud->getTagDecorator()->getFoo());
    }

    public function testGetAndSetTagDecorator()
    {
        $cloud = $this->_getCloud();
        $this->assertTrue($cloud->getTagDecorator() instanceof Zend_Tag_Cloud_Decorator_HtmlTag);

        $cloud->setTagDecorator(new Zend_Tag_Cloud_Decorator_Dummy_TagDummy());
        $this->assertTrue($cloud->getTagDecorator() instanceof Zend_Tag_Cloud_Decorator_Dummy_TagDummy);
    }

    public function testSetInvalidTagDecorator()
    {
        $cloud = $this->_getCloud();

        try {
            $cloud->setTagDecorator(new stdClass());
            $this->fail('An expected Zend_Tag_Cloud_Exception was not raised');
        } catch (Zend_Tag_Cloud_Exception $e) {
            $this->assertEquals('Decorator is no instance of Zend_Tag_Cloud_Decorator_Tag', $e->getMessage());
        }
    }

    public function testSetPrefixPathViaOptions()
    {
        $cloud = $this->_getCloud(array(
            'prefixPath' => array(
                'prefix' => 'Zend_Tag_Cloud_Decorator_Dummy_',
                'path' => dirname(__FILE__) . '/_classes'
            ),
            'cloudDecorator' => array(
                'decorator' => 'CloudDummy1',
                'options'   => array(
                    'foo' => 'bar'
                )
            )
        ), false);

        $this->assertTrue($cloud->getCloudDecorator() instanceof Zend_Tag_Cloud_Decorator_Dummy_CloudDummy1);
        $this->assertEquals('bar', $cloud->getCloudDecorator()->getFoo());
    }

    public function testSetPrefixPathsViaOptions()
    {
        $cloud = $this->_getCloud(array(
            'prefixPath' => array(
                array(
                    'prefix' => 'Zend_Tag_Cloud_Decorator_Dummy_',
                    'path' => dirname(__FILE__) . '/_classes'
                )
            ),
            'cloudDecorator' => array(
                'decorator' => 'CloudDummy2',
                'options'   => array(
                    'foo' => 'bar'
                )
            )
        ), false);

        $this->assertTrue($cloud->getCloudDecorator() instanceof Zend_Tag_Cloud_Decorator_Dummy_CloudDummy2);
        $this->assertEquals('bar', $cloud->getCloudDecorator()->getFoo());
    }

    public function testSetPrefixPathsSkip()
    {
        $cloud = $this->_getCloud(array(
            'prefixPath' => array(
                array(
                    'prefix' => 'foobar',
                )
            ),
        ), false);

        $this->assertEquals(1, count($cloud->getPluginLoader()->getPaths()));
    }

    public function testSetPluginLoader()
    {
        $loader = new Zend_Loader_PluginLoader(array('foo_' => 'bar/'));
        $cloud  = $this->_getCloud(array(), null);
        $cloud->setPluginLoader($loader);
        $paths  = $cloud->getPluginLoader()->getPaths();

        $this->assertEquals('bar/', $paths['foo_'][0]);
    }

    public function testSetPluginLoaderViaOptions()
    {
        $loader = new Zend_Loader_PluginLoader(array('foo_' => 'bar/'));
        $cloud  = $this->_getCloud(array('pluginLoader' => $loader), null);
        $paths  = $cloud->getPluginLoader()->getPaths();

        $this->assertEquals('bar/', $paths['foo_'][0]);
    }

    public function testAppendTagAsArray()
    {
        $cloud = $this->_getCloud();
        $list  = $cloud->getItemList();

        $cloud->appendTag(array('title' => 'foo', 'weight' => 1));

        $this->assertEquals('foo', $list[0]->getTitle());
    }

    public function testAppendTagAsItem()
    {
        $cloud = $this->_getCloud();
        $list  = $cloud->getItemList();

        $cloud->appendTag(new Zend_Tag_Item(array('title' => 'foo', 'weight' => 1)));

        $this->assertEquals('foo', $list[0]->getTitle());
    }

    public function testAppendInvalidTag()
    {
        $cloud = $this->_getCloud();

        try {
            $cloud->appendTag('foo');
            $this->fail('An expected Zend_Tag_Cloud_Exception was not raised');
        } catch (Zend_Tag_Cloud_Exception $e) {
            $this->assertEquals('Tag must be an instance of Zend_Tag_Taggable or an array', $e->getMessage());
        }
    }

    public function testSetTagsAsArray()
    {
        $cloud = $this->_getCloud();
        $list  = $cloud->getItemList();

        $cloud->setTags(array(array('title' => 'foo', 'weight' => 1),
                              array('title' => 'bar', 'weight' => 2)));

        $this->assertEquals('foo', $list[0]->getTitle());
        $this->assertEquals('bar', $list[1]->getTitle());
    }

    public function testSetTagsAsItem()
    {
        $cloud = $this->_getCloud();
        $list  = $cloud->getItemList();

        $cloud->setTags(array(new Zend_Tag_Item(array('title' => 'foo', 'weight' => 1)),
                              new Zend_Tag_Item(array('title' => 'bar', 'weight' => 2))));

        $this->assertEquals('foo', $list[0]->getTitle());
        $this->assertEquals('bar', $list[1]->getTitle());
    }

    public function testSetTagsMixed()
    {
        $cloud = $this->_getCloud();
        $list  = $cloud->getItemList();

        $cloud->setTags(array(array('title' => 'foo', 'weight' => 1),
                              new Zend_Tag_Item(array('title' => 'bar', 'weight' => 2))));

        $this->assertEquals('foo', $list[0]->getTitle());
        $this->assertEquals('bar', $list[1]->getTitle());
    }

    public function testSetInvalidTags()
    {
        $cloud = $this->_getCloud();

        try {
            $cloud->setTags(array('foo'));
            $this->fail('An expected Zend_Tag_Cloud_Exception was not raised');
        } catch (Zend_Tag_Cloud_Exception $e) {
            $this->assertEquals('Tag must be an instance of Zend_Tag_Taggable or an array', $e->getMessage());
        }
    }

    public function testConstructorWithArray()
    {
        $cloud = $this->_getCloud(array('tags' => array(array('title' => 'foo', 'weight' => 1))));
        $list  = $cloud->getItemList();

        $this->assertEquals('foo', $list[0]->getTitle());
    }

    public function testConstructorWithConfig()
    {
        $cloud = $this->_getCloud(new Zend_Config(array('tags' => array(array('title' => 'foo', 'weight' => 1)))));
        $list  = $cloud->getItemList();

        $this->assertEquals('foo', $list[0]->getTitle());
    }

    public function testSetOptions()
    {
        $cloud = $this->_getCloud();
        $cloud->setOptions(array('tags' => array(array('title' => 'foo', 'weight' => 1))));
        $list  = $cloud->getItemList();

        $this->assertEquals('foo', $list[0]->getTitle());
    }

    public function testSkipOptions()
    {
        $cloud = $this->_getCloud(array('options' => 'foobar'));
        // In case would fail due to an error
    }

    public function testRender()
    {
        $cloud    = $this->_getCloud(array('tags' => array(array('title' => 'foo', 'weight' => 1), array('title' => 'bar', 'weight' => 3))));
        $expected = '<ul class="Zend_Tag_Cloud">'
                  . '<li><a href="" style="font-size: 10px;">foo</a></li> '
                  . '<li><a href="" style="font-size: 20px;">bar</a></li>'
                  . '</ul>';
        $this->assertEquals($expected, $cloud->render());
    }

    public function testRenderEmptyCloud()
    {
        $cloud = $this->_getCloud();
        $this->assertEquals('', $cloud->render());
    }

    public function testRenderViaToString()
    {
        $cloud = $this->_getCloud(array('tags' => array(array('title' => 'foo', 'weight' => 1), array('title' => 'bar', 'weight' => 3))));
        $expected = '<ul class="Zend_Tag_Cloud">'
                  . '<li><a href="" style="font-size: 10px;">foo</a></li> '
                  . '<li><a href="" style="font-size: 20px;">bar</a></li>'
                  . '</ul>';
        $this->assertEquals($expected, (string) $cloud);
    }

    protected function _getCloud($options = null, $setPluginLoader = true)
    {
        $cloud = new Zend_Tag_Cloud($options);

        if ($setPluginLoader) {
            $cloud->getPluginLoader()->addPrefixPath('Zend_Tag_Cloud_Decorator_Dummy_', dirname(__FILE__) . '/_classes');
        }

        return $cloud;
    }
}

class Zend_Tag_ItemListDummy extends Zend_Tag_ItemList {}

if (PHPUnit_MAIN_METHOD == 'Zend_Tag_Cloud_CloudTest::main') {
    Zend_Tag_Cloud_CloudTest::main();
}
