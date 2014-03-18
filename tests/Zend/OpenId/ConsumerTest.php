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
 * @package    Zend_OpenId
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_OpenId
 */
require_once 'Zend/OpenId/Consumer.php';

/**
 * Zend_OpenId_ResponseHelper
 */
require_once 'Zend/OpenId/ResponseHelper.php';

/**
 * Zend_OpenId_Consumer_Storage_File
 */
require_once 'Zend/OpenId/Consumer/Storage/File.php';

/**
 * Zend_OpenId_Extensions_Sreg
 */
require_once 'Zend/OpenId/Extension/Sreg.php';

/**
 * Zend_Http_Client_Adapter_Test
 */
require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_OpenId
 */
class Zend_OpenId_ConsumerTest extends PHPUnit_Framework_TestCase
{
    const ID       = "http://id.myopenid.com/";
    const REAL_ID  = "http://real_id.myopenid.com/";
    const SERVER   = "http://www.myopenid.com/";

    const HANDLE   = "d41d8cd98f00b204e9800998ecf8427e";
    const MAC_FUNC = "sha256";
    const SECRET   = "4fa03202081808bd19f92b667a291873";

    /**
     * testing login
     *
     */
    public function testLogin()
    {
        $expiresIn = time() + 600;

        $_SERVER['SCRIPT_URI'] = "http://www.zf-test.com/test.php";
        $storage = new Zend_OpenId_Consumer_Storage_File(dirname(__FILE__)."/_files/consumer");
        $storage->delDiscoveryInfo(self::ID);
        $this->assertTrue( $storage->addDiscoveryInfo(self::ID, self::REAL_ID, self::SERVER, 1.1, $expiresIn) );
        $storage->delAssociation(self::SERVER);
        $this->assertTrue( $storage->addAssociation(self::SERVER, self::HANDLE, self::MAC_FUNC, self::SECRET, $expiresIn) );

        $response = new Zend_OpenId_ResponseHelper(true);
        $consumer = new Zend_OpenId_Consumer($storage);
        $this->assertTrue( $consumer->login(self::ID, null, null, null, $response) );
        $headers = $response->getHeaders();
        $this->assertSame( '', $response->getBody() );
        $this->assertTrue( is_array($headers) );
        $this->assertSame( 1, count($headers) );
        $this->assertTrue( is_array($headers[0]) );
        $this->assertSame( 3, count($headers[0]) );
        $this->assertSame( 'Location', $headers[0]['name'] );
        $this->assertSame( true, $headers[0]['replace'] );
        $url = $headers[0]['value'];
        $url = parse_url($url);
        $this->assertSame( "http", $url['scheme'] );
        $this->assertSame( "www.myopenid.com", $url['host'] );
        $this->assertSame( "/", $url['path'] );
        $q = explode("&", $url['query']);
        $query = array();
        foreach($q as $var) {
            if (list($key, $val) = explode("=", $var, 2)) {
                $query[$key] = $val;
            }
        }
        $this->assertTrue( is_array($query) );
        $this->assertSame( 6, count($query) );
        $this->assertSame( 'checkid_setup', $query['openid.mode'] );
        $this->assertSame( 'http%3A%2F%2Freal_id.myopenid.com%2F', $query['openid.identity'] );
        $this->assertSame( 'http%3A%2F%2Fid.myopenid.com%2F', $query['openid.claimed_id'] );
        $this->assertSame( self::HANDLE, $query['openid.assoc_handle'] );
        $this->assertSame( 'http%3A%2F%2Fwww.zf-test.com%2Ftest.php', $query['openid.return_to'] );
        $this->assertSame( 'http%3A%2F%2Fwww.zf-test.com', $query['openid.trust_root'] );

        // Test user defined return_to and trust_root
        $response = new Zend_OpenId_ResponseHelper(true);
        $consumer = new Zend_OpenId_Consumer($storage);
        $this->assertTrue( $consumer->login(self::ID, "http://www.zf-test.com/return.php", "http://www.zf-test.com/trust.php", null, $response) );
        $headers = $response->getHeaders();
        $url = $headers[0]['value'];
        $url = parse_url($url);
        $q = explode("&", $url['query']);
        $query = array();
        foreach($q as $var) {
            if (list($key, $val) = explode("=", $var, 2)) {
                $query[$key] = $val;
            }
        }
        $this->assertTrue( is_array($query) );
        $this->assertSame( 6, count($query) );
        $this->assertSame( 'checkid_setup', $query['openid.mode'] );
        $this->assertSame( 'http%3A%2F%2Freal_id.myopenid.com%2F', $query['openid.identity'] );
        $this->assertSame( 'http%3A%2F%2Fid.myopenid.com%2F', $query['openid.claimed_id'] );
        $this->assertSame( self::HANDLE, $query['openid.assoc_handle'] );
        $this->assertSame( 'http%3A%2F%2Fwww.zf-test.com%2Freturn.php', $query['openid.return_to'] );
        $this->assertSame( 'http%3A%2F%2Fwww.zf-test.com%2Ftrust.php', $query['openid.trust_root'] );

        $storage->delDiscoveryInfo(self::ID);
        $this->assertTrue( $storage->addDiscoveryInfo(self::ID, self::REAL_ID, self::SERVER, 2.0, $expiresIn) );

        // Test login with OpenID 2.0
        $response = new Zend_OpenId_ResponseHelper(true);
        $consumer = new Zend_OpenId_Consumer($storage);
        $this->assertTrue( $consumer->login(self::ID, "http://www.zf-test.com/return.php", "http://www.zf-test.com/trust.php", null, $response) );
        $headers = $response->getHeaders();
        $url = $headers[0]['value'];
        $url = parse_url($url);
        $q = explode("&", $url['query']);
        $query = array();
        foreach($q as $var) {
            if (list($key, $val) = explode("=", $var, 2)) {
                $query[$key] = $val;
            }
        }
        $this->assertTrue( is_array($query) );
        $this->assertSame( 7, count($query) );
        $this->assertSame( 'http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0', $query['openid.ns'] );
        $this->assertSame( 'checkid_setup', $query['openid.mode'] );
        $this->assertSame( 'http%3A%2F%2Freal_id.myopenid.com%2F', $query['openid.identity'] );
        $this->assertSame( 'http%3A%2F%2Fid.myopenid.com%2F', $query['openid.claimed_id'] );
        $this->assertSame( self::HANDLE, $query['openid.assoc_handle'] );
        $this->assertSame( 'http%3A%2F%2Fwww.zf-test.com%2Freturn.php', $query['openid.return_to'] );
        $this->assertSame( 'http%3A%2F%2Fwww.zf-test.com%2Ftrust.php', $query['openid.realm'] );

        // Test login with SREG extension
        $ext = new Zend_OpenId_Extension_Sreg(array("nickname"=>true,"email"=>false));
        $response = new Zend_OpenId_ResponseHelper(true);
        $consumer = new Zend_OpenId_Consumer($storage);
        $this->assertTrue( $consumer->login(self::ID, "http://www.zf-test.com/return.php", "http://www.zf-test.com/trust.php", $ext, $response) );
        $headers = $response->getHeaders();
        $url = $headers[0]['value'];
        $url = parse_url($url);
        $q = explode("&", $url['query']);
        $query = array();
        foreach($q as $var) {
            if (list($key, $val) = explode("=", $var, 2)) {
                $query[$key] = $val;
            }
        }
        $this->assertTrue( is_array($query) );
        $this->assertSame( 9, count($query) );
        $this->assertSame( 'http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0', $query['openid.ns'] );
        $this->assertSame( 'checkid_setup', $query['openid.mode'] );
        $this->assertSame( 'http%3A%2F%2Freal_id.myopenid.com%2F', $query['openid.identity'] );
        $this->assertSame( 'http%3A%2F%2Fid.myopenid.com%2F', $query['openid.claimed_id'] );
        $this->assertSame( self::HANDLE, $query['openid.assoc_handle'] );
        $this->assertSame( 'http%3A%2F%2Fwww.zf-test.com%2Freturn.php', $query['openid.return_to'] );
        $this->assertSame( 'http%3A%2F%2Fwww.zf-test.com%2Ftrust.php', $query['openid.realm'] );
        $this->assertSame( 'nickname', $query['openid.sreg.required'] );
        $this->assertSame( 'email', $query['openid.sreg.optional'] );

        // Test login in dumb mode
        $storage->delAssociation(self::SERVER);
        $response = new Zend_OpenId_ResponseHelper(true);
        $consumer = new Zend_OpenId_Consumer($storage, true);
        $this->assertTrue( $consumer->login(self::ID, "http://www.zf-test.com/return.php", "http://www.zf-test.com/trust.php", null, $response) );
        $headers = $response->getHeaders();
        $url = $headers[0]['value'];
        $url = parse_url($url);
        $q = explode("&", $url['query']);
        $query = array();
        foreach($q as $var) {
            if (list($key, $val) = explode("=", $var, 2)) {
                $query[$key] = $val;
            }
        }
        $this->assertTrue( is_array($query) );
        $this->assertSame( 6, count($query) );
        $this->assertSame( 'http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0', $query['openid.ns'] );
        $this->assertSame( 'checkid_setup', $query['openid.mode'] );
        $this->assertSame( 'http%3A%2F%2Freal_id.myopenid.com%2F', $query['openid.identity'] );
        $this->assertSame( 'http%3A%2F%2Fid.myopenid.com%2F', $query['openid.claimed_id'] );
        $this->assertSame( 'http%3A%2F%2Fwww.zf-test.com%2Freturn.php', $query['openid.return_to'] );
        $this->assertSame( 'http%3A%2F%2Fwww.zf-test.com%2Ftrust.php', $query['openid.realm'] );

        $storage->delDiscoveryInfo(self::ID);
    }

