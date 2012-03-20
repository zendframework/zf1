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

require_once 'Zend/Mobile/Push/C2dm.php';
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
 * @group      Zend_Mobile_Push_C2dm
 */
class Zend_Mobile_Push_C2dmTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->adapter = new Zend_Http_Client_Adapter_Test();
        $this->client = new Zend_Http_Client();
        $this->client->setAdapter($this->adapter);
        $this->c2dm = new Zend_Mobile_Push_C2dm();
        $this->c2dm->setLoginToken('testing');
        $this->c2dm->setHttpClient($this->client);
        $this->message = new Zend_Mobile_Push_Message_C2dm();
        $this->message->setId('testing');
        $this->message->setToken('testing');
        $this->message->addData('testKey', 'testValue');
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception
     */
    public function testSetLoginTokenThrowsExceptionOnNonString()
    {
        $this->c2dm->setLoginToken(array());
    }

    public function testSetLoginToken()
    {
        $loginToken = 'a-login-token';
        $this->c2dm->setLoginToken($loginToken);
        $this->assertEquals($loginToken, $this->c2dm->getLoginToken());
    }

    public function testGetHttpClientReturnsDefault()
    {
        $c2dm = new Zend_Mobile_Push_C2dm();
        $this->assertEquals('Zend_Http_Client', get_class($c2dm->getHttpClient()));
        $this->assertTrue($c2dm->getHttpClient() instanceof Zend_Http_Client);
    }

    public function testSetHttpClient()
    {
        $client = new Zend_Http_Client();
        $this->c2dm->setHttpClient($client);
        $this->assertEquals($client, $this->c2dm->getHttpClient());
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception
     */
    public function testSendThrowsExceptionWithNonValidMessage()
    {
        $msg = new Zend_Mobile_Push_Message_C2dm();
        $this->c2dm->send($msg);
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_ServerUnavailable
     */
    public function testSendThrowsExceptionWhenServerUnavailable()
    {
        $this->adapter->setResponse('HTTP/1.1 500 Internal Server Error' . "\r\n\r\n");
        $this->c2dm->send($this->message);
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_InvalidAuthToken
     */
    public function testSendThrowsExceptionWhenInvalidAuthToken()
    {
        $this->adapter->setResponse('HTTP/1.1 401 Unauthorized' . "\r\n\r\n");
        $this->c2dm->send($this->message);
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_QuotaExceeded
     */
    public function testSendThrowsExceptionWhenQuotaExceeded()
    {
        $this->adapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" .
            'Error=QuotaExceeded'
        );
        $this->c2dm->send($this->message);
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_DeviceQuotaExceeded
     */
    public function testSendThrowsExceptionWhenDeviceQuotaExceeded()
    {
        $this->adapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" .
            'Error=DeviceQuotaExceeded'
        );
        $this->c2dm->send($this->message);
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_InvalidToken
     */
    public function testSendThrowsExceptionWhenMissingRegistration()
    {
        $this->adapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" .
            'Error=MissingRegistration'
        );
        $this->c2dm->send($this->message);
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_InvalidToken
     */
    public function testSendThrowsExceptionWhenInvalidRegistration()
    {
        $this->adapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" .
            'Error=InvalidRegistration'
        );
        $this->c2dm->send($this->message);
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_InvalidToken
     */
    public function testSendThrowsExceptionWhenMismatchSenderId()
    {
        $this->adapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" .
            'Error=MismatchSenderId'
        );
        $this->c2dm->send($this->message);
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_InvalidToken
     */
    public function testSendThrowsExceptionWhenNotRegistered()
    {
        $this->adapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" .
            'Error=NotRegistered'
        );
        $this->c2dm->send($this->message);
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_InvalidPayload
     */
    public function testSendThrowsExceptionWhenMessageTooBig()
    {
        $this->adapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" . 
            'Error=MessageTooBig'
        );
        $this->c2dm->send($this->message);
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception_InvalidTopic
     */
    public function testSendThrowsExceptionWhenMissingCollapseKey()
    {
        $this->adapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" .
            'Error=MissingCollapseKey'
        );
        $this->c2dm->send($this->message);
    }

    /**
     * @expectedException Zend_Mobile_Push_Exception
     */
    public function testSendThrowsExceptionWhenUnknownError()
    {
        $this->adapter->setResponse(
            'HTTP/1.1 200 OK' . "\r\n" .
            'Context-Type: text/html' . "\r\n\r\n" .
            'Error=somethinghappened'
        );
        $this->c2dm->send($this->message);
    }
}
