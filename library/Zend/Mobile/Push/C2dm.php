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
 * @subpackage Zend_Mobile_Push
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Http_Client **/
require_once 'Zend/Http/Client.php';

/** Zend_Mobile_Push_Abstract **/
require_once 'Zend/Mobile/Push/Abstract.php';

/** Zend_Mobile_Push_Message_C2dm **/
require_once 'Zend/Mobile/Push/Message/C2dm.php';

/**
 * C2DM Push
 *
 * @category   Zend
 * @package    Zend_Mobile
 * @subpackage Zend_Mobile_Push
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Mobile_Push_C2dm extends Zend_Mobile_Push_Abstract
{

    /**
     * @const string Server URI
     */
    const SERVER_URI = 'https://android.apis.google.com/c2dm/send';

    /**
     * @const string ClientLogin auth service name
     */
    const AUTH_SERVICE_NAME = 'ac2dm';

    /**
     * Http Client
     *
     * @var Client
     */
    protected $_httpClient;

    /**
     * Login Token
     *
     * @var string
     */
    protected $_loginToken;

    /**
     * Get Login Token
     *
     * @return string
     */
    public function getLoginToken()
    {
        return $this->_loginToken;
    }

    /**
     * Set Login Token
     *
     * @param  string $token
     * @return Zend_Mobile_Push_C2dm
     * @throws Zend_Mobile_Push_Exception
     */
    public function setLoginToken($token)
    {
        if (!is_string($token) || empty($token)) {
            throw new Zend_Mobile_Push_Exception('The login token must be a string and not empty');
        }
        $this->_loginToken = $token;
        return $this;
    }

    /**
     * Get Http Client
     *
     * @return Zend_Http_Client
     */
    public function getHttpClient()
    {
        if (!$this->_httpClient) {
            $this->_httpClient = new Zend_Http_Client();
            $this->_httpClient->setConfig(array(
                'strictredirects' => true,
            ));
        }
        return $this->_httpClient;
    }

    /**
     * Set Http Client
     *
     * @return Zend_Mobile_Push_C2dm
     */
    public function setHttpClient(Zend_Http_Client $client)
    {
        $this->_httpClient = $client;
        return $this;
    }

    /**
     * Send Message
     *
     * @param Zend_Mobile_Push_Message_C2dm $message
     * @return boolean
     * @throws Zend_Mobile_Push_Exception
     */
    public function send(Zend_Mobile_Push_Message_Abstract $message)
    {
        if (!$message->validate()) {
            throw new Zend_Mobile_Push_Exception('The message is not valid.');
        }

        $this->connect();

        $client = $this->getHttpClient();
        $client->setUri(self::SERVER_URI);
        $client->setHeaders('Authorization', 'GoogleLogin auth=' . $this->getLoginToken());
        $client->setParameterPost('delay_while_idle', (int) $message->getDelayWhileIdle());
        $client->setParameterPost('registration_id', $message->getToken());
        $client->setParameterPost('collapse_key', $message->getId());
        foreach ($message->getData() as $k => $v) {
            $client->setParameterPost('data.' . $k, $v);
        }
        $response = $client->request('POST');
        $this->close();


        switch ($response->getStatus())
        {
            case 500:
            case 503:
                require_once 'Zend/Mobile/Push/Exception/ServerUnavailable.php';
                throw new Zend_Mobile_Push_Exception_ServerUnavailable('The server was unavailable, check Retry-After header');
                break;
            case 401:
                require_once 'Zend/Mobile/Push/Exception/InvalidAuthToken.php';
                throw new Zend_Mobile_Push_Exception_InvalidAuthToken('The auth token is invalid');
                break;
            default:
                $body = $response->getBody();
                $body = preg_split('/=/', $body);
                if (!isset($body[0]) || !isset($body[1])) {
                    require_once 'Zend/Mobile/Push/Exception/ServerUnavailable.php';
                    throw new Zend_Mobile_Push_Exception_ServerUnavailable('The server gave us an invalid response, try again later');
                }
                if (strtolower($body[0]) == 'error') {
                    $err = strtolower($body[1]);
                    switch ($err) {
                        case 'quotaexceeded':
                            require_once 'Zend/Mobile/Push/Exception/QuotaExceeded.php';
                            throw new Zend_Mobile_Push_Exception_QuotaExceeded('Too many messages sent by the sender. Retry after a while.');
                            break;
                        case 'devicequotaexceeded':
                            require_once 'Zend/Mobile/Push/Exception/DeviceQuotaExceeded.php';
                            throw new Zend_Mobile_Push_Exception_DeviceQuotaExceeded('Too many messages sent by the sender to a specific device. Retry after a while.');
                            break;
                        case 'missingregistration':
                            require_once 'Zend/Mobile/Push/Exception/InvalidToken.php';
                            throw new Zend_Mobile_Push_Exception_InvalidToken('Missing token.  The message must always have a token.');
                            break;
                        case 'invalidregistration':
                            require_once 'Zend/Mobile/Push/Exception/InvalidToken.php';
                            throw new Zend_Mobile_Push_Exception_InvalidToken('Bad token.  Remove this token from being sent to again.');
                            break;
                        case 'mismatchsenderid':
                            require_once 'Zend/Mobile/Push/Exception/InvalidToken.php';
                            throw new Zend_Mobile_Push_Exception_InvalidToken('Bad token.  This token is not registered with the current login');
                            break;
                        case 'notregistered':
                            require_once 'Zend/Mobile/Push/Exception/InvalidToken.php';
                            throw new Zend_Mobile_Push_Exception_InvalidToken('Bad token.  This token is not registered.');
                            break;
                        case 'messagetoobig':
                            require_once 'Zend/Mobile/Push/Exception/InvalidPayload.php';
                            throw new Zend_Mobile_Push_Exception_InvalidPayload('The message is too big; reduce the size of the message.');
                            break;
                        case 'missingcollapsekey':
                            require_once 'Zend/Mobile/Push/Exception/InvalidTopic.php';
                            throw new Zend_Mobile_Push_Exception_InvalidTopic('The message id must be set; include one in the message.');
                            break;
                        default:
                            $err = strip_tags($body[1]);
                            throw new Zend_Mobile_Push_Exception(sprintf('An unknown error occurred: %s', $err));

                    }
                }
                break;
        }
        return true;
    }
}
