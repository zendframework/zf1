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
require_once 'Zend/OpenId.php';

/**
 * Zend_OpenId_ResponseHelper
 */
require_once 'Zend/OpenId/ResponseHelper.php';


/**
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_OpenId
 */
class Zend_OpenIdTest extends PHPUnit_Framework_TestCase
{
    private $_serverVariables;

    public function setUp()
    {
        $this->_serverVariables = $_SERVER;
    }

    public function tearDown()
    {
        $_SERVER = $this->_serverVariables;
    }


    /**
     * testing testSelfUrl
     *
     */
    public function testSelfUrl()
    {
        unset($_SERVER['SCRIPT_URI']);
        unset($_SERVER['HTTPS']);
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['SERVER_NAME']);
        unset($_SERVER['SERVER_PORT']);
        unset($_SERVER['SCRIPT_URL']);
        unset($_SERVER['REDIRECT_URL']);
        unset($_SERVER['PHP_SELF']);
        unset($_SERVER['SCRIPT_NAME']);
        unset($_SERVER['PATH_INFO']);
        $this->assertSame( 'http://', Zend_OpenId::selfUrl() );

        $_SERVER['SCRIPT_URI'] = "http://www.test.com/";
        $this->assertSame( 'http://www.test.com/', Zend_OpenId::selfUrl() );

        unset($_SERVER['SCRIPT_URI']);
        $_SERVER['HTTP_HOST'] = "www.test.com";
        $_SERVER['SERVER_NAME'] = "www.wrong.com";
        $this->assertSame( 'http://www.test.com', Zend_OpenId::selfUrl() );

        $_SERVER['HTTP_HOST'] = "www.test.com:80";
        $this->assertSame( 'http://www.test.com', Zend_OpenId::selfUrl() );

        $_SERVER['HTTP_HOST'] = "www.test.com:8080";
        $this->assertSame( 'http://www.test.com:8080', Zend_OpenId::selfUrl() );

        $_SERVER['HTTP_HOST'] = "www.test.com";
        $_SERVER['SERVER_PORT'] = "80";
        $this->assertSame( 'http://www.test.com', Zend_OpenId::selfUrl() );

