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
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Navigation/Page/Mvc.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Router/Route.php';
require_once 'Zend/Controller/Router/Route/Regex.php';
require_once 'Zend/Controller/Router/Route/Chain.php';

/**
 * Tests the class Zend_Navigation_Page_Mvc
 *
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Navigation
 */
class Zend_Navigation_Page_MvcTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Controller_Front
     */
    protected $_front;

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_oldRequest;

    /**
     * @var Zend_Controller_Router_Interface
     */
    protected $_oldRouter;

    protected function setUp()
    {
        $this->_front = Zend_Controller_Front::getInstance();
        $this->_oldRequest = $this->_front->getRequest();
        $this->_oldRouter = $this->_front->getRouter();

        $this->_front->resetInstance();

        $_SERVER['HTTP_HOST'] = 'foobar.example.com';

        $this->_front->setRequest(new Zend_Controller_Request_Http());
        $this->_front->getRouter()->addDefaultRoutes();
    }

    protected function tearDown()
    {
        if (null !== $this->_oldRequest) {
            $this->_front->setRequest($this->_oldRequest);
        } else {
            $this->_front->setRequest(new Zend_Controller_Request_Http());
        }
        $this->_front->setRouter($this->_oldRouter);
    }

    public function testHrefGeneratedByUrlHelperRequiresNoRoute()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $page->setAction('view');
        $page->setController('news');

        $this->assertEquals('/news/view', $page->getHref());
    }

    public function testHrefGeneratedIsRouteAware()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'myaction',
            'controller' => 'mycontroller',
            'route' => 'myroute',
            'params' => array(
                'page' => 1337
            )
        ));

        $this->_front->getRouter()->addRoute(
            'myroute',
            new Zend_Controller_Router_Route(
                'lolcat/:action/:page',
                array(
                    'module'     => 'default',
                    'controller' => 'foobar',
                    'action'     => 'bazbat',
                    'page'       => 1
                )
            )
        );

        $this->assertEquals('/lolcat/myaction/1337', $page->getHref());
    }

    /**
     * @group ZF-8922
     */
    public function testGetHrefWithFragmentIdentifier()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label'              => 'foo',
            'fragment' => 'qux',
            'controller'         => 'mycontroller',
            'action'             => 'myaction',
            'route'              => 'myroute',
            'params'             => array(
                'page' => 1337
            )
        ));
 
        $this->_front->getRouter()->addRoute(
            'myroute',
            new Zend_Controller_Router_Route(
                'lolcat/:action/:page',
                array(
                    'module'     => 'default',
                    'controller' => 'foobar',
                    'action'     => 'bazbat',
                    'page'       => 1
                )
            )
        );
 
        $this->assertEquals('/lolcat/myaction/1337#qux', $page->getHref());
    } 

    public function testIsActiveReturnsTrueOnIdenticalModuleControllerAction()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $this->_front->getRequest()->setParams(array(
            'module' => 'default',
            'controller' => 'index',
            'action' => 'index'
        ));

        $this->assertEquals(true, $page->isActive());
    }

    public function testIsActiveReturnsFalseOnDifferentModuleControllerAction()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'bar',
            'controller' => 'index'
        ));

        $this->_front->getRequest()->setParams(array(
            'module' => 'default',
            'controller' => 'index',
            'action' => 'index'
        ));

        $this->assertEquals(false, $page->isActive());
    }

    public function testIsActiveReturnsTrueOnIdenticalIncludingPageParams()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'view',
            'controller' => 'post',
            'module' => 'blog',
            'params' => array(
                'id' => '1337'
            )
        ));

        $this->_front->getRequest()->setParams(array(
            'module' => 'blog',
            'controller' => 'post',
            'action' => 'view',
            'id' => '1337'
        ));

        $this->assertEquals(true, $page->isActive());
    }

    public function testIsActiveReturnsTrueWhenRequestHasMoreParams()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'view',
            'controller' => 'post',
            'module' => 'blog'
        ));

        $this->_front->getRequest()->setParams(array(
            'module' => 'blog',
            'controller' => 'post',
            'action' => 'view',
            'id' => '1337'
        ));

        $this->assertEquals(true, $page->isActive());
    }

    public function testIsActiveReturnsFalseWhenRequestHasLessParams()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'view',
            'controller' => 'post',
            'module' => 'blog',
            'params' => array(
                'id' => '1337'
            )
        ));

        $this->_front->getRequest()->setParams(array(
            'module' => 'blog',
            'controller' => 'post',
            'action' => 'view',
            'id' => null
        ));

        $this->assertEquals(false, $page->isActive());
    }

    public function testIsActiveIsRouteAware()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'myaction',
            'route' => 'myroute',
            'params' => array(
                'page' => 1337
            )
        ));

        $this->_front->getRouter()->addRoute(
            'myroute',
            new Zend_Controller_Router_Route(
                'lolcat/:action/:page',
                array(
                    'module'     => 'default',
                    'controller' => 'foobar',
                    'action'     => 'bazbat',
                    'page'       => 1
                )
            )
        );

        $this->_front->getRequest()->setParams(array(
            'module' => 'default',
            'controller' => 'foobar',
            'action' => 'myaction',
            'page' => 1337
        ));

        $this->assertEquals(true, $page->isActive());
    }

    /**
     * @group ZF-11664
     */
    public function testIsActiveWithoutAndWithRecursiveOption()
    {
        // Parent
        $page = new Zend_Navigation_Page_Mvc(array(
            'controller' => 'index',
            'action'     => 'index',
        ));

        // Child
        $page->addPage(new Zend_Navigation_Page_Mvc(array(
            'controller' => 'index',
            'action'     => 'foo',
        )));

        // Front controller
        $this->_front->getRequest()->setParams(array(
            'controller' => 'index',
            'action'     => 'foo'
        ));

        $this->assertFalse($page->isActive());

        $this->assertTrue($page->isActive(true));
    }

    public function testActionAndControllerAccessors()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $props = array('Action', 'Controller');
        $valids = array('index', 'help', 'home', 'default', '1', ' ', '', null);
        $invalids = array(42, (object) null);

        foreach ($props as $prop) {
            $setter = "set$prop";
            $getter = "get$prop";

            foreach ($valids as $valid) {
                $page->$setter($valid);
                $this->assertEquals($valid, $page->$getter());
            }

            foreach ($invalids as $invalid) {
                try {
                    $page->$setter($invalid);
                    $msg = "'$invalid' is invalid for $setter(), but no ";
                    $msg .= 'Zend_Navigation_Exception was thrown';
                    $this->fail($msg);
                } catch (Zend_Navigation_Exception $e) {

                }
            }
        }
    }

    public function testModuleAndRouteAccessors()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $props = array('Module', 'Route');
        $valids = array('index', 'help', 'home', 'default', '1', ' ', null);
        $invalids = array(42, (object) null);

        foreach ($props as $prop) {
            $setter = "set$prop";
            $getter = "get$prop";

            foreach ($valids as $valid) {
                $page->$setter($valid);
                $this->assertEquals($valid, $page->$getter());
            }

            foreach ($invalids as $invalid) {
                try {
                    $page->$setter($invalid);
                    $msg = "'$invalid' is invalid for $setter(), but no ";
                    $msg .= 'Zend_Navigation_Exception was thrown';
                    $this->fail($msg);
                } catch (Zend_Navigation_Exception $e) {

                }
            }
        }
    }

    public function testSetAndGetResetParams()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $valids = array(true, 1, '1', 3.14, 'true', 'yes');
        foreach ($valids as $valid) {
            $page->setResetParams($valid);
            $this->assertEquals(true, $page->getResetParams());
        }

        $invalids = array(false, 0, '0', 0.0, array());
        foreach ($invalids as $invalid) {
            $page->setResetParams($invalid);
            $this->assertEquals(false, $page->getResetParams());
        }
    }

    public function testSetAndGetParams()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $params = array('foo' => 'bar', 'baz' => 'bat');

        $page->setParams($params);
        $this->assertEquals($params, $page->getParams());

        $page->setParams();
        $this->assertEquals(array(), $page->getParams());

        $page->setParams($params);
        $this->assertEquals($params, $page->getParams());

        $page->setParams(array());
        $this->assertEquals(array(), $page->getParams());
    }
    
    /**
     * @group ZF-10727
     */
    public function testSetAndGetParam()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));
        
        $page->setParam('foo', 'bar');
        $this->assertEquals('bar', $page->getParam('foo'));
        
        // Check type conversion
        $page->setParam(null, null);
        $this->assertEquals(null, $page->getParam('null'));
    }
    
    /**
     * @group ZF-10727
     */
    public function testAddParams()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));
        
        $params = array('foo' => 'bar', 'baz' => 'bat');
        
        $page->addParams($params);
        $this->assertEquals($params, $page->getParams());
        
        $params2 = array('qux' => 'foobar');
        
        $page->addParams($params2);
        $this->assertEquals(array_merge($params, $params2), $page->getParams());
    }
    
    /**
     * @group ZF-10727
     */
    public function testRemoveParam()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));
        
        $params = array('foo' => 'bar', 'baz' => 'bat');
        
        $page->setParams($params);
        $page->removeParam('foo');
        
        $this->assertEquals(array('baz' => 'bat'), $page->getParams());
        
        $this->assertNull($page->getParam('foo'));
    }
    
    /**
     * @group ZF-10727
     */
    public function testClearParams()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));
        
        $params = array('foo' => 'bar', 'baz' => 'bat');
        
        $page->setParams($params);
        $page->clearParams();
        
        $this->assertEquals(array(), $page->getParams());
    }

    /**
     * @group ZF-11664
     */
    public function testSetActiveAndIsActive()
    {
        // Page
        $page = new Zend_Navigation_Page_Mvc(array(
            'controller' => 'foo',
            'action'     => 'bar',
        ));

        // Front controller
        $this->_front->getRequest()->setParams(array(
            'controller' => 'foo',
            'action'     => 'bar'
        ));

        $this->assertTrue($page->isActive());

        $page->setActive(false);
        $this->assertFalse($page->isActive());
    }

    /**
     * @group ZF-10465
     */
    public function testSetAndGetEncodeUrl()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label'      => 'foo',
            'action'     => 'index',
            'controller' => 'index',
        ));
        
        $page->setEncodeUrl(false);
        $this->assertEquals(false, $page->getEncodeUrl());
    }
    
    /**
     * @group ZF-10465
     */
    public function testEncodeUrlIsRouteAware()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label'      => 'foo',
            'route'      => 'myroute',
            'encodeUrl'  => false,
            'params'     => array(
                'contentKey' => 'pagexy/subpage',
            )
        ));
 
        $this->_front->getRouter()->addRoute(
            'myroute',
            new Zend_Controller_Router_Route_Regex(
                '(.+)\.html',
                array(
                    'module'     => 'default',
                    'controller' => 'foobar',
                    'action'     => 'bazbat',
                ),
                array(
                    1 => 'contentKey'
                ),
                '%s.html'
            )
        );

        $this->assertEquals('/pagexy/subpage.html', $page->getHref());
    }

    /**
     * @group ZF-7794
     */
    public function testSetScheme()
    {
        $page = new Zend_Navigation_Page_Mvc();
        $page->setScheme('https');

        $this->assertEquals('https', $page->getScheme());
    }

    /**
     * @group ZF-7794
     */
    public function testOptionScheme()
    {
        $page = new Zend_Navigation_Page_Mvc(
            array(
                 'scheme' => 'https',
            )
        );

        $this->assertEquals('https', $page->getScheme());
    }

    /**
     * @group ZF-7794
     */
    public function testHrefGeneratedWithScheme()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'controller' => 'foo',
            'action'     => 'bar',
            'scheme'     => 'https',
        ));

        $this->assertEquals(
            'https://foobar.example.com/foo/bar',
            $page->getHref()
        );
    }

    /**
     * @group ZF-7794
     */
    public function testHrefGeneratedWithSchemeIsRouteAware()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'action'     => 'myaction',
            'controller' => 'mycontroller',
            'route'      => 'myroute',
            'params'     => array(
                'page' => 1337,
            ),
            'scheme'     => 'https',
        ));

        $this->_front->getRouter()->addRoute(
            'myroute',
            new Zend_Controller_Router_Route(
                'lolcat/:action/:page',
                array(
                    'module'     => 'default',
                    'controller' => 'foobar',
                    'action'     => 'bazbat',
                    'page'       => 1,
                )
            )
        );

        $this->assertEquals(
            'https://foobar.example.com/lolcat/myaction/1337',
            $page->getHref()
        );
    }

    public function testToArrayMethod()
    {
        $options = array(
            'accesskey'  => null,
            'label'      => 'foo',
            'action'     => 'index',
            'controller' => 'index',
            'customHtmlAttribs' => array(),
            'module'     => 'test',
            'fragment'   => 'bar',
            'id'         => 'my-id',
            'class'      => 'my-class',
            'title'      => 'my-title',
            'target'     => 'my-target',
            'order'      => 100,
            'pages'      => array(),
            'active'     => true,
            'visible'    => false,
            'encodeUrl'  => false,
            'scheme'     => 'https',
            'foo'        => 'bar',
            'meaning'    => 42
        );

        $page = new Zend_Navigation_Page_Mvc($options);

        $toArray = $page->toArray();

        $options['reset_params'] = true;
        $options['route']        = null;
        $options['params']       = array();
        $options['privilege']    = null;
        $options['rel']          = array();
        $options['resource']     = null;
        $options['rev']          = array();
        $options['type']         = 'Zend_Navigation_Page_Mvc';

        $this->assertEquals(
            $options,
            $toArray
        );
    }

    public function testSpecifyingAnotherUrlHelperToGenerateHrefs()
    {
        $path = dirname(dirname(__FILE__)) . '/_files/My/UrlHelper.php';
        require_once $path;

        $newHelper = new My_UrlHelper();
        Zend_Navigation_Page_Mvc::setUrlHelper($newHelper);

        $page = new Zend_Navigation_Page_Mvc();

        $expected = My_UrlHelper::RETURN_URL;
        $actual = $page->getHref();

        $old = Zend_Controller_Action_HelperBroker::getStaticHelper('Url');
        Zend_Navigation_Page_Mvc::setUrlHelper($old);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @group ZF-7794
     */
    public function testSpecifyingAnotherSchemeHelperToGenerateHrefs()
    {
        $path = dirname(dirname(__FILE__)) . '/_files/My/SchemeHelper.php';
        require_once $path;

        $newHelper = new My_SchemeHelper();
        Zend_Navigation_Page_Mvc::setSchemeHelper($newHelper);

        $page = new Zend_Navigation_Page_Mvc(
            array(
                 'scheme' => 'https',
            )
        );

        $expected = My_SchemeHelper::RETURN_URL;
        $actual   = $page->getHref();

        $old = new Zend_View_Helper_ServerUrl();
        Zend_Navigation_Page_Mvc::setSchemeHelper($old);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @group ZF-11550
     */
    public function testNullValuesInMatchedRouteWillStillReturnMatchedPage()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'route'      => 'default',
            'module'     => 'default',
            'controller' => 'index',
            'action'     => 'index',
            'label'      => 'Home',
            'title'      => 'Home',
        ));

        $this->_front->getRouter()->addRoute(
            'default',
            new Zend_Controller_Router_Route(
                ':locale/:module/:controller/:action/*',
                array(
                    'locale'     => null,
                    'module'     => 'default',
                    'controller' => 'index',
                    'action'     => 'index',
                ),
                array(
                    'locale'     => '.*',
                    'module'     => '.*',
                    'controller' => '.*',
                    'action'     => '.*',
                )
            )
        );

        $this->_front->getRequest()->setParams(array(
            'locale'     => 'en_US',
            'module'     => 'default',
            'controller' => 'index',
            'action'     => 'index',
        ));

        $this->assertEquals(true, $page->isActive());
    }

    /**
     * @group ZF-12414
     */
    public function testNullValueInParameters()
    {
        // Create pages
        $pages         = array();
        $pages['home'] = new Zend_Navigation_Page_Mvc(
            array(
                 'label'      => 'Home',
                 'route'      => 'page',
                 'params'     => array(
                     'slug' => '',
                 ),
            )
        );
        $pages['news'] = new Zend_Navigation_Page_Mvc(
            array(
                 'label'      => 'News',
                 'route'      => 'page',
                 'params'     => array(
                     'slug' => 'news',
                 ),
            )
        );

        // Add route
        $this->_front->getRouter()->addRoute(
            'page',
            new Zend_Controller_Router_Route_Regex(
                '((?!(admin|page)).*)',
                array(
                    'module'     => 'page',
                    'controller' => 'index',
                    'action'     => 'index',
                ),
                array(
                    1 => 'slug',
                ),
                '%s'
            )
        );

        // Set request
        $this->_front->getRequest()->setParams(
            array(
                 'module'     => 'page',
                 'controller' => 'index',
                 'action'     => 'index',
                 'slug'       => 'news',
            )
        );

        $this->assertTrue($pages['news']->isActive());
        $this->assertFalse($pages['home']->isActive());
    }

    /**
     * @group ZF-11442
     */
    public function testIsActiveIsChainedRouteAware()
    {
        // Create page
        $page = new Zend_Navigation_Page_Mvc(
            array(
                 'action' => 'myaction',
                 'route'  => 'myroute',
                 'params' => array(
                     'page' => 1337,
                     'item' => 1234
                 )
            )
        );

        // Create chained route
        $chain = new Zend_Controller_Router_Route_Chain();

        $foo = new Zend_Controller_Router_Route(
            'lolcat/:action',
            array(
                 'module'     => 'default',
                 'controller' => 'foobar',
                 'action'     => 'bazbat'
            )
        );
        $bar = new Zend_Controller_Router_Route(
            ':page/:item',
            array(
                 'page' => 1,
                 'item' => 1
            )
        );
        $chain->chain($foo)->chain($bar);

        // Set up router
        $this->_front->getRouter()->addRoute('myroute', $chain);

        $this->_front->getRequest()->setParams(
            array(
                 'module'     => 'default',
                 'controller' => 'foobar',
                 'action'     => 'myaction',
                 'page'       => 1337,
                 'item'       => 1234
            )
        );

        // Test
        $this->assertTrue($page->isActive());
    }
}
