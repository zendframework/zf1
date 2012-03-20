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

require_once 'Zend/Mobile/Push/Mpns.php';
require_once 'Zend/Mobile/Push/Message/Mpns/Raw.php';
require_once 'Zend/Mobile/Push/Message/Mpns/Tile.php';
require_once 'Zend/Mobile/Push/Message/Mpns/Toast.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * @category   Zend
 * @package    Zend_Mobile
 * @subpackage Push
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mobile
 * @group      Zend_Mobile_Push
 * @group      Zend_Mobile_Push_Mpns
 */
class Zend_Mobile_Push_MpnsTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->adapter = new Zend_Http_Client_Adapter_Test();
        $this->client = new Zend_Http_Client();
        $this->client->setAdapter($this->adapter);
        $this->mpns = new Zend_Mobile_Push_Mpns();
        $this->mpns->setHttpClient($this->client);
    }

    public function getMessage($type) {
        switch ($type) {
            case 'tile':
                $message = new Zend_Mobile_Push_Message_Mpns_Tile();
                break;
            case 'toast':
                $message = new Zend_Mobile_Push_Message_Mpns_Toast();
                break;
            default:
                $message = new Zend_Mobile_Push_Message_Mpns_Raw();
                $message->setMessage('<w><oa h="" /></w>');
        }
        $message->setToken('http://this.is.a.url.com');
        return $message;
    }

    public function testGetHttpClientReturnsDefault()
    {
        $mpns = new Zend_Mobile_Push_Mpns();
        $this->assertEquals('Zend_Http_Client', get_class($mpns->getHttpClient()));
        $this->assertTrue($mpns->getHttpClient() instanceof Zend_Http_Client);
    }

    public function testSetHttpClient()
    {
        $client = new Zend_Http_Client();
        $this->mpns->setHttpClient($client);
        $this->assertEquals($client, $this->mpns->getHttpClient());
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception
     */
    public function testSendThrowsExceptionWithNonValidMessage()
    {
        $msg = new Zend_Mobile_Push_Message_Mpns_Tile();
        $this->mpns->send($msg);
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_DeviceQuotaExceeded
     */
    public function testSendThrowsExceptionWhenDeviceQuotaExceeded()
    {
        $this->adapter->setResponse('HTTP/1.1 200 OK' . "\r\n" . 'NotificationStatus: QueueFull' . "\r\n\r\n");
        $this->mpns->send($this->getMessage('raw'));
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_InvalidPayload
     */
    public function testSendThrowsExceptionWhenInvalidPayload()
    {
        $this->adapter->setResponse('HTTP/1.1 400 Bad Request' . "\r\n\r\n");
        $this->mpns->send($this->getMessage('raw'));
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_InvalidToken
     */
    public function testSendThrowsExceptionWhenInvalidToken()
    {
        $this->adapter->setResponse('HTTP/1.1 401 Unauthorized' . "\r\n\r\n");
        $this->mpns->send($this->getMessage('raw'));
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_InvalidToken
     */
    public function testSendThrowsExceptionWhenDeviceNotRegistered()
    {
        $this->adapter->setResponse('HTTP/1.1 404 Not Found' . "\r\n\r\n");
        $this->mpns->send($this->getMessage('raw'));
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception
     */
    public function testSendThrowsExceptionWhenMethodNotPost()
    {
        $this->adapter->setResponse('HTTP/1.1 405 Method Not Allowed' . "\r\n\r\n");
        $this->mpns->send($this->getMessage('raw'));
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_QuotaExceeded
     */
    public function testSendThrowsExceptionWhenServiceQuotaExceeded()
    {
        $this->adapter->setResponse('HTTP/1.1 406 Not Acceptable');
        $this->mpns->send($this->getMessage('raw'));
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_InvalidToken
     */
    public function testSendThrowsExceptionWhenInvalidToken2()
    {
        $this->adapter->setResponse('HTTP/1.1 412 Precondition Failed' . "\r\n\r\n");
        $this->mpns->send($this->getMessage('raw'));
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_ServerUnavailable
     */
    public function testSendThrowsExceptionWhenServerUnavailable()
    {
        $this->adapter->setResponse('HTTP/1.1 503 Service Unavailable' . "\r\n\r\n");
        $this->mpns->send($this->getMessage('raw'));
    }

    public function testAllOk()
    {
        $this->adapter->setResponse('HTTP/1.1 200 OK' . "\r\n\r\n");
        $this->mpns->send($this->getMessage('raw'));

        $toast = $this->getMessage('toast');
        $toast->setTitle('Foo');
        $toast->setMessage('Bar');
        $this->mpns->send($toast);

        $tile = $this->getMessage('tile');
        $tile->setBackgroundImage('red.jpg');
        $tile->setCount(1);
        $tile->setTitle('Foo Bar');

        // other optional attributes for wp7.1+
        $tile->setTileId('/SomeAction.xaml');
        $tile->setBackBackgroundImage('blue.jpg');
        $tile->setBackTitle('Bar');
        $tile->setBackContent('Foo');
    }
}