        $_SERVER['SERVER_PORT'] = "8080";
        $this->assertSame( 'http://www.test.com:8080', Zend_OpenId::selfUrl() );

        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['SERVER_PORT']);
        $_SERVER['SERVER_NAME'] = "www.test.com";
        $this->assertSame( 'http://www.test.com', Zend_OpenId::selfUrl() );

        $_SERVER['SERVER_PORT'] = "80";
        $this->assertSame( 'http://www.test.com', Zend_OpenId::selfUrl() );

        $_SERVER['SERVER_PORT'] = "8080";
        $this->assertSame( 'http://www.test.com:8080', Zend_OpenId::selfUrl() );

        unset($_SERVER['SERVER_PORT']);
        $_SERVER['HTTPS'] = "on";
        $this->assertSame( 'https://www.test.com', Zend_OpenId::selfUrl() );

        $_SERVER['SERVER_PORT'] = "443";
        $this->assertSame( 'https://www.test.com', Zend_OpenId::selfUrl() );

        $_SERVER['SERVER_PORT'] = "8080";
        $this->assertSame( 'https://www.test.com:8080', Zend_OpenId::selfUrl() );

        unset($_SERVER['SERVER_PORT']);
        unset($_SERVER['HTTPS']);

        $_SERVER['SCRIPT_URL'] = '/test.php';
        $_SERVER['PHP_SELF'] = '/bug.php';
        $_SERVER['SCRIPT_NAME'] = '/bug.php';
        $_SERVER['PATH_INFO'] = '/bug';
        $this->assertSame( 'http://www.test.com/test.php', Zend_OpenId::selfUrl() );

        unset($_SERVER['SCRIPT_URL']);
        $_SERVER['REDIRECT_URL'] = '/ok';
        $_SERVER['PHP_SELF'] = '/bug.php';
        $_SERVER['SCRIPT_NAME'] = '/bug.php';
        $_SERVER['PATH_INFO'] = '/bug';
        $this->assertSame( 'http://www.test.com/ok', Zend_OpenId::selfUrl() );

        unset($_SERVER['REDIRECT_URL']);
        $_SERVER['PHP_SELF'] = '/test.php';
        $this->assertSame( 'http://www.test.com/test.php', Zend_OpenId::selfUrl() );

        unset($_SERVER['PHP_SELF']);
        $_SERVER['SCRIPT_NAME'] = '/test.php';
        $_SERVER['PATH_INFO'] = '/ok';
        $this->assertSame( 'http://www.test.com/test.php/ok', Zend_OpenId::selfUrl() );

        unset($_SERVER['PATH_INFO']);
        $this->assertSame( 'http://www.test.com/test.php', Zend_OpenId::selfUrl() );
    }

    /**
     * testing testAbsolutefUrl
     *
     */
    public function testAbsoluteUrl()
    {
        unset($_SERVER['SCRIPT_URI']);
        unset($_SERVER['HTTPS']);
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['SERVER_NAME']);
        unset($_SERVER['SERVER_PORT']);
        unset($_SERVER['SCRIPT_URL']);
        unset($_SERVER['REDIRECT_URL']);
        unset($_SERVER['PHP_SELF']);
        unset($_SERVER['SCRIPT_NAME']);
        unset($_SERVER['PATH_INFO']);

        $_SERVER['HTTP_HOST'] = "www.test.com";
        $_SERVER['SCRIPT_NAME'] = '/a/b/c/test.php';

        $this->assertSame( 'http://www.test.com/a/b/c/test.php', Zend_OpenId::absoluteUrl("") );

        $this->assertSame( 'http://www.test.com/a/b/c/ok.php', Zend_OpenId::absoluteUrl("ok.php") );

        $this->assertSame( 'http://www.test.com/a/ok.php', Zend_OpenId::absoluteUrl("/a/ok.php") );

        $this->assertSame( 'http://www.php.net/ok.php', Zend_OpenId::absoluteUrl("http://www.php.net/ok.php") );

        $this->assertSame( 'https://www.php.net/ok.php', Zend_OpenId::absoluteUrl("https://www.php.net/ok.php") );

        $_SERVER['SCRIPT_NAME'] = '/test.php';
        $this->assertSame( 'http://www.test.com/a/b.php', Zend_OpenId::absoluteUrl("/a/b.php") );

        $this->assertSame( 'http://www.test.com/a/b.php', Zend_OpenId::absoluteUrl("a/b.php") );
    }

    /**
     * testing testParamsToQuery
     *
     */
    public function testParamsToQuery()
    {
        $this->assertSame( '', Zend_OpenId::paramsToQuery(array()) );
        $this->assertSame( 'a=1', Zend_OpenId::paramsToQuery(array('a'=>1)) );
        $this->assertSame( 'a=1&b=2', Zend_OpenId::paramsToQuery(array('a'=>1,'b'=>2)) );
        $this->assertSame( 'a=x+y', Zend_OpenId::paramsToQuery(array('a'=>'x y')) );
    }

    /**
     * testing testNormalizeUrl
     *
     */
    public function testNormalizeUrl()
    {
        $url = 'example://a/b/c/%7Bfoo%7D';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://a/b/c/%7Bfoo%7D', $url );

        $url = 'eXAMPLE://A/./b/../b/%63/%7bfoo%7d';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://a/b/c/%7Bfoo%7D', $url );

        $url = 'eXAMPLE://A/./b/../b/%63/%bbfoo%Bd';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://a/b/c/%BBfoo%BD', $url );

        $url = 'example://a/b/c/%1';
        $this->assertFalse( Zend_OpenId::normalizeUrl($url) );

        $url = 'example://a/b/c/%x1';
        $this->assertFalse( Zend_OpenId::normalizeUrl($url) );

        $url = 'example://a/b/c/%1x';
        $this->assertFalse( Zend_OpenId::normalizeUrl($url) );

        $url = 'eXAMPLE://A/b/c/x%20y';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://a/b/c/x%20y', $url );

        $url = 'example://host/.a/b/c';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/.a/b/c', $url );

        $url = 'a/b/c';
        $this->assertFalse( Zend_OpenId::normalizeUrl($url) );

        $url = 'example://:80/a/b/c';
        $this->assertFalse( Zend_OpenId::normalizeUrl($url) );

        $url = 'example://host/a/.b/c';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/a/.b/c', $url );

        $url = 'example://host/a/b/.c';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/a/b/.c', $url );

        $url = 'example://host/..a/b/c';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/..a/b/c', $url );

        $url = 'example://host/a/..b/c';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/a/..b/c', $url );

        $url = 'example://host/a/b/..c';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/a/b/..c', $url );

        $url = 'example://host/./b/c';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/b/c', $url );

        $url = 'example://host/a/./c';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/a/c', $url );

        $url = 'example://host/a/b/.';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/a/b', $url );

        $url = 'example://host/a/b/./';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/a/b/', $url );

        $url = 'example://host/../b/c';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/b/c', $url );

        $url = 'example://host/a/../c';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/c', $url );

        $url = 'example://host/a/b/..';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/a', $url );

        $url = 'example://host/a/b/../';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/a/', $url );

        $url = 'example://host/a/b/c/..';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/a/b', $url );

        $url = 'example://host/a/b/c/../..';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/a', $url );

        $url = 'example://host/a/b/c/../../..';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/', $url );

        $url = 'example://host///a///b///c///..///../d';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/a/d', $url );

        $url = 'example://host///a///b///c///.///./d';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/a/b/c/d', $url );

        $url = 'example://host///a///b///c///..///./d';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/a/b/d', $url );

        $url = 'example://host///a///b///c///.///../d';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'example://host/a/b/d', $url );

        $url = 'http://example.com';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'http://example.com/', $url );

        $url = 'http://example.com/';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'http://example.com/', $url );

        $url = 'http://example.com:';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'http://example.com/', $url );

        $url = 'http://example.com:80/';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'http://example.com/', $url );

        $url = 'https://example.com:443/';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'https://example.com/', $url );

        $url = 'http://example.com?';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'http://example.com/?', $url );

        $url = 'http://example.com/?';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'http://example.com/?', $url );

        $url = 'http://example.com/test.php?Foo=Bar#Baz';
        $this->assertTrue( Zend_OpenId::normalizeUrl($url) );
        $this->assertSame( 'http://example.com/test.php?Foo=Bar#Baz', $url );
    }

    /**
     * testing testNormalize
     *
     */
    public function testNormalize()
    {
        $url = '';
        $this->assertTrue( Zend_OpenId::normalize($url) );
        $this->assertSame( '', $url );

        $url = ' localhost ';
        $this->assertTrue( Zend_OpenId::normalize($url) );
        $this->assertSame( 'http://localhost/', $url );

        $url = 'xri://$ip*127.0.0.1';
        $this->assertTrue( Zend_OpenId::normalize($url) );
        $this->assertSame( 'http://127.0.0.1/', $url );

        $url = 'xri://$dns*localhost';
        $this->assertTrue( Zend_OpenId::normalize($url) );
        $this->assertSame( 'http://localhost/', $url );

        $url = 'xri://localhost';
        $this->assertTrue( Zend_OpenId::normalize($url) );
        $this->assertSame( 'http://localhost/', $url );

        $url = '=name';
        $this->assertTrue( Zend_OpenId::normalize($url) );
        $this->assertSame( '=name', $url );

        $url = '@name';
        $this->assertTrue( Zend_OpenId::normalize($url) );
        $this->assertSame( '@name', $url );

        $url = '+name';
        $this->assertTrue( Zend_OpenId::normalize($url) );
        $this->assertSame( '+name', $url );

        $url = '$name';
        $this->assertTrue( Zend_OpenId::normalize($url) );
        $this->assertSame( '$name', $url );

        $url = '!name';
        $this->assertTrue( Zend_OpenId::normalize($url) );
        $this->assertSame( '!name', $url );

        $url = 'localhost';
        $this->assertTrue( Zend_OpenId::normalize($url) );
        $this->assertSame( 'http://localhost/', $url );

        $url = 'http://localhost';
        $this->assertTrue( Zend_OpenId::normalize($url) );
        $this->assertSame( 'http://localhost/', $url );

        $url = 'https://localhost';
        $this->assertTrue( Zend_OpenId::normalize($url) );
        $this->assertSame( 'https://localhost/', $url );
    }

    /**
     * testing testRedirect
     *
     */
    public function testRedirect()
    {
        $response = new Zend_OpenId_ResponseHelper(true);
        Zend_OpenId::redirect("http://www.test.com/", null, $response, 'GET');
        $this->assertSame( 302, $response->getHttpResponseCode() );
        $this->assertSame( array(), $response->getRawHeaders() );
        $headers = $response->getHeaders();
        $this->assertTrue( is_array($headers) );
        $this->assertSame( 1, count($headers) );
        $this->assertTrue( is_array($headers[0]) );
        $this->assertSame( 3, count($headers[0]) );
        $this->assertSame( 'Location', $headers[0]['name'] );
        $this->assertSame( 'http://www.test.com/', $headers[0]['value'] );
        $this->assertSame( true, $headers[0]['replace'] );
        $this->assertSame( '', $response->getBody() );

        $response = new Zend_OpenId_ResponseHelper(true);
        Zend_OpenId::redirect("http://www.test.com/test.php?a=b", null, $response, 'GET');
        $headers = $response->getHeaders();
        $this->assertSame( 'http://www.test.com/test.php?a=b', $headers[0]['value'] );

        $response = new Zend_OpenId_ResponseHelper(true);
        Zend_OpenId::redirect("http://www.test.com/test.php", array('a'=>'b'), $response, 'GET');
        $headers = $response->getHeaders();
        $this->assertSame( 'http://www.test.com/test.php?a=b', $headers[0]['value'] );

        $response = new Zend_OpenId_ResponseHelper(true);
        Zend_OpenId::redirect("http://www.test.com/test.php", array('a'=>'b', 'c'=>'d'), $response, 'GET');
        $headers = $response->getHeaders();
        $this->assertSame( 'http://www.test.com/test.php?a=b&c=d', $headers[0]['value'] );

        $response = new Zend_OpenId_ResponseHelper(true);
        Zend_OpenId::redirect("http://www.test.com/test.php?a=b", array('c'=>'d'), $response, 'GET');
        $headers = $response->getHeaders();
        $this->assertSame( 'http://www.test.com/test.php?a=b&c=d', $headers[0]['value'] );

        $response = new Zend_OpenId_ResponseHelper(true);
        Zend_OpenId::redirect("http://www.test.com/test.php", array('a'=>'x y'), $response, 'GET');
        $headers = $response->getHeaders();
        $this->assertSame( 'http://www.test.com/test.php?a=x+y', $headers[0]['value'] );

        $response = new Zend_OpenId_ResponseHelper(false);
        Zend_OpenId::redirect("http://www.test.com/", null, $response, 'GET');
        $this->assertSame( 200, $response->getHttpResponseCode() );
        $this->assertSame( array(), $response->getRawHeaders() );
        $this->assertSame( array(), $response->getHeaders() );
        $this->assertSame(
            "<script language=\"JavaScript\" type=\"text/javascript\">window.location='http://www.test.com/';</script>",
            $response->getBody() );

        $response = new Zend_OpenId_ResponseHelper(false);
        Zend_OpenId::redirect("http://www.test.com/test.php?a=b", null, $response, 'GET');
        $this->assertSame(
            "<script language=\"JavaScript\" type=\"text/javascript\">window.location='http://www.test.com/test.php?a=b';</script>",
            $response->getBody() );

        $response = new Zend_OpenId_ResponseHelper(false);
        Zend_OpenId::redirect("http://www.test.com/test.php", array('a'=>'b'), $response, 'GET');
        $this->assertSame(
            "<script language=\"JavaScript\" type=\"text/javascript\">window.location='http://www.test.com/test.php?a=b';</script>",
            $response->getBody() );

        $response = new Zend_OpenId_ResponseHelper(false);
        Zend_OpenId::redirect("http://www.test.com/test.php", array('a'=>'b','c'=>'d'), $response, 'GET');
        $this->assertSame(
            "<script language=\"JavaScript\" type=\"text/javascript\">window.location='http://www.test.com/test.php?a=b&c=d';</script>",
            $response->getBody() );

        $response = new Zend_OpenId_ResponseHelper(false);
        Zend_OpenId::redirect("http://www.test.com/test.php?a=b", array('c'=>'d'), $response, 'GET');
        $this->assertSame(
            "<script language=\"JavaScript\" type=\"text/javascript\">window.location='http://www.test.com/test.php?a=b&c=d';</script>",
            $response->getBody() );

        $response = new Zend_OpenId_ResponseHelper(false);
        Zend_OpenId::redirect("http://www.test.com/test.php", array('a'=>'x y'), $response, 'GET');
        $this->assertSame(
            "<script language=\"JavaScript\" type=\"text/javascript\">window.location='http://www.test.com/test.php?a=x+y';</script>",
            $response->getBody() );

        $response = new Zend_OpenId_ResponseHelper(true);
        Zend_OpenId::redirect("http://www.test.com/", null, $response, 'POST');
        $this->assertSame( 200, $response->getHttpResponseCode() );
        $this->assertSame( array(), $response->getRawHeaders() );
        $this->assertSame( array(), $response->getHeaders() );
        $this->assertSame(
            "<html><body onLoad=\"document.forms[0].submit();\">\n" .
            "<form method=\"POST\" action=\"http://www.test.com/\">\n" .
            "<input type=\"submit\" value=\"Continue OpenID transaction\">\n" .
            "</form></body></html>\n",
            $response->getBody() );

        $response = new Zend_OpenId_ResponseHelper(true);
        Zend_OpenId::redirect("http://www.test.com/test.php?a=b", array('a'=>'b'), $response, 'POST');
        $this->assertSame(
            "<html><body onLoad=\"document.forms[0].submit();\">\n" .
            "<form method=\"POST\" action=\"http://www.test.com/test.php?a=b\">\n" .
            "<input type=\"hidden\" name=\"a\" value=\"b\">\n" .
            "<input type=\"submit\" value=\"Continue OpenID transaction\">\n" .
            "</form></body></html>\n",
            $response->getBody() );

        $response = new Zend_OpenId_ResponseHelper(true);
        Zend_OpenId::redirect("http://www.test.com/test.php?a=b", array('a'=>'b','c'=>'d'), $response, 'POST');
        $this->assertSame(
            "<html><body onLoad=\"document.forms[0].submit();\">\n" .
            "<form method=\"POST\" action=\"http://www.test.com/test.php?a=b\">\n" .
            "<input type=\"hidden\" name=\"a\" value=\"b\">\n" .
            "<input type=\"hidden\" name=\"c\" value=\"d\">\n" .
            "<input type=\"submit\" value=\"Continue OpenID transaction\">\n" .
            "</form></body></html>\n",
            $response->getBody() );
    }

    /**
     * testing testRedirect
     *
     */
    public function testRandomBytes()
    {
        $this->assertSame( '', Zend_OpenId::randomBytes(0) );
        $x = Zend_OpenId::randomBytes(1);
        $this->assertTrue( is_string($x) );
        $this->assertSame( 1, strlen($x) );
        $x = Zend_OpenId::randomBytes(1024);
        $this->assertTrue( is_string($x) );
        $this->assertSame( 1024, strlen($x) );
    }

    /**
     * testing testDigest
     *
     */
    public function testDigest()
    {
        $this->assertSame(
            'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d',
            bin2hex(Zend_OpenId::digest('sha1',   'hello')) );
        $this->assertSame(
            '2cf24dba5fb0a30e26e83b2ac5b9e29e1b161e5c1fa7425e73043362938b9824',
            bin2hex(Zend_OpenId::digest('sha256', 'hello')) );
    }

    /**
     * testing testHashHmac
     *
     */
    public function testHashHmac()
    {
        $key = 'password';
        $this->assertSame(
            '1f48abc79459fa853af681ddb3c73ff7f35c48fb',
            bin2hex(Zend_OpenId::hashHmac('sha1',   'hello', $key)) );
        $this->assertSame(
            '7ae615e698567e5e1512dd8140e740bd4d65dfa4db195d80ca327de6302b4a63',
            bin2hex(Zend_OpenId::hashHmac('sha256', 'hello', $key)) );
        $key = str_repeat('x',128);
        $this->assertSame(
            '59c6c30dc9fb96b2cb2d7c41dbc6f96d1fbf67ac',
            bin2hex(Zend_OpenId::hashHmac('sha1',   'hello', $key)) );
        $this->assertSame(
            'f5e0c31f7cdd272710052ac3ebcc40d7e82be2427b7e5e1e8373ef1e327515f4',
            bin2hex(Zend_OpenId::hashHmac('sha256', 'hello', $key)) );
    }

    /**
     * testing testCreateDhKey
     *
     */
    public function testCreateDhKey()
    {
        try {
            $dh = Zend_OpenId::createDhKey(
                pack('H*', '0233'),
                pack('H*', '05'),
                pack('H*', '09'));
            $dh_details = Zend_OpenId::getDhKeyDetails($dh);
            $this->assertTrue( is_array($dh_details) );
            $this->assertSame( 4, count($dh_details));
            $this->assertSame( '0233', bin2hex($dh_details['p']) );
            $this->assertSame( '05', bin2hex($dh_details['g']) );
            $this->assertSame( '09', bin2hex($dh_details['priv_key']) );
            $this->assertSame( '4e', bin2hex($dh_details['pub_key']) );

            $dh = Zend_OpenId::createDhKey(
                pack('H*', '0233'),
                pack('H*', '02'),
                pack('H*', '09'));
            $dh_details = Zend_OpenId::getDhKeyDetails($dh);
            $this->assertTrue( is_array($dh_details) );
            $this->assertSame( 4, count($dh_details) );
            $this->assertSame( '0233', bin2hex($dh_details['p']) );
            $this->assertSame( '02', bin2hex($dh_details['g']) );
            $this->assertSame( '09', bin2hex($dh_details['priv_key']) );
            $this->assertSame( '0200', bin2hex($dh_details['pub_key']) );

            $dh = Zend_OpenId::createDhKey(
                pack('H*', '0233'),
                pack('H*', '02'));
            $dh_details = Zend_OpenId::getDhKeyDetails($dh);
            $this->assertTrue( is_array($dh_details) );
            $this->assertSame( 4, count($dh_details) );
            $this->assertSame( '0233', bin2hex($dh_details['p']) );
            $this->assertSame( '02', bin2hex($dh_details['g']) );
            $this->assertTrue( is_string($dh_details['priv_key']) );
            $this->assertTrue( strlen($dh_details['priv_key']) > 0 );
            $this->assertTrue( is_string($dh_details['pub_key']) );
            $this->assertTrue( strlen($dh_details['pub_key']) > 0 );
        } catch (Zend_OpenId_Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    /**
     * testing testComputeDhSecret
     *
     */
    public function testComputeDhSecret()
    {
        try {
            $alice = Zend_OpenId::createDhKey(
                pack('H*', '0233'),
                pack('H*', '05'),
                pack('H*', '09'));
            $alice_details = Zend_OpenId::getDhKeyDetails($alice);
            $this->assertSame( '4e', bin2hex($alice_details['pub_key']) );

            $bob = Zend_OpenId::createDhKey(
                pack('H*', '0233'),
                pack('H*', '05'),
                pack('H*', '0e'));
            $bob_details = Zend_OpenId::getDhKeyDetails($bob);
            $this->assertSame( '0216', bin2hex($bob_details['pub_key']) );

            $this->assertSame( '75',
                bin2hex(Zend_OpenId::computeDhSecret($alice_details['pub_key'], $bob)) );
            $this->assertSame( '75',
                bin2hex(Zend_OpenId::computeDhSecret($bob_details['pub_key'], $alice)) );
        } catch (Zend_OpenId_Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    /**
     * testing testBtwoc
     *
     */
    public function testBtwoc()
    {
        $this->assertSame( '00', bin2hex(Zend_OpenId::btwoc(pack('H*', '00'))) );
        $this->assertSame( '01', bin2hex(Zend_OpenId::btwoc(pack('H*', '01'))) );
        $this->assertSame( '7e', bin2hex(Zend_OpenId::btwoc(pack('H*', '7e'))) );
        $this->assertSame( '78', bin2hex(Zend_OpenId::btwoc(pack('H*', '78'))) );
        $this->assertSame( '0080', bin2hex(Zend_OpenId::btwoc(pack('H*', '80'))) );
        $this->assertSame( '0081', bin2hex(Zend_OpenId::btwoc(pack('H*', '81'))) );
        $this->assertSame( '00fe', bin2hex(Zend_OpenId::btwoc(pack('H*', 'fe'))) );
        $this->assertSame( '00ff', bin2hex(Zend_OpenId::btwoc(pack('H*', 'ff'))) );
    }

    /**
     * testing setSelfUrl
     *
     */
    public function testSetSelfUrl()
    {
        unset($_SERVER['SCRIPT_URI']);
        unset($_SERVER['HTTPS']);
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['SERVER_NAME']);
        unset($_SERVER['SERVER_PORT']);
        unset($_SERVER['SCRIPT_URL']);
        unset($_SERVER['REDIRECT_URL']);
        unset($_SERVER['PHP_SELF']);
        unset($_SERVER['SCRIPT_NAME']);
        unset($_SERVER['PATH_INFO']);
        $_SERVER['SCRIPT_URI'] = "http://www.test.com/";

        $this->assertSame( 'http://www.test.com/', Zend_OpenId::selfUrl() );

        $this->assertSame( null, Zend_OpenId::setSelfUrl("http://localhost/test") );
        $this->assertSame( "http://localhost/test", Zend_OpenId::selfUrl() );

        $this->assertSame( "http://localhost/test", Zend_OpenId::setSelfUrl() );
        $this->assertSame( 'http://www.test.com/', Zend_OpenId::selfUrl() );

        $this->assertSame( null, Zend_OpenId::setSelfUrl() );
        $this->assertSame( 'http://www.test.com/', Zend_OpenId::selfUrl() );
    }
}
