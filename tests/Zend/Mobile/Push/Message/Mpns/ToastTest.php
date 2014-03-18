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
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once 'Zend/Mobile/Push/Message/Mpns/Toast.php';

/**
 * @category   Zend
 * @package    Zend_Mobile
 * @subpackage Push
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mobile
 * @group      Zend_Mobile_Push
 * @group      Zend_Mobile_Push_Mpns
 */
class Zend_Mobile_Push_Message_Mpns_ToastTest extends PHPUnit_Framework_TestCase
{
    private $_msg;

    public function setUp()
    {
        $this->_msg = new Zend_Mobile_Push_Message_Mpns_Toast();
    }

    public function testSetToken()
    {
        $token = 'http://sn1.notify.live.net/throttledthirdparty/bogusdata';
        $this->_msg->setToken($token);
        $this->assertEquals($token, $this->_msg->getToken());
    }

    /**
     * @expectedException Zend_Mobile_Push_Message_Exception
     */
    public function testSetTokenNonStringThrowsException()
    {
        $token = array('foo' => 'bar');
        $this->_msg->setToken($token);
    }

    /**
     * @expectedException Zend_Mobile_Push_Message_Exception
     */
    public function testSetTokenInvalidUrlThrowsException()
    {
        $token = 'notaurl';
        $this->_msg->setToken($token);
    }

    public function testGetNotificationType()
    {
        $this->assertEquals(Zend_Mobile_Push_Message_Mpns::TYPE_TOAST, $this->_msg->getNotificationType());
    }

    public function testSetTitle()
    {
        $title = 'foo';
        $this->_msg->setTitle($title);
        $this->assertEquals($title, $this->_msg->getTitle());
    }

    /**
     * @expectedException Zend_Mobile_Push_Message_Exception
     */
    public function testSetTitleThrowsExceptionOnNonString()
    {
        $title = array('foo' => 'bar');
        $this->_msg->setTitle($title);
    }

    public function testSetMessage()
    {
        $msg = 'foo';
        $this->_msg->setMessage($msg);
        $this->assertEquals($msg, $this->_msg->getMessage());
    }

    /**
     * @expectedException Zend_Mobile_Push_Message_Exception
     */
    public function testSetMessageThrowsExceptionOnNonString()
    {
        $msg = array('foo' => 'bar');
        $this->_msg->setMessage($msg);
    }

    public function testSetParams()
    {
        $params = '?foo=bar';
        $this->_msg->setParams($params);
        $this->assertEquals($params, $this->_msg->getParams());
    }

    /**
     * @expectedException Zend_Mobile_Push_Message_Exception
     */
    public function testSetParamsThrowsExceptionOnNonString()
    {
        $params = array('foo' => 'bar');
        $this->_msg->setParams($params);
    }

    public function testGetDelayHasDefaultOfImmediate()
    {
        $this->assertEquals(Zend_Mobile_Push_Message_Mpns_Toast::DELAY_IMMEDIATE, $this->_msg->getDelay());
    }

    public function testSetDelay()
    {
        $this->_msg->setDelay(Zend_Mobile_Push_Message_Mpns_Toast::DELAY_450S);
        $this->assertEquals(Zend_Mobile_Push_Message_Mpns_Toast::DELAY_450S, $this->_msg->getDelay());
        $this->_msg->setDelay(Zend_Mobile_Push_Message_Mpns_Toast::DELAY_900S);
        $this->assertEquals(Zend_Mobile_Push_Message_Mpns_Toast::DELAY_900S, $this->_msg->getDelay());
        $this->_msg->setDelay(Zend_Mobile_Push_Message_Mpns_Toast::DELAY_IMMEDIATE);
        $this->assertEquals(Zend_Mobile_Push_Message_Mpns_Toast::DELAY_IMMEDIATE, $this->_msg->getDelay());
    }

    public function testValidate()
    {
        $this->assertFalse($this->_msg->validate());
        $this->_msg->setToken('http://sn1.notify.live.net/throttledthirdparty/bogusdata');
        $this->assertFalse($this->_msg->validate());
        $this->_msg->setTitle('foo');
        $this->assertFalse($this->_msg->validate());
        $this->_msg->setMessage('bar');
        $this->assertTrue($this->_msg->validate());
    }

    public function testGetXmlPayload()
    {
        $title = 'Foo';
        $message = 'Bar';
        $this->_msg->setToken('http://sn1.notify.live.net/throttledthirdparty/abcdef1234567890');
        $this->_msg->setTitle($title);
        $this->_msg->setMessage($message);

        $xml = new SimpleXMLElement($this->_msg->getXmlPayload(), 0, false, 'wp', true);

        $this->assertEquals($title, (string) $xml->Toast->Text1);
        $this->assertEquals($message, (string) $xml->Toast->Text2);
    }
}
