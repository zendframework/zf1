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
 * @package    Zend_Mobile
 * @subpackage Push
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once 'Zend/Mobile/Push/Message/C2dm.php';

/**
 * @category   Zend
 * @package    Zend_Mobile
 * @subpackage Push
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mobile
 * @group      Zend_Mobile_Push
 * @group      Zend_Mobile_Push_C2dm
 */
class Zend_Mobile_Push_Message_C2dmTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Zend_Mobile_Push_Message_Exception
     */
    public function testAddDataThrowsExceptionOnNonStringKey()
    {
        $msg = new Zend_Mobile_Push_Message_C2dm();
        $msg->addData(array(), 'value');
    }

    /**
     * @expectedException Zend_Mobile_Push_Message_Exception
     */
    public function testAddDataThrowsExceptionOnNonScalarValue()
    {
        $msg = new Zend_Mobile_Push_Message_C2dm();
        $msg->addData('key', new stdClass);
    }

    public function testSetData()
    {
        $data = array('key' => 'value');
        $data2 = array('key2' => 'value2');
        $msg = new Zend_Mobile_Push_Message_C2dm();

        $msg->setData($data);
        $this->assertEquals($data, $msg->getData());

        $msg->setData($data2);
        $this->assertEquals($data2, $msg->getData());
    }

    public function testDelayWhileIdle()
    {
        $msg = new Zend_Mobile_Push_Message_C2dm();
        $msg->setDelayWhileIdle(true);
        $this->assertTrue($msg->getDelayWhileIdle());
        $msg->setDelayWhileIdle(false);
        $this->assertFalse($msg->getDelayWhileIdle());
    }

    /**
     * @expectedException Zend_Mobile_Push_Message_Exception
     */
    public function testDelayWhileIdleThrowsExceptionOnInvalidValue()
    {
        $msg = new Zend_Mobile_Push_Message_C2dm();
        $msg->setDelayWhileIdle('true');
    }

    public function testValidateWithoutTokenReturnsFalse()
    {
        $msg = new Zend_Mobile_Push_Message_C2dm();
        $msg->setId('collapseKey');
        $this->assertFalse($msg->validate());
    }

    public function testValidateWithoutIdReturnsFalse()
    {
        $msg = new Zend_Mobile_Push_Message_C2dm();
        $msg->setToken('a-token!');
        $this->assertFalse($msg->validate());
    }

    public function testValidateWithIdAndTokenReturnsTrue()
    {
        $msg = new Zend_Mobile_Push_Message_C2dm();
        $msg->setId('collapseKey');
        $msg->setToken('a-token!');
        $this->assertTrue($msg->validate());
    }

    public function testValidateWithIdAsIntAndTokenReturnsTrue()
    {
        $msg = new Zend_Mobile_Push_Message_C2dm();
        $msg->setId(time());
        $msg->setToken('da-token');
        $this->assertTrue($msg->validate());
    }
}