    /**
     * testing check
     *
     */
    public function testCheck()
    {
        $expiresIn = time() + 600;

        $_SERVER['SCRIPT_URI'] = "http://www.zf-test.com/test.php";
        $storage = new Zend_OpenId_Consumer_Storage_File(dirname(__FILE__)."/_files/consumer");
        $storage->delDiscoveryInfo(self::ID);
        $this->assertTrue( $storage->addDiscoveryInfo(self::ID, self::REAL_ID, self::SERVER, 1.1, $expiresIn) );
        $storage->delAssociation(self::SERVER);
        $this->assertTrue( $storage->addAssociation(self::SERVER, self::HANDLE, self::MAC_FUNC, self::SECRET, $expiresIn) );

        $response = new Zend_OpenId_ResponseHelper(true);
        $consumer = new Zend_OpenId_Consumer($storage);
        $this->assertTrue( $consumer->check(self::ID, null, null, null, $response) );
        $headers = $response->getHeaders();
        $this->assertSame( '', $response->getBody() );
        $this->assertTrue( is_array($headers) );
        $this->assertSame( 1, count($headers) );
        $this->assertTrue( is_array($headers[0]) );
        $this->assertSame( 3, count($headers[0]) );
        $this->assertSame( 'Location', $headers[0]['name'] );
        $this->assertSame( true, $headers[0]['replace'] );
        $url = $headers[0]['value'];
        $url = parse_url($url);
        $this->assertSame( "http", $url['scheme'] );
        $this->assertSame( "www.myopenid.com", $url['host'] );
        $this->assertSame( "/", $url['path'] );
        $q = explode("&", $url['query']);
        $query = array();
        foreach($q as $var) {
            if (list($key, $val) = explode("=", $var, 2)) {
                $query[$key] = $val;
            }
        }
        $this->assertTrue( is_array($query) );
        $this->assertSame( 6, count($query) );
        $this->assertSame( 'checkid_immediate', $query['openid.mode'] );
        $this->assertSame( 'http%3A%2F%2Freal_id.myopenid.com%2F', $query['openid.identity'] );
        $this->assertSame( 'http%3A%2F%2Fid.myopenid.com%2F', $query['openid.claimed_id'] );
        $this->assertSame( self::HANDLE, $query['openid.assoc_handle'] );
        $this->assertSame( 'http%3A%2F%2Fwww.zf-test.com%2Ftest.php', $query['openid.return_to'] );
        $this->assertSame( 'http%3A%2F%2Fwww.zf-test.com', $query['openid.trust_root'] );

        $storage->delDiscoveryInfo(self::ID);
        $storage->delAssociation(self::SERVER);
    }

    /**
     * testing _getAssociation
     *
     */
    public function testGetAssociation()
    {
        $expiresIn = time() + 600;

        $storage = new Zend_OpenId_Consumer_Storage_File(dirname(__FILE__)."/_files/consumer");
        $storage->delAssociation(self::SERVER);
        $consumer = new Zend_OpenId_ConsumerHelper($storage);
        $this->assertFalse( $consumer->getAssociation(self::SERVER, $handle, $macFunc, $secret, $expires) );
        $this->assertTrue( $storage->addAssociation(self::SERVER, self::HANDLE, self::MAC_FUNC, self::SECRET, $expiresIn) );
        $this->assertTrue( $consumer->getAssociation(self::SERVER, $handle, $macFunc, $secret, $expires) );
        $this->assertSame( self::HANDLE, $handle );
        $this->assertSame( self::MAC_FUNC, $macFunc );
        $this->assertSame( self::SECRET, $secret );
        $this->assertSame( $expiresIn, $expires );
        $storage->delAssociation(self::SERVER);
        $this->assertTrue( $consumer->getAssociation(self::SERVER, $handle, $macFunc, $secret, $expires) );
        $this->assertSame( self::HANDLE, $handle );
        $this->assertSame( self::MAC_FUNC, $macFunc );
        $this->assertSame( self::SECRET, $secret );
        $this->assertSame( $expiresIn, $expires );
    }

    /**
     * testing _httpRequest
     *
     */
    public function testHttpRequest()
    {
        $consumer = new Zend_OpenId_ConsumerHelper(new Zend_OpenId_Consumer_Storage_File(dirname(__FILE__)."/_files/consumer"));
        $http = new Zend_Http_Client(null,
            array(
                'maxredirects' => 4,
                'timeout'      => 15,
                'useragent'    => 'Zend_OpenId'
            ));
        $test = new Zend_Http_Client_Adapter_Test();
        $http->setAdapter($test);
        $consumer->SetHttpClient($http);
        $this->assertSame( $http, $consumer->GetHttpClient() );
        $this->assertFalse( $consumer->httpRequest(self::SERVER) );

        $test->setResponse("HTTP/1.1 200 OK\r\n\r\nok\n");

        // Test GET request without parameters
        $this->assertSame( "ok\n", $consumer->httpRequest(self::SERVER) );
        $this->assertSame( "GET / HTTP/1.1\r\n" .
                           "Host: www.myopenid.com\r\n" .
                           "Connection: close\r\n" .
                           "Accept-encoding: gzip, deflate\r\n" .
                           "User-Agent: Zend_OpenId\r\n\r\n",
                           $http->getLastRequest() );

        // Test POST request without parameters
        $this->assertSame( "ok\n", $consumer->httpRequest(self::SERVER, 'POST') );
        $this->assertSame( "POST / HTTP/1.1\r\n" .
                           "Host: www.myopenid.com\r\n" .
                           "Connection: close\r\n" .
                           "Accept-encoding: gzip, deflate\r\n" .
                           "Content-Type: application/x-www-form-urlencoded\r\n" .
                           "User-Agent: Zend_OpenId\r\n" .
                           "Content-Length: 0\r\n\r\n",
                           $http->getLastRequest() );

        // Test GET request with parameters
        $this->assertSame( "ok\n", $consumer->httpRequest(self::SERVER . 'test.php', 'GET', array('a'=>'b','c'=>'d')) );
        $this->assertSame( "GET /test.php?a=b&c=d HTTP/1.1\r\n" .
                           "Host: www.myopenid.com\r\n" .
                           "Connection: close\r\n" .
                           "Accept-encoding: gzip, deflate\r\n" .
                           "User-Agent: Zend_OpenId\r\n\r\n",
                           $http->getLastRequest() );

        // Test POST request with parameters
        $this->assertSame( "ok\n", $consumer->httpRequest(self::SERVER . 'test.php', 'POST', array('a'=>'b','c'=>'d')) );
        $this->assertSame( "POST /test.php HTTP/1.1\r\n" .
                           "Host: www.myopenid.com\r\n" .
                           "Connection: close\r\n" .
                           "Accept-encoding: gzip, deflate\r\n" .
                           "User-Agent: Zend_OpenId\r\n" .
                           "Content-Type: application/x-www-form-urlencoded\r\n" .
                           "Content-Length: 7\r\n\r\n" .
                           "a=b&c=d",
                           $http->getLastRequest() );

        // Test GET parameters combination
        $this->assertSame( "ok\n", $consumer->httpRequest(self::SERVER . 'test.php?a=b', 'GET', array('c'=>'x y')) );
        $this->assertSame( "GET /test.php?a=b&c=x+y HTTP/1.1\r\n" .
                           "Host: www.myopenid.com\r\n" .
                           "Connection: close\r\n" .
                           "Accept-encoding: gzip, deflate\r\n" .
                           "User-Agent: Zend_OpenId\r\n\r\n",
                           $http->getLastRequest() );

        // Test GET and POST parameters combination
        $this->assertSame( "ok\n", $consumer->httpRequest(self::SERVER . 'test.php?a=b', 'POST', array('c'=>'x y')) );
        $this->assertSame( "POST /test.php?a=b HTTP/1.1\r\n" .
                           "Host: www.myopenid.com\r\n" .
                           "Connection: close\r\n" .
                           "Accept-encoding: gzip, deflate\r\n" .
                           "User-Agent: Zend_OpenId\r\n" .
                           "Content-Type: application/x-www-form-urlencoded\r\n" .
                           "Content-Length: 5\r\n\r\n" .
                           "c=x+y",
                           $http->getLastRequest() );
    }

