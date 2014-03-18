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
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version $Id$
 */

require_once 'Zend/Server/Reflection/Node.php';

/**
 * Test case for Zend_Server_Reflection_Node
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Server
 */
class Zend_Server_Reflection_NodeTest extends PHPUnit_Framework_TestCase
{
    /**
     * __construct() test
     */
    public function test__construct()
    {
        $node = new Zend_Server_Reflection_Node('string');
        $this->assertTrue($node instanceof Zend_Server_Reflection_Node);
        $this->assertEquals('string', $node->getValue());
        $this->assertTrue(null === $node->getParent());
        $children = $node->getChildren();
        $this->assertTrue(empty($children));

        $child = new Zend_Server_Reflection_Node('array', $node);
        $this->assertTrue($child instanceof Zend_Server_Reflection_Node);
        $this->assertEquals('array', $child->getValue());
        $this->assertTrue($node === $child->getParent());
        $children = $child->getChildren();
        $this->assertTrue(empty($children));

        $children = $node->getChildren();
        $this->assertTrue($child === $children[0]);
    }

    /**
     * setParent() test
     */
    public function testSetParent()
    {
        $parent = new Zend_Server_Reflection_Node('string');
        $child  = new Zend_Server_Reflection_Node('array');

        $child->setParent($parent);

        $this->assertTrue($parent === $child->getParent());
    }

    /**
     * createChild() test
     */
    public function testCreateChild()
    {
        $parent = new Zend_Server_Reflection_Node('string');
        $child = $parent->createChild('array');

        $this->assertTrue($child instanceof Zend_Server_Reflection_Node);
        $this->assertTrue($parent === $child->getParent());
        $children = $parent->getChildren();
        $this->assertTrue($child === $children[0]);
    }

    /**
     * attachChild() test
     */
    public function testAttachChild()
    {
        $parent = new Zend_Server_Reflection_Node('string');
        $child  = new Zend_Server_Reflection_Node('array');

        $parent->attachChild($child);
        $this->assertTrue($parent === $child->getParent());
        $children = $parent->getChildren();
        $this->assertTrue($child === $children[0]);
    }

    /**
     * getChildren() test
     */
    public function testGetChildren()
    {
        $parent = new Zend_Server_Reflection_Node('string');
        $child = $parent->createChild('array');

        $children = $parent->getChildren();
        $types = array();
        foreach ($children as $c) {
            $types[] = $c->getValue();
        }
        $this->assertTrue(is_array($children));
        $this->assertEquals(1, count($children), var_export($types, 1));
        $this->assertTrue($child === $children[0]);
    }

    /**
     * hasChildren() test
     */
    public function testHasChildren()
    {
        $parent = new Zend_Server_Reflection_Node('string');

        $this->assertFalse($parent->hasChildren());
        $parent->createChild('array');
        $this->assertTrue($parent->hasChildren());
    }

    /**
     * getParent() test
     */
    public function testGetParent()
    {
        $parent = new Zend_Server_Reflection_Node('string');
        $child = $parent->createChild('array');

        $this->assertTrue(null === $parent->getParent());
        $this->assertTrue($parent === $child->getParent());
    }

    /**
     * getValue() test
     */
    public function testGetValue()
    {
        $parent = new Zend_Server_Reflection_Node('string');
        $this->assertEquals('string', $parent->getValue());
    }

    /**
     * setValue() test
     */
    public function testSetValue()
    {
        $parent = new Zend_Server_Reflection_Node('string');
        $this->assertEquals('string', $parent->getValue());
        $parent->setValue('array');
        $this->assertEquals('array', $parent->getValue());
    }

    /**
     * getEndPoints() test
     */
    public function testGetEndPoints()
    {
        $root = new Zend_Server_Reflection_Node('root');
        $child1 = $root->createChild('child1');
        $child2 = $root->createChild('child2');
        $child1grand1 = $child1->createChild(null);
        $child1grand2 = $child1->createChild('child1grand2');
        $child2grand1 = $child2->createChild('child2grand1');
        $child2grand2 = $child2->createChild('child2grand2');
        $child2grand2great1 = $child2grand2->createChild(null);
        $child2grand2great2 = $child2grand2->createChild('child2grand2great2');

        $endPoints = $root->getEndPoints();
        $endPointsArray = array();
        foreach ($endPoints as $endPoint) {
            $endPointsArray[] = $endPoint->getValue();
        }

        $test = array(
            'child1',
            'child1grand2',
            'child2grand1',
            'child2grand2',
            'child2grand2great2'
        );

        $this->assertTrue($test === $endPointsArray, 'Test was [' . var_export($test, 1) . ']; endPoints were [' . var_export($endPointsArray, 1) . ']');
    }
}
