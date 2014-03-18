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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Validate_InArray
 */
require_once 'Zend/Validate/InArray.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_InArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $validator = new Zend_Validate_InArray(array(1, 'a', 2.3));
        $this->assertTrue($validator->isValid(1));
        $this->assertTrue($validator->isValid(1.0));
        $this->assertTrue($validator->isValid('1'));
        $this->assertTrue($validator->isValid('a'));
        $this->assertFalse($validator->isValid('A'));
        $this->assertTrue($validator->isValid(2.3));
        $this->assertTrue($validator->isValid(2.3e0));
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $validator = new Zend_Validate_InArray(array(1, 2, 3));
        $this->assertEquals(array(), $validator->getMessages());
    }

    /**
     * Ensures that getHaystack() returns expected value
     *
     * @return void
     */
    public function testGetHaystack()
    {
        $validator = new Zend_Validate_InArray(array(1, 2, 3));
        $this->assertEquals(array(1, 2, 3), $validator->getHaystack());
    }

    /**
     * Ensures that getStrict() returns expected default value
     *
     * @return void
     */
    public function testGetStrict()
    {
        $validator = new Zend_Validate_InArray(array(1, 2, 3));
        $this->assertFalse($validator->getStrict());
    }

    public function testGivingOptionsAsArrayAtInitiation()
    {
        $validator = new Zend_Validate_InArray(
            array('haystack' =>
                array(1, 'a', 2.3)
            )
        );
        $this->assertTrue($validator->isValid(1));
        $this->assertTrue($validator->isValid(1.0));
        $this->assertTrue($validator->isValid('1'));
        $this->assertTrue($validator->isValid('a'));
        $this->assertFalse($validator->isValid('A'));
        $this->assertTrue($validator->isValid(2.3));
        $this->assertTrue($validator->isValid(2.3e0));
    }

    public function testSettingANewHaystack()
    {
        $validator = new Zend_Validate_InArray(
            array('haystack' =>
                array('test', 0, 'A')
            )
        );
        $this->assertTrue($validator->isValid('A'));

        $validator->setHaystack(array(1, 'a', 2.3));
        $this->assertTrue($validator->isValid(1));
        $this->assertTrue($validator->isValid(1.0));
        $this->assertTrue($validator->isValid('1'));
        $this->assertTrue($validator->isValid('a'));
        $this->assertFalse($validator->isValid('A'));
        $this->assertTrue($validator->isValid(2.3));
        $this->assertTrue($validator->isValid(2.3e0));
    }

    public function testSettingNewStrictMode()
    {
        $validator = new Zend_Validate_InArray(array(1, 2, 3));
        $this->assertFalse($validator->getStrict());
        $this->assertTrue($validator->isValid('1'));
        $this->assertTrue($validator->isValid(1));

        $validator->setStrict(true);
        $this->assertTrue($validator->getStrict());
        $this->assertFalse($validator->isValid('1'));
        $this->assertTrue($validator->isValid(1));
    }

    public function testSettingStrictViaInitiation()
    {
        $validator = new Zend_Validate_InArray(
            array(
                'haystack' => array('test', 0, 'A'),
                'strict'   => true
            )
        );
        $this->assertTrue($validator->getStrict());
    }

    public function testGettingRecursiveOption()
    {
        $validator = new Zend_Validate_InArray(array(1, 2, 3));
        $this->assertFalse($validator->getRecursive());

        $validator->setRecursive(true);
        $this->assertTrue($validator->getRecursive());
    }

    public function testSettingRecursiveViaInitiation()
    {
        $validator = new Zend_Validate_InArray(
            array(
                'haystack'  => array('test', 0, 'A'),
                'recursive' => true
            )
        );
        $this->assertTrue($validator->getRecursive());
    }

    public function testRecursiveDetection()
    {
        $validator = new Zend_Validate_InArray(
            array(
                'haystack'  =>
                    array(
                        'firstDimension' => array('test', 0, 'A'),
                        'secondDimension' => array('value', 2, 'a')),
                'recursive' => false
            )
        );
        $this->assertFalse($validator->isValid('A'));

        $validator->setRecursive(true);
        $this->assertTrue($validator->isValid('A'));
    }

    public function testRecursiveStandalone()
    {
        $validator = new Zend_Validate_InArray(
            array(
                'firstDimension' => array('test', 0, 'A'),
                'secondDimension' => array('value', 2, 'a')
            )
        );
        $this->assertFalse($validator->isValid('A'));

        $validator->setRecursive(true);
        $this->assertTrue($validator->isValid('A'));
    }
}