    /**
     * testing _associate
     *
     */
    public function testAssociate()
    {
        try {
            $storage = new Zend_OpenId_Consumer_Storage_File(dirname(__FILE__)."/_files/consumer");
            $storage->delAssociation(self::SERVER);
            $consumer = new Zend_OpenId_ConsumerHelper($storage);
            $http = new Zend_Http_Client(null,
                array(
                    'maxredirects' => 4,
                    'timeout'      => 15,
                    'useragent'    => 'Zend_OpenId'
                ));
            $test = new Zend_Http_Client_Adapter_Test();
            $http->setAdapter($test);
            $consumer->SetHttpClient($http);

            // Test OpenID 1.1 association request with DH-SHA1
            $consumer->clearAssociation();
            $this->assertFalse( $consumer->associate(self::SERVER, 1.1, pack("H*", "60017f7ebf0ef29ace27f0dfee2aaa6528d170e147b1260cc3987d7851cb67d49fbfdbb42c56494e61b1e1e39fa42315db0bf4f879787fcf1e807d0629d47cf05d3ac50602b1e7f6e73cd370320ddcdcf7f7aa86f35a3273d187de9c9efa959a02ce3a9c80f47dfcc83cfaad60b673e1806a764227344deae158ceec9ca4d60e")) );
            $this->assertSame( "POST / HTTP/1.1\r\n" .
                               "Host: www.myopenid.com\r\n" .
                               "Connection: close\r\n" .
                               "Accept-encoding: gzip, deflate\r\n" .
                               "User-Agent: Zend_OpenId\r\n" .
                               "Content-Type: application/x-www-form-urlencoded\r\n" .
                               "Content-Length: 510\r\n\r\n" .
                               "openid.mode=associate&" .
                               "openid.assoc_type=HMAC-SHA1&" .
                               "openid.session_type=DH-SHA1&".
                               "openid.dh_modulus=ANz5OguIOXLsDhmYmsWizjEOHTdxfo2Vcbt2I3MYZuYe91ouJ4mLBX%2BYkcLiemOcPym2CBRYHNOyyjmG0mg3BVd9RcLn5S3IHHoXGHblzqdLFEi%2F368Ygo79JRnxTkXjgmY0rxlJ5bU1zIKaSDuKdiI%2BXUkKJX8Fvf8W8vsixYOr&" .
                               "openid.dh_gen=Ag%3D%3D&" .
                               "openid.dh_consumer_public=GaLlROlBGgSopPzo1ewYISnnT4BUFBfIKlgDPoS9U41t5eQb8QYqgcw7%2BW3dSF1VlWcvJGR0UbZIEhJ3UrCs6p69q6sgl%2FOZ7P%2B17rme7OynqszA3pqD6MJoQVZ5Ht%2FR%2BjmMjK08ajcgYEZU1GG4U5k8eYbcFnje00%2FTGfjKY0I%3D",
                               $http->getLastRequest() );

            // Test OpenID 2.0 association request with DH-SHA256
            $consumer->clearAssociation();
            $this->assertFalse( $consumer->associate(self::SERVER, 2.0, pack("H*", "60017f7ebf0ef29ace27f0dfee2aaa6528d170e147b1260cc3987d7851cb67d49fbfdbb42c56494e61b1e1e39fa42315db0bf4f879787fcf1e807d0629d47cf05d3ac50602b1e7f6e73cd370320ddcdcf7f7aa86f35a3273d187de9c9efa959a02ce3a9c80f47dfcc83cfaad60b673e1806a764227344deae158ceec9ca4d60e")) );
            $this->assertSame( "POST / HTTP/1.1\r\n" .
                               "Host: www.myopenid.com\r\n" .
                               "Connection: close\r\n" .
                               "Accept-encoding: gzip, deflate\r\n" .
                               "User-Agent: Zend_OpenId\r\n" .
                               "Content-Type: application/x-www-form-urlencoded\r\n" .
                               "Content-Length: 567\r\n\r\n" .
                               "openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0&" .
                               "openid.mode=associate&" .
                               "openid.assoc_type=HMAC-SHA256&" .
                               "openid.session_type=DH-SHA256&".
                               "openid.dh_modulus=ANz5OguIOXLsDhmYmsWizjEOHTdxfo2Vcbt2I3MYZuYe91ouJ4mLBX%2BYkcLiemOcPym2CBRYHNOyyjmG0mg3BVd9RcLn5S3IHHoXGHblzqdLFEi%2F368Ygo79JRnxTkXjgmY0rxlJ5bU1zIKaSDuKdiI%2BXUkKJX8Fvf8W8vsixYOr&" .
                               "openid.dh_gen=Ag%3D%3D&" .
                               "openid.dh_consumer_public=GaLlROlBGgSopPzo1ewYISnnT4BUFBfIKlgDPoS9U41t5eQb8QYqgcw7%2BW3dSF1VlWcvJGR0UbZIEhJ3UrCs6p69q6sgl%2FOZ7P%2B17rme7OynqszA3pqD6MJoQVZ5Ht%2FR%2BjmMjK08ajcgYEZU1GG4U5k8eYbcFnje00%2FTGfjKY0I%3D",
                               $http->getLastRequest() );

            // Test OpenID 1.1 association response with DH-SHA1
            $consumer->clearAssociation();
            $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                               "assoc_type:HMAC-SHA1\n" .
                               "assoc_handle:0123456789absdef0123456789absdef\n" .
                               "expires_in:3600\n" .
                               "session_type:DH-SHA1\n".
                               "dh_server_public:AIoP3d+ZTkd5vZj6G82XVIQ6KRAfSKmLz2Q3qVMzZ5tt7Z7St714GccipYXzCs5Tzgkc+Nt/uDE5xQ/f0Zn0uDS65CZHx3MOPqAANw/9YC/CafF1CD1MxW5TiN50GsjT/wGkcJFcpPXYVigQDOjIkHjKCysk53ktFvCoT60nFKGc\n".
                               "enc_mac_key:ON+M6/X8uUcOfxw1HF4sw/0XYyw=\n");
            $this->assertTrue( $consumer->associate(self::SERVER, 1.1, pack("H*", "60017f7ebf0ef29ace27f0dfee2aaa6528d170e147b1260cc3987d7851cb67d49fbfdbb42c56494e61b1e1e39fa42315db0bf4f879787fcf1e807d0629d47cf05d3ac50602b1e7f6e73cd370320ddcdcf7f7aa86f35a3273d187de9c9efa959a02ce3a9c80f47dfcc83cfaad60b673e1806a764227344deae158ceec9ca4d60e")) );
            $this->assertTrue( $storage->getAssociation(self::SERVER, $handle, $macFunc, $secret, $expires) );
            $this->assertSame( "0123456789absdef0123456789absdef", $handle );
            $this->assertSame( "sha1", $macFunc );
            $this->assertSame( "e36624c686748f6b646648f12748ffd157e4d4dd", bin2hex($secret) );
            $this->assertTrue( $storage->delAssociation(self::SERVER) );

            // Wrong OpenID 2.0 association response (wrong ns)
            $consumer->clearAssociation();
            $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                               "ns:http://specs.openid.net/auth/1.0\n" .
                               "assoc_type:HMAC-SHA256\n" .
                               "assoc_handle:0123456789absdef0123456789absdef\n" .
                               "expires_in:3600\n" .
                               "session_type:DH-SHA256\n".
                               "dh_server_public:AIlflxF8rvxx1Xi4Oj/KdP+7fvczeIRvx8WScMQS9I27R6YKd3Nx++5tAAF0rHelKDSG2ZeFM/zLEu9ZmUFzF02OaehWqykCfmtLASwMZO0u2GwYiIu5BoeJb9HlXJes58u/M4ViPXWhn27w2ZTlZJuuK8sDiTSTj9TmFxOriH4X\n".
                               "enc_mac_key:lvvCoTyvKy8oV6wnNHeroU0uLgBHiGV4BNkrXJe04JE=\n");
            $this->assertFalse( $consumer->associate(self::SERVER, 2.0, pack("H*", "60017f7ebf0ef29ace27f0dfee2aaa6528d170e147b1260cc3987d7851cb67d49fbfdbb42c56494e61b1e1e39fa42315db0bf4f879787fcf1e807d0629d47cf05d3ac50602b1e7f6e73cd370320ddcdcf7f7aa86f35a3273d187de9c9efa959a02ce3a9c80f47dfcc83cfaad60b673e1806a764227344deae158ceec9ca4d60e")) );

