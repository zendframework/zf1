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
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Controller_Router_Route */
require_once 'Zend/Controller/Router/Route/Static.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Router
 */
class Zend_Controller_Router_Route_StaticTest extends PHPUnit_Framework_TestCase
{

    public function testStaticMatch()
    {
        $route = new Zend_Controller_Router_Route_Static('users/all');
        $values = $route->match('users/all');

        $this->assertTrue(is_array($values));
    }

    public function testStaticMatchFailure()
    {
        $route = new Zend_Controller_Router_Route_Static('archive/2006');
        $values = $route->match('users/all');

        $this->assertSame(false, $values);
    }

    public function testStaticMatchWithDefaults()
    {
        $route = new Zend_Controller_Router_Route_Static('users/all',
                    array('controller' => 'ctrl', 'action' => 'act'));
        $values = $route->match('users/all');

        $this->assertTrue(is_array($values));
        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);
    }

    public function testStaticUTFMatch()
    {
        $route = new Zend_Controller_Router_Route_Static('żółć');
        $values = $route->match('żółć');

        $this->assertTrue(is_array($values));
    }

    public function testRootRoute()
    {
        $route = new Zend_Controller_Router_Route_Static('/');
        $values = $route->match('');

        $this->assertSame(array(), $values);
    }

    public function testAssemble()
    {
        $route = new Zend_Controller_Router_Route_Static('/about');
        $url = $route->assemble();

        $this->assertSame('about', $url);
    }

    public function testGetDefaults()
    {
        $route = new Zend_Controller_Router_Route_Static('users/all',
                    array('controller' => 'ctrl', 'action' => 'act'));

        $values = $route->getDefaults();

        $this->assertTrue(is_array($values));
        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);
    }

    public function testGetDefault()
    {
        $route = new Zend_Controller_Router_Route_Static('users/all',
                    array('controller' => 'ctrl', 'action' => 'act'));

        $this->assertSame('ctrl', $route->getDefault('controller'));
        $this->assertSame(null, $route->getDefault('bogus'));
    }

    public function testGetInstance()
    {
        require_once 'Zend/Config.php';

        $routeConf = array(
            'route' => 'users/all',
            'defaults' => array(
                'controller' => 'ctrl'
            )
        );

        $config = new Zend_Config($routeConf);
        $route = Zend_Controller_Router_Route_Static::getInstance($config);

        $this->assertTrue($route instanceof Zend_Controller_Router_Route_Static);

        $values = $route->match('users/all');

        $this->assertSame('ctrl', $values['controller']);

    }

}