            // Wrong OpenID 2.0 association response (wrong assoc_type)
            $consumer->clearAssociation();
            $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                               "ns:http://specs.openid.net/auth/2.0\n" .
                               "assoc_type:HMAC-SHA1\n" .
                               "assoc_handle:0123456789absdef0123456789absdef\n" .
                               "expires_in:3600\n" .
                               "session_type:DH-SHA256\n".
                               "dh_server_public:AIlflxF8rvxx1Xi4Oj/KdP+7fvczeIRvx8WScMQS9I27R6YKd3Nx++5tAAF0rHelKDSG2ZeFM/zLEu9ZmUFzF02OaehWqykCfmtLASwMZO0u2GwYiIu5BoeJb9HlXJes58u/M4ViPXWhn27w2ZTlZJuuK8sDiTSTj9TmFxOriH4X\n".
                               "enc_mac_key:lvvCoTyvKy8oV6wnNHeroU0uLgBHiGV4BNkrXJe04JE=\n");
            $this->assertFalse( $consumer->associate(self::SERVER, 2.0, pack("H*", "60017f7ebf0ef29ace27f0dfee2aaa6528d170e147b1260cc3987d7851cb67d49fbfdbb42c56494e61b1e1e39fa42315db0bf4f879787fcf1e807d0629d47cf05d3ac50602b1e7f6e73cd370320ddcdcf7f7aa86f35a3273d187de9c9efa959a02ce3a9c80f47dfcc83cfaad60b673e1806a764227344deae158ceec9ca4d60e")) );

            // Wrong OpenID 2.0 association response (wrong session_type)
            $consumer->clearAssociation();
            $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                               "ns:http://specs.openid.net/auth/2.0\n" .
                               "assoc_type:HMAC-SHA256\n" .
                               "assoc_handle:0123456789absdef0123456789absdef\n" .
                               "expires_in:3600\n" .
                               "session_type:DH-SHA257\n".
                               "dh_server_public:AIlflxF8rvxx1Xi4Oj/KdP+7fvczeIRvx8WScMQS9I27R6YKd3Nx++5tAAF0rHelKDSG2ZeFM/zLEu9ZmUFzF02OaehWqykCfmtLASwMZO0u2GwYiIu5BoeJb9HlXJes58u/M4ViPXWhn27w2ZTlZJuuK8sDiTSTj9TmFxOriH4X\n".
                               "enc_mac_key:lvvCoTyvKy8oV6wnNHeroU0uLgBHiGV4BNkrXJe04JE=\n");
            $this->assertFalse( $consumer->associate(self::SERVER, 2.0, pack("H*", "60017f7ebf0ef29ace27f0dfee2aaa6528d170e147b1260cc3987d7851cb67d49fbfdbb42c56494e61b1e1e39fa42315db0bf4f879787fcf1e807d0629d47cf05d3ac50602b1e7f6e73cd370320ddcdcf7f7aa86f35a3273d187de9c9efa959a02ce3a9c80f47dfcc83cfaad60b673e1806a764227344deae158ceec9ca4d60e")) );

            // Test OpenID 2.0 association response with DH-SHA256
            $consumer->clearAssociation();
            $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                               "ns:http://specs.openid.net/auth/2.0\n" .
                               "assoc_type:HMAC-SHA256\n" .
                               "assoc_handle:0123456789absdef0123456789absdef\n" .
                               "expires_in:3600\n" .
                               "session_type:DH-SHA256\n".
                               "dh_server_public:AIlflxF8rvxx1Xi4Oj/KdP+7fvczeIRvx8WScMQS9I27R6YKd3Nx++5tAAF0rHelKDSG2ZeFM/zLEu9ZmUFzF02OaehWqykCfmtLASwMZO0u2GwYiIu5BoeJb9HlXJes58u/M4ViPXWhn27w2ZTlZJuuK8sDiTSTj9TmFxOriH4X\n".
                               "enc_mac_key:lvvCoTyvKy8oV6wnNHeroU0uLgBHiGV4BNkrXJe04JE=\n");
            $this->assertTrue( $consumer->associate(self::SERVER, 2.0, pack("H*", "60017f7ebf0ef29ace27f0dfee2aaa6528d170e147b1260cc3987d7851cb67d49fbfdbb42c56494e61b1e1e39fa42315db0bf4f879787fcf1e807d0629d47cf05d3ac50602b1e7f6e73cd370320ddcdcf7f7aa86f35a3273d187de9c9efa959a02ce3a9c80f47dfcc83cfaad60b673e1806a764227344deae158ceec9ca4d60e")) );
            $this->assertTrue( $storage->getAssociation(self::SERVER, $handle, $macFunc, $secret, $expires) );
            $this->assertSame( "0123456789absdef0123456789absdef", $handle );
            $this->assertSame( "sha256", $macFunc );
            $this->assertSame( "ed901bc561c29fd7bb42862e5f09fa37e7944a7ee72142322f34a21bfe1384b8", bin2hex($secret) );
            $this->assertTrue( $storage->delAssociation(self::SERVER) );

            // Test OpenID 2.0 association response without encryption (missing session_type)
            $consumer->clearAssociation();
            $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                               "ns:http://specs.openid.net/auth/2.0\n" .
                               "assoc_type:HMAC-SHA256\n" .
                               "assoc_handle:0123456789absdef0123456789absdef\n" .
                               "expires_in:3600\n" .
                               "mac_key:7ZAbxWHCn9e7QoYuXwn6N+eUSn7nIUIyLzSiG/4ThLg=\n");
            $this->assertTrue( $consumer->associate(self::SERVER, 2.0, pack("H*", "60017f7ebf0ef29ace27f0dfee2aaa6528d170e147b1260cc3987d7851cb67d49fbfdbb42c56494e61b1e1e39fa42315db0bf4f879787fcf1e807d0629d47cf05d3ac50602b1e7f6e73cd370320ddcdcf7f7aa86f35a3273d187de9c9efa959a02ce3a9c80f47dfcc83cfaad60b673e1806a764227344deae158ceec9ca4d60e")) );
            $this->assertTrue( $storage->getAssociation(self::SERVER, $handle, $macFunc, $secret, $expires) );
            $this->assertSame( "0123456789absdef0123456789absdef", $handle );
            $this->assertSame( "sha256", $macFunc );
            $this->assertSame( "ed901bc561c29fd7bb42862e5f09fa37e7944a7ee72142322f34a21bfe1384b8", bin2hex($secret) );
            $this->assertTrue( $storage->delAssociation(self::SERVER) );

            // Test OpenID 2.0 association response without encryption (blank session_type)
            $consumer->clearAssociation();
            $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                               "ns:http://specs.openid.net/auth/2.0\n" .
                               "assoc_type:HMAC-SHA256\n" .
                               "assoc_handle:0123456789absdef0123456789absdef\n" .
                               "expires_in:3600\n" .
                               "session_type:\n".
                               "mac_key:7ZAbxWHCn9e7QoYuXwn6N+eUSn7nIUIyLzSiG/4ThLg=\n");
            $this->assertTrue( $consumer->associate(self::SERVER, 2.0, pack("H*", "60017f7ebf0ef29ace27f0dfee2aaa6528d170e147b1260cc3987d7851cb67d49fbfdbb42c56494e61b1e1e39fa42315db0bf4f879787fcf1e807d0629d47cf05d3ac50602b1e7f6e73cd370320ddcdcf7f7aa86f35a3273d187de9c9efa959a02ce3a9c80f47dfcc83cfaad60b673e1806a764227344deae158ceec9ca4d60e")) );
            $this->assertTrue( $storage->getAssociation(self::SERVER, $handle, $macFunc, $secret, $expires) );
            $this->assertSame( "0123456789absdef0123456789absdef", $handle );
            $this->assertSame( "sha256", $macFunc );
            $this->assertSame( "ed901bc561c29fd7bb42862e5f09fa37e7944a7ee72142322f34a21bfe1384b8", bin2hex($secret) );
            $this->assertTrue( $storage->delAssociation(self::SERVER) );

            // Test OpenID 2.0 association response without encryption (blank session_type)
            $consumer->clearAssociation();
            $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                               "ns:http://specs.openid.net/auth/2.0\n" .
                               "assoc_type:HMAC-SHA256\n" .
                               "assoc_handle:0123456789absdef0123456789absdef\n" .
                               "expires_in:3600\n" .
                               "session_type:no-encryption\n".
                               "mac_key:7ZAbxWHCn9e7QoYuXwn6N+eUSn7nIUIyLzSiG/4ThLg=\n");
            $this->assertTrue( $consumer->associate(self::SERVER, 2.0, pack("H*", "60017f7ebf0ef29ace27f0dfee2aaa6528d170e147b1260cc3987d7851cb67d49fbfdbb42c56494e61b1e1e39fa42315db0bf4f879787fcf1e807d0629d47cf05d3ac50602b1e7f6e73cd370320ddcdcf7f7aa86f35a3273d187de9c9efa959a02ce3a9c80f47dfcc83cfaad60b673e1806a764227344deae158ceec9ca4d60e")) );
            $this->assertTrue( $storage->getAssociation(self::SERVER, $handle, $macFunc, $secret, $expires) );
            $this->assertSame( "0123456789absdef0123456789absdef", $handle );
            $this->assertSame( "sha256", $macFunc );
            $this->assertSame( "ed901bc561c29fd7bb42862e5f09fa37e7944a7ee72142322f34a21bfe1384b8", bin2hex($secret) );
            $this->assertTrue( $storage->delAssociation(self::SERVER) );
        } catch (Zend_OpenId_Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    /**
     * testing discovery
     *
     */
    public function testDiscovery()
    {
        $storage = new Zend_OpenId_Consumer_Storage_File(dirname(__FILE__)."/_files/consumer");
        $consumer = new Zend_OpenId_ConsumerHelper($storage);
        $http = new Zend_Http_Client(null,
            array(
                'maxredirects' => 4,
                'timeout'      => 15,
                'useragent'    => 'Zend_OpenId'
            ));
        $test = new Zend_Http_Client_Adapter_Test();
        $http->setAdapter($test);
        $consumer->SetHttpClient($http);

        // Bad response
        $storage->delDiscoveryInfo(self::ID);
        $id = self::ID;
        $this->assertFalse( $consumer->discovery($id, $server, $version) );

        // Test Yardis Discovery
        $storage->delDiscoveryInfo(self::ID);
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                           "<html><head>\n" .
                           "<meta http-equiv=\"X-XRDS-Location\" content=\"" . self::SERVER . "\" />" .
                           "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
                           "<xrds:XRDS xmlns:xrds=\"xri://\$xrds\" xmlns=\"xri://\$xrd*(\$v*2.0)\">\n" .
                           "  <XRD>\n" .
                           "  <Service priority=\"0\">\n" .
                           "  <Type>http://specs.openid.net/auth/2.0/server</Type>\n" .
                           "  <URI>" . self::SERVER . "</URI>\n" .
                           "  </Service>\n" .
                           "  </XRD>\n" .
                           "</xrds:XRDS>");
        $this->assertTrue( $consumer->discovery($id, $server, $version) );
        $this->assertSame( "http://specs.openid.net/auth/2.0/identifier_select", $id );
        $this->assertSame( self::SERVER, $server );
        $this->assertSame( 2.0, $version );

        // Test HTML based discovery (OpenID 1.1)
        $storage->delDiscoveryInfo(self::ID);
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                           "<html><head>\n" .
                           "<link rel=\"openid.server\" href=\"" . self::SERVER . "\">\n" .
                           "<link rel=\"openid.delegate\" href=\"" . self::REAL_ID . "\">\n" .
                           "</head><body</body></html>\n");
        $id = self::ID;
        $this->assertTrue( $consumer->discovery($id, $server, $version) );
        $this->assertSame( self::REAL_ID, $id );
        $this->assertSame( self::SERVER, $server );
        $this->assertSame( 1.1, $version );

        // Test HTML based discovery (OpenID 1.1)
        $storage->delDiscoveryInfo(self::ID);
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                           "<html><head>\n" .
                           "<link href=\"" . self::SERVER . "\" rel=\"openid.server\">\n" .
                           "<link href=\"" . self::REAL_ID . "\" rel=\"openid.delegate\">\n" .
                           "</head><body</body></html>\n");
        $id = self::ID;
        $this->assertTrue( $consumer->discovery($id, $server, $version) );
        $this->assertSame( self::REAL_ID, $id );
        $this->assertSame( self::SERVER, $server );
        $this->assertSame( 1.1, $version );

        // Test HTML based discovery (OpenID 2.0)
        $storage->delDiscoveryInfo(self::ID);
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                           "<html><head>\n" .
                           "<link rel=\"openid2.provider\" href=\"" . self::SERVER . "\">\n" .
                           "<link rel=\"openid2.local_id\" href=\"" . self::REAL_ID . "\">\n" .
                           "</head><body</body></html>\n");
        $id = self::ID;
        $this->assertTrue( $consumer->discovery($id, $server, $version) );
        $this->assertSame( self::REAL_ID, $id );
        $this->assertSame( self::SERVER, $server );
        $this->assertSame( 2.0, $version );

        // Test HTML based discovery (OpenID 2.0)
        $storage->delDiscoveryInfo(self::ID);
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                           "<html><head>\n" .
                           "<link href=\"" . self::SERVER . "\" rel=\"openid2.provider\">\n" .
                           "<link href=\"" . self::REAL_ID . "\" rel=\"openid2.local_id\">\n" .
                           "</head><body</body></html>\n");
        $id = self::ID;
        $this->assertTrue( $consumer->discovery($id, $server, $version) );
        $this->assertSame( self::REAL_ID, $id );
        $this->assertSame( self::SERVER, $server );
        $this->assertSame( 2.0, $version );

        // Test HTML based discovery (OpenID 1.1 and 2.0)
        $storage->delDiscoveryInfo(self::ID);
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                           "<html><head>\n" .
                           "<link rel=\"openid2.provider\" href=\"" . self::SERVER . "\">\n" .
                           "<link rel=\"openid2.local_id\" href=\"" . self::REAL_ID . "\">\n" .
                           "<link rel=\"openid.server\" href=\"" . self::SERVER . "\">\n" .
                           "<link rel=\"openid.delegate\" href=\"" . self::REAL_ID . "\">\n" .
                           "</head><body</body></html>\n");
        $id = self::ID;
        $this->assertTrue( $consumer->discovery($id, $server, $version) );
        $this->assertSame( self::REAL_ID, $id );
        $this->assertSame( self::SERVER, $server );
        $this->assertSame( 2.0, $version );

        // Test HTML based discovery (OpenID 1.1) (single quotes)
        $storage->delDiscoveryInfo(self::ID);
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                           "<html><head>\n" .
                           "<link rel='openid.server' href='" . self::SERVER . "'>\n" .
                           "<link rel='openid.delegate' href='" . self::REAL_ID . "'>\n" .
                           "</head><body</body></html>\n");
        $id = self::ID;
        $this->assertTrue( $consumer->discovery($id, $server, $version) );
        $this->assertSame( self::REAL_ID, $id );
        $this->assertSame( self::SERVER, $server );
        $this->assertSame( 1.1, $version );

        // Test HTML based discovery (OpenID 1.1) (single quotes)
        $storage->delDiscoveryInfo(self::ID);
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                           "<html><head>\n" .
                           "<link href='" . self::SERVER . "' rel='openid.server'>\n" .
                           "<link href='" . self::REAL_ID . "' rel='openid.delegate'>\n" .
                           "</head><body</body></html>\n");
        $id = self::ID;
        $this->assertTrue( $consumer->discovery($id, $server, $version) );
        $this->assertSame( self::REAL_ID, $id );
        $this->assertSame( self::SERVER, $server );
        $this->assertSame( 1.1, $version );

        // Test HTML based discovery (OpenID 2.0) (single quotes)
        $storage->delDiscoveryInfo(self::ID);
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                           "<html><head>\n" .
                           "<link rel='openid2.provider' href='" . self::SERVER . "'>\n" .
                           "<link rel='openid2.local_id' href='" . self::REAL_ID . "'>\n" .
                           "</head><body</body></html>\n");
        $id = self::ID;
        $this->assertTrue( $consumer->discovery($id, $server, $version) );
        $this->assertSame( self::REAL_ID, $id );
        $this->assertSame( self::SERVER, $server );
        $this->assertSame( 2.0, $version );

        // Test HTML based discovery (OpenID 2.0) (single quotes)
        $storage->delDiscoveryInfo(self::ID);
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                           "<html><head>\n" .
                           "<link href='" . self::SERVER . "' rel='openid2.provider'>\n" .
                           "<link href='" . self::REAL_ID . "' rel='openid2.local_id'>\n" .
                           "</head><body</body></html>\n");
        $id = self::ID;
        $this->assertTrue( $consumer->discovery($id, $server, $version) );
        $this->assertSame( self::REAL_ID, $id );
        $this->assertSame( self::SERVER, $server );
        $this->assertSame( 2.0, $version );

        // Test HTML based discovery (OpenID 1.1 and 2.0) (single quotes)
        $storage->delDiscoveryInfo(self::ID);
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                           "<html><head>\n" .
                           "<link rel='openid2.provider' href='" . self::SERVER . "'>\n" .
                           "<link rel='openid2.local_id' href='" . self::REAL_ID . "'>\n" .
                           "<link rel='openid.server' href='" . self::SERVER . "'>\n" .
                           "<link rel='openid.delegate' href='" . self::REAL_ID . "'>\n" .
                           "</head><body</body></html>\n");
        $id = self::ID;
        $this->assertTrue( $consumer->discovery($id, $server, $version) );
        $this->assertSame( self::REAL_ID, $id );
        $this->assertSame( self::SERVER, $server );
        $this->assertSame( 2.0, $version );

        // Wrong HTML
        $storage->delDiscoveryInfo(self::ID);
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                           "<html><head>\n" .
                           "</head><body</body></html>\n");
        $id = self::ID;
        $this->assertFalse( $consumer->discovery($id, $server, $version) );

        // Test HTML based discovery with multivalue rel (OpenID 1.1)
        $storage->delDiscoveryInfo(self::ID);
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\n" .
                           "<html><head>\n" .
                           "<link rel=\" aaa openid.server bbb \" href=\"" . self::SERVER . "\">\n" .
                           "<link rel=\"aaa openid.delegate\" href=\"" . self::REAL_ID . "\">\n" .
                           "</head><body</body></html>\n");
        $id = self::ID;
        $this->assertTrue( $consumer->discovery($id, $server, $version) );
        $this->assertSame( self::REAL_ID, $id );
        $this->assertSame( self::SERVER, $server );
        $this->assertSame( 1.1, $version );
    }

    /**
     * testing verify
     *
     */
    public function testVerify()
    {
        $expiresIn = time() + 600;
        $_SERVER['SCRIPT_URI'] = "http://www.zf-test.com/test.php";
        $storage = new Zend_OpenId_Consumer_Storage_File(dirname(__FILE__)."/_files/consumer");
        $consumer = new Zend_OpenId_ConsumerHelper($storage);

        $storage->addDiscoveryInfo(self::ID, self::REAL_ID, self::SERVER, 1.1, $expiresIn);

        // Wrong arguments
        $this->assertFalse( $consumer->verify(array()) );

        // HMAC-SHA1
        $consumer->clearAssociation();
        $params = array(
            "openid_return_to" => "http://www.zf-test.com/test.php",
            "openid_assoc_handle" => self::HANDLE,
            "openid_claimed_id" => self::ID,
            "openid_identity" => self::REAL_ID,
            "openid_response_nonce" => "2007-08-14T12:52:33Z46c1a59124ffe",
            "openid_mode" => "id_res",
            "openid_op_endpoint" => self::SERVER,
            "openid_signed" => "op_endpoint,assoc_handle,return_to,claimed_id,identity,response_nonce,mode,signed",
            "openid_sig" => "4KFaoZApYmYq6aFdIGzzgbsIiaA="
        );
        $storage->delAssociation(self::SERVER);
        $storage->addAssociation(self::SERVER, self::HANDLE, "sha1", pack("H*", "8382aea922560ece833ba55fa53b7a975f597370"), $expiresIn);
        $storage->purgeNonces();
        $this->assertTrue( $consumer->verify($params) );

        $storage->delDiscoveryInfo(self::ID);
        $storage->addDiscoveryInfo(self::ID, self::REAL_ID, self::SERVER, 2.0, $expiresIn);

        // HMAC-SHA256
        $consumer->clearAssociation();
        $params = array(
            "openid_ns" => Zend_OpenId::NS_2_0,
            "openid_op_endpoint" => self::SERVER,
            "openid_return_to" => "http://www.zf-test.com/test.php",
            "openid_assoc_handle" => self::HANDLE,
            "openid_claimed_id" => self::ID,
            "openid_identity" => self::REAL_ID,
            "openid_response_nonce" => "2007-08-14T12:52:33Z46c1a59124ffe",
            "openid_mode" => "id_res",
            "openid_signed" => "op_endpoint,assoc_handle,return_to,claimed_id,identity,response_nonce,mode,signed",
            "openid_sig" => "O1ycNUA75AiVnoIcdBrx/nx462lLRv4f7xO9IPRiHqU="
        );
        $storage->delAssociation(self::SERVER);
        $storage->addAssociation(self::SERVER, self::HANDLE, "sha256", pack("H*", "ed901bc561c29fd7bb42862e5f09fa37e7944a7ee72142322f34a21bfe1384b8"), $expiresIn);
        $storage->purgeNonces();
        $this->assertTrue( $consumer->verify($params) );

        // HMAC-SHA256 (duplicate response_nonce)
        $consumer->clearAssociation();
        $params = array(
            "openid_ns" => Zend_OpenId::NS_2_0,
            "openid_op_endpoint" => self::SERVER,
            "openid_return_to" => "http://www.zf-test.com/test.php",
            "openid_assoc_handle" => self::HANDLE,
            "openid_claimed_id" => self::ID,
            "openid_identity" => self::REAL_ID,
            "openid_response_nonce" => "2007-08-14T12:52:33Z46c1a59124ffe",
            "openid_mode" => "id_res",
            "openid_signed" => "assoc_handle,return_to,claimed_id,identity,response_nonce,mode,signed",
            "openid_sig" => "rMiVhEmHVcIHoY2uzPNb7udWqa4lruvjnwZfujct0TE="
        );
        $storage->delAssociation(self::SERVER);
        $storage->addAssociation(self::SERVER, self::HANDLE, "sha256", pack("H*", "ed901bc561c29fd7bb42862e5f09fa37e7944a7ee72142322f34a21bfe1384b8"), $expiresIn);
        $this->assertFalse( $consumer->verify($params) );

        $storage->delDiscoveryInfo(self::ID);
        $storage->addDiscoveryInfo(self::ID, self::REAL_ID, self::SERVER, 1.1, $expiresIn);

        // wrong signature
        $consumer->clearAssociation();
        $params = array(
            "openid_return_to" => "http://www.zf-test.com/test.php",
            "openid_op_endpoint" => self::SERVER,
            "openid_assoc_handle" => self::HANDLE,
            "openid_claimed_id" => self::ID,
            "openid_identity" => self::REAL_ID,
            "openid_response_nonce" => "2007-08-14T12:52:33Z46c1a59124fff",
            "openid_mode" => "id_res",
            "openid_signed" => "op_endpoint,assoc_handle,return_to,claimed_id,identity,response_nonce,mode,signed",
            "openid_sig" => "h/5AFD25NpzSok5tzHEGCVUkQSw="
        );
        $storage->delAssociation(self::SERVER);
        $storage->addAssociation(self::SERVER, self::HANDLE, "sha1", pack("H*", "8382aea922560ece833ba55fa53b7a975f597370"), $expiresIn);
        $storage->purgeNonces();
        $this->assertFalse( $consumer->verify($params) );
        $this->assertFalse( $storage->getAssociation(self::SERVER, $handle, $func, $secret, $expires) );

        // openid_invalidate_handle
        $consumer->clearAssociation();
        $params = array(
            "openid_return_to" => "http://www.zf-test.com/test.php",
            "openid_op_endpoint" => self::SERVER,
            "openid_invalidate_handle" => self::HANDLE."1",
            "openid_assoc_handle" => self::HANDLE,
            "openid_claimed_id" => self::ID,
            "openid_identity" => self::REAL_ID,
            "openid_response_nonce" => "2007-08-14T12:52:33Z46c1a59124ffe",
            "openid_mode" => "id_res",
            "openid_signed" => "op_endpoint,assoc_handle,return_to,claimed_id,identity,response_nonce,mode,signed",
            "openid_sig" => "4KFaoZApYmYq6aFdIGzzgbsIiaA="
        );
        $storage->delAssociation(self::SERVER);
        $storage->addAssociation(self::SERVER, self::HANDLE, "sha1", pack("H*", "8382aea922560ece833ba55fa53b7a975f597370"), $expiresIn);
        $storage->delAssociation(self::SERVER."1");
        $storage->addAssociation(self::SERVER."1", self::HANDLE."1", "sha1", pack("H*", "8382aea922560ece833ba55fa53b7a975f597370"), $expiresIn);
        $storage->purgeNonces();
        $this->assertTrue( $consumer->verify($params) );
        $this->assertFalse( $storage->getAssociation(self::SERVER."1", $handle, $func, $secret, $expires) );

        $storage->delDiscoveryInfo(self::ID);
    }

    /**
     * testing verify
     *
     */
    public function testVerifyDumb()
    {
        $expiresIn = time() + 600;
        $_SERVER['SCRIPT_URI'] = "http://www.zf-test.com/test.php";
        $storage = new Zend_OpenId_Consumer_Storage_File(dirname(__FILE__)."/_files/consumer");
        $consumer = new Zend_OpenId_ConsumerHelper($storage);
        $http = new Zend_Http_Client(null,
            array(
                'maxredirects' => 4,
                'timeout'      => 15,
                'useragent'    => 'Zend_OpenId'
            ));
        $test = new Zend_Http_Client_Adapter_Test();
        $http->setAdapter($test);
        $consumer->SetHttpClient($http);
        $storage->delDiscoveryInfo(self::ID);
        $this->assertTrue( $storage->addDiscoveryInfo(self::ID, self::REAL_ID, self::SERVER, 1.1, $expiresIn) );
        $this->assertTrue( $storage->addDiscoveryInfo(self::REAL_ID, self::REAL_ID, self::SERVER, 1.1, $expiresIn) );

        // Wrong arguments (no identity)
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\nis_valid:true");
        $consumer->clearAssociation();
        $storage->delAssociation(self::SERVER);
        $params = array(
            "openid_return_to" => "http://www.zf-test.com/test.php",
            "openid_assoc_handle" => self::HANDLE,
            "openid_response_nonce" => "2007-08-14T12:52:33Z46c1a59124ffe",
            "openid_mode" => "id_res",
            "openid_signed" => "assoc_handle,return_to,response_nonce,mode,signed",
            "openid_sig" => "h/5AFD25NpzSok5tzHEGCVUkQSw="
        );
        $storage->purgeNonces();
        $this->assertFalse( $consumer->verify($params) );

        $test->setResponse("HTTP/1.1 200 OK\r\n\r\nis_valid:false");
        $consumer->clearAssociation();
        $storage->delAssociation(self::SERVER);
        $params = array(
            "openid_return_to" => "http://www.zf-test.com/test.php",
            "openid_assoc_handle" => self::HANDLE,
            "openid_claimed_id" => self::ID,
            "openid_identity" => self::REAL_ID,
            "openid_response_nonce" => "2007-08-14T12:52:33Z46c1a59124ffe",
            "openid_mode" => "id_res",
            "openid_signed" => "assoc_handle,return_to,claimed_id,identity,response_nonce,mode,signed",
            "openid_sig" => "h/5AFD25NpzSok5tzHEGCVUkQSw="
        );
        $storage->purgeNonces();
        $this->assertFalse( $consumer->verify($params) );
        $this->assertSame( "POST / HTTP/1.1\r\n" .
                           "Host: www.myopenid.com\r\n" .
                           "Connection: close\r\n" .
                           "Accept-encoding: gzip, deflate\r\n" .
                           "User-Agent: Zend_OpenId\r\n" .
                           "Content-Type: application/x-www-form-urlencoded\r\n" .
                           "Content-Length: 445\r\n\r\n" .
                           "openid.return_to=http%3A%2F%2Fwww.zf-test.com%2Ftest.php&" .
                           "openid.assoc_handle=d41d8cd98f00b204e9800998ecf8427e&" .
                           "openid.claimed_id=http%3A%2F%2Fid.myopenid.com%2F&" .
                           "openid.identity=http%3A%2F%2Freal_id.myopenid.com%2F&" .
                           "openid.response_nonce=2007-08-14T12%3A52%3A33Z46c1a59124ffe&" .
                           "openid.mode=check_authentication&" .
                           "openid.signed=assoc_handle%2Creturn_to%2Cclaimed_id%2Cidentity%2Cresponse_nonce%2Cmode%2Csigned&" .
                           "openid.sig=h%2F5AFD25NpzSok5tzHEGCVUkQSw%3D",
                           $http->getLastRequest() );

        $test->setResponse("HTTP/1.1 200 OK\r\n\r\nis_valid:true");
        $consumer->clearAssociation();
        $storage->delAssociation(self::SERVER);
        $params = array(
            "openid_return_to" => "http://www.zf-test.com/test.php",
            "openid_assoc_handle" => self::HANDLE,
            "openid_identity" => self::REAL_ID,
            "openid_response_nonce" => "2007-08-14T12:52:33Z46c1a59124ffe",
            "openid_mode" => "id_res",
            "openid_signed" => "assoc_handle,return_to,identity,response_nonce,mode,signed",
            "openid_sig" => "h/5AFD25NpzSok5tzHEGCVUkQSw="
        );
        $storage->purgeNonces();
        $this->assertTrue( $consumer->verify($params) );

        // SREG
        $this->assertTrue( $storage->delDiscoveryInfo(self::ID) );
        $this->assertTrue( $storage->addDiscoveryInfo(self::ID, self::REAL_ID, self::SERVER, 2.0, $expiresIn) );
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\nis_valid:true");
        $consumer->clearAssociation();
        $storage->delAssociation(self::SERVER);
        $params = array(
            "openid_ns"        => Zend_OpenId::NS_2_0,
            "openid_return_to" => "http://www.zf-test.com/test.php",
            "openid_assoc_handle" => self::HANDLE,
            "openid_claimed_id" => self::ID,
            "openid_identity" => self::REAL_ID,
            "openid_response_nonce" => "2007-08-14T12:52:33Z46c1a59124ffe",
            "openid_op_endpoint" => self::SERVER,
            "openid_mode" => "id_res",
            "openid_ns_sreg" => "http://openid.net/extensions/sreg/1.1",
            "openid_sreg_nickname" => "test",
            "openid_signed" => "ns,assoc_handle,return_to,claimed_id,identity,response_nonce,mode,ns.sreg,sreg.nickname,signed",
            "openid_sig" => "h/5AFD25NpzSok5tzHEGCVUkQSw="
        );
        $storage->purgeNonces();
        $this->assertTrue( $consumer->verify($params) );
        $this->assertSame( "POST / HTTP/1.1\r\n" .
                           "Host: www.myopenid.com\r\n" .
                           "Connection: close\r\n" .
                           "Accept-encoding: gzip, deflate\r\n" .
                           "User-Agent: Zend_OpenId\r\n" .
                           "Content-Type: application/x-www-form-urlencoded\r\n" .
                           "Content-Length: 672\r\n\r\n" .
                           "openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0&" .
                           "openid.return_to=http%3A%2F%2Fwww.zf-test.com%2Ftest.php&" .
                           "openid.assoc_handle=d41d8cd98f00b204e9800998ecf8427e&" .
                           "openid.claimed_id=http%3A%2F%2Fid.myopenid.com%2F&" .
                           "openid.identity=http%3A%2F%2Freal_id.myopenid.com%2F&" .
                           "openid.response_nonce=2007-08-14T12%3A52%3A33Z46c1a59124ffe&" .
                           "openid.op_endpoint=http%3A%2F%2Fwww.myopenid.com%2F&" .
                           "openid.mode=check_authentication&" .
                           "openid.ns.sreg=http%3A%2F%2Fopenid.net%2Fextensions%2Fsreg%2F1.1&" .
                           "openid.sreg.nickname=test&" .
                           "openid.signed=ns%2Cassoc_handle%2Creturn_to%2Cclaimed_id%2Cidentity%2Cresponse_nonce%2Cmode%2Cns.sreg%2Csreg.nickname%2Csigned&" .
                           "openid.sig=h%2F5AFD25NpzSok5tzHEGCVUkQSw%3D",
                           $http->getLastRequest() );

        // invalidate_handle
        $test->setResponse("HTTP/1.1 200 OK\r\n\r\nis_valid:false\ninvalidate_handle:".self::HANDLE."1"."\n");
        $consumer->clearAssociation();
        $params = array(
            "openid_ns"        => Zend_OpenId::NS_2_0,
            "openid_return_to" => "http://www.zf-test.com/test.php",
            "openid_assoc_handle" => self::HANDLE,
            "openid_claimed_id" => self::ID,
            "openid_identity" => self::REAL_ID,
            "openid_response_nonce" => "2007-08-14T12:52:33Z46c1a59124ffe",
            "openid_op_endpoint" => self::SERVER,
            "openid_mode" => "id_res",
            "openid_signed" => "assoc_handle,return_to,claimed_id,identity,response_nonce,mode,signed",
            "openid_sig" => "h/5AFD25NpzSok5tzHEGCVUkQSw="
        );
        $storage->delAssociation(self::SERVER."1");
        $storage->addAssociation(self::SERVER."1", self::HANDLE."1", "sha1", pack("H*", "8382aea922560ece833ba55fa53b7a975f597370"), $expiresIn);
        $storage->purgeNonces();
        $this->assertFalse( $consumer->verify($params) );
        $this->assertFalse( $storage->getAssociation(self::SERVER."1", $handle, $func, $secret, $expires) );
    }

    /**
     * Test the required parameters for the signature
     * @see https://openid.net/specs/openid-authentication-2_0.html#positive_assertions 
     */
    public function testSignedParams()
    {
        $expiresIn = time() + 600;
        $_SERVER['SCRIPT_URI'] = "http://www.zf-test.com/test.php";
        $storage = new Zend_OpenId_Consumer_Storage_File(dirname(__FILE__)."/_files/consumer");
        $consumer = new Zend_OpenId_ConsumerHelper($storage);

        $storage->addDiscoveryInfo(self::ID, self::REAL_ID, self::SERVER, 1.1, $expiresIn);

        // Wrong arguments
        $this->assertFalse( $consumer->verify(array()) );
        // HMAC-SHA1
        $consumer->clearAssociation();
        $params = array(
            "openid_return_to" => "http://www.zf-test.com/test.php",
            "openid_assoc_handle" => self::HANDLE,
            "openid_claimed_id" => self::ID,
            "openid_identity" => self::REAL_ID,
            "openid_response_nonce" => "2007-08-14T12:52:33Z46c1a59124ffe",
            "openid_mode" => "id_res",
            "openid_signed" => "assoc_handle,return_to,claimed_id,identity,response_nonce,mode,signed",
            "openid_sig" => "h/5AFD25NpzSok5tzHEGCVUkQSw="
        );
        $storage->delAssociation(self::SERVER);
        $storage->addAssociation(self::SERVER, self::HANDLE, "sha1", pack("H*", "8382aea922560ece833ba55fa53b7a975f597370"), $expiresIn);
        $storage->purgeNonces();
        $this->assertFalse( $consumer->verify($params) );
        $this->assertEquals( "The required parameter op_endpoint is missing in the signed", $consumer->getError());
    }

}

class Zend_OpenId_ConsumerHelper extends Zend_OpenId_Consumer {

    public function addAssociation($url, $handle, $macFunc, $secret, $expires)
    {
        return $this->_addAssociation($url, $handle, $macFunc, $secret, $expires);
    }

    public function getAssociation($url, &$handle, &$macFunc, &$secret, &$expires)
    {
        return $this->_getAssociation($url, $handle, $macFunc, $secret, $expires);
    }

    public function clearAssociation()
    {
        $this->_cache = array();
    }

    public function httpRequest($url, $method = 'GET', array $params = array())
    {
        return $this->_httpRequest($url, $method, $params);
    }

    public function associate($url, $version, $priv_key = null)
    {
        return $this->_associate($url, $version, $priv_key);
    }

    public function discovery(&$id, &$server, &$version)
    {
        return $this->_discovery($id, $server, $version);
    }

}
