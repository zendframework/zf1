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
 * @package    Zend_Http_Client
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Http/Client.php';

require_once 'Zend/Http/Client/Adapter/Test.php';

/**
 * This Testsuite includes all Zend_Http_Client tests that do not rely
 * on performing actual requests to an HTTP server. These tests can be
 * executed once, and do not need to be tested with different servers /
 * client setups.
 *
 * @category   Zend
 * @package    Zend_Http_Client
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Http
 * @group      Zend_Http_Client
 */
class Zend_Http_Client_StaticTest extends PHPUnit_Framework_TestCase
{
    /**
     * Common HTTP client
     *
     * @var Zend_Http_Client
     */
    protected $_client = null;

    /**
     * Set up the test suite before each test
     *
     */
    public function setUp()
    {
        $this->_client = new Zend_Http_Client_StaticTest_Mock('http://www.example.com');
    }

    /**
     * Clean up after running a test
     *
     */
    public function tearDown()
    {
        $this->_client = null;
    }

    /**
     * URI Tests
     */

    /**
     * Test we can SET and GET a URI as string
     *
     */
    public function testSetGetUriString()
    {
        $uristr = 'http://www.zend.com:80/';

        $this->_client->setUri($uristr);

        $uri = $this->_client->getUri();
        $this->assertTrue($uri instanceof Zend_Uri_Http, 'Returned value is not a Uri object as expected');
        $this->assertEquals($uri->__toString(), $uristr, 'Returned Uri object does not hold the expected URI');

        $uri = $this->_client->getUri(true);
        $this->assertTrue(is_string($uri), 'Returned value expected to be a string, ' . gettype($uri) . ' returned');
        $this->assertEquals($uri, $uristr, 'Returned string is not the expected URI');
    }

    /**
     * Test we can SET and GET a URI as object
     *
     */
    public function testSetGetUriObject()
    {
        $uriobj = Zend_Uri::factory('http://www.zend.com:80/');

        $this->_client->setUri($uriobj);

        $uri = $this->_client->getUri();
        $this->assertTrue($uri instanceof Zend_Uri_Http, 'Returned value is not a Uri object as expected');
        $this->assertEquals($uri, $uriobj, 'Returned object is not the excepted Uri object');
    }

    /**
     * Test that passing an invalid URI string throws an exception
     *
     * @expectedException Zend_Uri_Exception
     */
    public function testInvalidUriStringException()
    {
        $this->_client->setUri('httpp://__invalid__.com');
    }

    /**
     * Test that passing an invalid URI object throws an exception
     *
     */
    public function testInvalidUriObjectException()
    {
        try {
            $uri = Zend_Uri::factory('mailto:nobody@example.com');
            $this->_client->setUri($uri);
            $this->fail('Excepted invalid URI object exception was not thrown');
        } catch (Zend_Http_Client_Exception $e) {
            // We're good
        } catch (Zend_Uri_Exception $e) {
            // URI is currently unimplemented
            $this->markTestIncomplete('Zend_Uri_Mailto is not implemented yet');
        }
    }

    /**
     * Test that setting the same parameter twice in the query string does not
     * get reduced to a single value only.
     *
     */
    public function testDoubleGetParameter()
    {
        $qstr = 'foo=bar&foo=baz';

        $this->_client->setUri('http://example.com/test/?' . $qstr);
        $this->_client->setAdapter('Zend_Http_Client_Adapter_Test');

        $res = $this->_client->request('GET');
        $this->assertContains($qstr, $this->_client->getLastRequest(),
            'Request is expected to contain the entire query string');
    }

    /**
     * Header Tests
     */

    /**
     * Make sure an exception is thrown if an invalid header name is used
     *
     * @expectedException Zend_Http_Client_Exception
     */
    public function testInvalidHeaderExcept()
    {
        $this->_client->setHeaders('Ina_lid* Hea%der', 'is not good');
    }

    /**
     * Make sure non-strict mode disables header name validation
     *
     */
    public function testInvalidHeaderNonStrictMode()
    {
        // Disable strict validation
        $this->_client->setConfig(array('strict' => false));

        try {
            $this->_client->setHeaders('Ina_lid* Hea%der', 'is not good');
        } catch (Zend_Http_Client_Exception $e) {
            $this->fail('Invalid header names should be allowed in non-strict mode');
        }
    }

    /**
     * Test we can get already set headers
     *
     */
    public function testGetHeader()
    {
        $this->_client->setHeaders(array(
            'Accept-encoding' => 'gzip,deflate',
            'Accept-language' => 'en,de,*',
        ));

        $this->assertEquals($this->_client->getHeader('Accept-encoding'), 'gzip,deflate', 'Returned value of header is not as expected');
        $this->assertEquals($this->_client->getHeader('X-Fake-Header'), null, 'Non-existing header should not return a value');
    }

    public function testUnsetHeader()
    {
        $this->_client->setHeaders('Accept-Encoding', 'gzip,deflate');
        $this->_client->setHeaders('Accept-Encoding', null);
        $this->assertNull($this->_client->getHeader('Accept-encoding'), 'Returned value of header is expected to be null');
    }

    /**
     * Authentication tests
     */

    /**
     * Test setAuth (dynamic method) fails when trying to use an unsupported
     * authentication scheme
     *
     * @expectedException Zend_Http_Client_Exception
     */
    public function testExceptUnsupportedAuthDynamic()
    {
        $this->_client->setAuth('shahar', '1234', 'SuperStrongAlgo');
    }

    /**
     * Test encodeAuthHeader (static method) fails when trying to use an
     * unsupported authentication scheme
     *
     * @expectedException Zend_Http_Client_Exception
     */
    public function testExceptUnsupportedAuthStatic()
    {
        Zend_Http_Client::encodeAuthHeader('shahar', '1234', 'SuperStrongAlgo');
    }

    /**
     * Cookie and Cookie Jar tests
     */

    /**
     * Test we can properly set a new cookie jar
     *
     */
    public function testSetNewCookieJar()
    {
        $this->_client->setCookieJar();
        $this->_client->setCookie('cookie', 'value');
        $this->_client->setCookie('chocolate', 'chips');
        $jar = $this->_client->getCookieJar();

        // Check we got the right cookiejar
        $this->assertTrue($jar instanceof Zend_Http_CookieJar, '$jar is not an instance of Zend_Http_CookieJar as expected');
        $this->assertEquals(count($jar->getAllCookies()), 2, '$jar does not contain 2 cookies as expected');
    }

    /**
     * Test we can properly set an existing cookie jar
     *
     */
    public function testSetReadyCookieJar()
    {
        $jar = new Zend_Http_CookieJar();
        $jar->addCookie('cookie=value', 'http://www.example.com');
        $jar->addCookie('chocolate=chips; path=/foo', 'http://www.example.com');

        $this->_client->setCookieJar($jar);

        // Check we got the right cookiejar
        $this->assertEquals($jar, $this->_client->getCookieJar(), '$jar is not the client\'s cookie jar as expected');
    }

    /**
     * Test we can unset a cookie jar
     *
     */
    public function testUnsetCookieJar()
    {
        // Set the cookie jar just like in testSetNewCookieJar
        $this->_client->setCookieJar();
        $this->_client->setCookie('cookie', 'value');
        $this->_client->setCookie('chocolate', 'chips');
        $jar = $this->_client->getCookieJar();

        // Try unsetting the cookiejar
        $this->_client->setCookieJar(null);

        $this->assertNull($this->_client->getCookieJar(), 'Cookie jar is expected to be null but it is not');
    }

    /**
     * Make sure using an invalid cookie jar object throws an exception
     *
     * @expectedException Zend_Http_Client_Exception
     */
    public function testSetInvalidCookieJar()
    {
        $this->_client->setCookieJar('cookiejar');
    }

    /**
     * Test that when the encodecookies flag is set to FALSE, cookies captured
     * from a response by Zend_Http_CookieJar are not encoded
     *
     * @group ZF-1850
     */
    public function testCaptureCookiesNoEncodeZF1850()
    {
        $cookieName = "cookieWithSpecialChars";
        $cookieValue = "HID=XXXXXX&UN=XXXXXXX&UID=XXXXX";

        $adapter = new Zend_Http_Client_Adapter_Test();
        $adapter->setResponse(
        	"HTTP/1.0 200 OK\r\n" .
            "Content-type: text/plain\r\n" .
            "Content-length: 2\r\n" .
            "Connection: close\r\n" .
            "Set-Cookie: $cookieName=$cookieValue; path=/\r\n" .
            "\r\n" .
            "OK"
        );

        $this->_client->setUri('http://example.example/test');
        $this->_client->setConfig(array(
            'adapter'       => $adapter,
            'encodecookies' => false
        ));

        $this->_client->setCookieJar();

        // First request is expected to set the cookie
        $this->_client->request();

        // Next request should contain the cookie
        $this->_client->request();

        $request = $this->_client->getLastRequest();
        if (! preg_match("/^Cookie: $cookieName=([^;]+)/m", $request, $match)) {
            $this->fail("Could not find cookie in request");
        }

        $this->assertEquals($cookieValue, $match[1]);
    }

    /**
     * Configuration Handling
     */

    /**
     * Test that we can set a valid configuration array with some options
     *
     */
    public function testConfigSetAsArray()
    {
        $config = array(
            'timeout'    => 500,
            'someoption' => 'hasvalue'
        );

        $this->_client->setConfig($config);

        $hasConfig = $this->_client->config;
        foreach($config as $k => $v) {
            $this->assertEquals($v, $hasConfig[$k]);
        }
    }

    /**
     * Test that a Zend_Config object can be used to set configuration
     *
     * @link http://framework.zend.com/issues/browse/ZF-5577
     */
    public function testConfigSetAsZendConfig()
    {
        require_once 'Zend/Config.php';

        $config = new Zend_Config(array(
            'timeout'  => 400,
            'nested'   => array(
                'item' => 'value',
            )
        ));

        $this->_client->setConfig($config);

        $hasConfig = $this->_client->config;
        $this->assertEquals($config->timeout, $hasConfig['timeout']);
        $this->assertEquals($config->nested->item, $hasConfig['nested']['item']);
    }

    /**
     * Test that passing invalid variables to setConfig() causes an exception
     *
     * @dataProvider      invalidConfigProvider
     * @expectedException Zend_Http_Client_Exception
     */
    public function testConfigSetInvalid($config)
    {
        $this->_client->setConfig($config);
    }

    /**
     * Test that configuration options are passed to the adapter after the
     * adapter is instantiated
     *
     * @link http://framework.zend.com/issues/browse/ZF-4557
     */
    public function testConfigPassToAdapterZF4557()
    {
        $adapter = new Zend_Http_Client_StaticTest_TestAdapter_Mock();

        // test that config passes when we set the adapter
        $this->_client->setConfig(array('param' => 'value1'));
        $this->_client->setAdapter($adapter);
        $adapterCfg = $adapter->config;
        $this->assertEquals('value1', $adapterCfg['param']);

        // test that adapter config value changes when we set client config
        $this->_client->setConfig(array('param' => 'value2'));
        $adapterCfg = $adapter->config;
        $this->assertEquals('value2', $adapterCfg['param']);
    }

    /**
     * Other Tests
     */

    /**
     * Test the getLastResponse() method actually returns the last response
     *
     */
    public function testGetLastResponse()
    {
        // First, make sure we get null before the request
        $this->assertEquals(null, $this->_client->getLastResponse(),
            'getLastResponse() is still expected to return null');

        // Now, test we get a proper response after the request
        $this->_client->setUri('http://example.com/foo/bar');
        $this->_client->setAdapter('Zend_Http_Client_Adapter_Test');

        $response = $this->_client->request();
        $this->assertTrue(($response === $this->_client->getLastResponse()),
            'Response is expected to be identical to the result of getLastResponse()');
    }

    /**
     * Test that getLastResponse returns null when not storing
     *
     */
    public function testGetLastResponseWhenNotStoring()
    {
        // Now, test we get a proper response after the request
        $this->_client->setUri('http://example.com/foo/bar');
        $this->_client->setAdapter('Zend_Http_Client_Adapter_Test');
        $this->_client->setConfig(array('storeresponse' => false));

        $response = $this->_client->request();

        $this->assertNull($this->_client->getLastResponse(),
            'getLastResponse is expected to be null when not storing');
    }

    /**
     * Check we get an exception when trying to send a POST request with an
     * invalid content-type header
     *
     * @expectedException Zend_Http_Client_Exception
     */
    public function testInvalidPostContentType()
    {
        $this->_client->setEncType('x-foo/something-fake');
        $this->_client->setParameterPost('parameter', 'value');

        // This should throw an exception
        $this->_client->request('POST');
    }

    /**
     * Check we get an exception if there's an error in the socket
     *
     * @expectedException Zend_Http_Client_Adapter_Exception
     */
    public function testSocketErrorException()
    {
        // Try to connect to an invalid host
        $this->_client->setUri('http://255.255.255.255');

        // Reduce timeout to 3 seconds to avoid waiting
        $this->_client->setConfig(array('timeout' => 3));

        // This call should cause an exception
        $this->_client->request();
    }

    /**
     * Check that we can set methods which are not documented in the RFC.
     *
     * @dataProvider validMethodProvider
     */
    public function testSettingExtendedMethod($method)
    {
        try {
            $this->_client->setMethod($method);
        } catch (Exception $e) {
            $this->fail("An unexpected exception was thrown when setting request method to '{$method}'");
        }
    }

    /**
     * Check that an exception is thrown if non-word characters are used in
     * the request method.
     *
     * @dataProvider invalidMethodProvider
     * @expectedException Zend_Http_Client_Exception
     */
    public function testSettingInvalidMethodThrowsException($method)
    {
        $this->_client->setMethod($method);
    }

    /**
     * Test that POST data with mutli-dimentional array is properly encoded as
     * multipart/form-data
     *
     */
    public function testFormDataEncodingWithMultiArrayZF7038()
    {
        $this->_client->setAdapter('Zend_Http_Client_Adapter_Test');
        $this->_client->setUri('http://example.com');
        $this->_client->setEncType(Zend_Http_Client::ENC_FORMDATA);

        $this->_client->setParameterPost('test', array(
            'v0.1',
            'v0.2',
            'k1' => 'v1.0',
            'k2' => array(
                'v2.1',
                'k2.1' => 'v2.1.0'
            )
        ));

        $this->_client->request('POST');

        $expectedLines = file(dirname(__FILE__) . '/_files/ZF7038-multipartarrayrequest.txt');
        $gotLines = explode("\n", $this->_client->getLastRequest());

        $this->assertEquals(count($expectedLines), count($gotLines));

        while (($expected = array_shift($expectedLines)) &&
               ($got = array_shift($gotLines))) {

            $expected = trim($expected);
            $got = trim($got);
            $this->assertRegExp("/^$expected$/", $got);
        }
    }

    /**
     * @group ZF-4236
     */
    public function testFormFileUpload()
    {
        $this->_client->setAdapter('Zend_Http_Client_Adapter_Test');
        $this->_client->setUri('http://example.com');
        $this->_client->setFileUpload('testFile.name', 'testFile', 'TESTDATA12345', 'text/plain');
        $this->_client->request('POST');

        $expectedLines = file(dirname(__FILE__) . '/_files/ZF4236-fileuploadrequest.txt');
        $gotLines = explode("\n", trim($this->_client->getLastRequest()));

        $this->assertEquals(count($expectedLines), count($gotLines));
        while (($expected = array_shift($expectedLines)) &&
               ($got = array_shift($gotLines))) {

            $expected = trim($expected);
            $got = trim($got);
            $this->assertRegExp("/^$expected$/", $got);
        }
    }

    /**
     * @group ZF-4236
     */
    public function testClientBodyRetainsFieldOrdering()
    {
        $this->_client->setAdapter('Zend_Http_Client_Adapter_Test');
        $this->_client->setUri('http://example.com');
        $this->_client->setParameterPost('testFirst', 'foo');
        $this->_client->setFileUpload('testFile.name', 'testFile', 'TESTDATA12345', 'text/plain');
        $this->_client->setParameterPost('testLast', 'bar');
        $this->_client->request('POST');

        $expectedLines = file(dirname(__FILE__) . '/_files/ZF4236-clientbodyretainsfieldordering.txt');
        $gotLines = explode("\n", trim($this->_client->getLastRequest()));

        $this->assertEquals(count($expectedLines), count($gotLines));
        while (($expected = array_shift($expectedLines)) &&
               ($got = array_shift($gotLines))) {

            $expected = trim($expected);
            $got = trim($got);
            $this->assertRegExp("/^$expected$/", $got);
        }
    }

    /**
     * Test that we properly calculate the content-length of multibyte-encoded
     * request body
     *
     * This may file in case that mbstring overloads the substr and strlen
     * functions, and the mbstring internal encoding is a multibyte encoding.
     *
     * @link http://framework.zend.com/issues/browse/ZF-2098
     */
    public function testMultibyteRawPostDataZF2098()
    {
        $this->_client->setAdapter('Zend_Http_Client_Adapter_Test');
        $this->_client->setUri('http://example.com');

        $bodyFile = dirname(__FILE__) . '/_files/ZF2098-multibytepostdata.txt';

        $this->_client->setRawData(file_get_contents($bodyFile), 'text/plain');
        $this->_client->request('POST');
        $request = $this->_client->getLastRequest();

        if (! preg_match('/^content-length:\s+(\d+)/mi', $request, $match)) {
            $this->fail("Unable to find content-length header in request");
        }

        $this->assertEquals(filesize($bodyFile), (int) $match[1]);
    }

    /**
     * @group ZF-8057
     */
    public function testSetDisabledAuthBeforSettingUriBug()
    {
        $client = new Zend_Http_Client_StaticTest_Mock();
        // if the bug exists this call should creates a fatal error
        $client->setAuth(false);
    }

	/**
     * Testing if the connection isn't closed
     *
     * @group ZF-9685
     */
    public function testOpenTempStreamWithValidFileDoesntThrowsException()
    {
    	$url = 'http://www.example.com';
    	$config = array (
			'output_stream' => realpath(dirname(__FILE__) . '/_files/zend_http_client_stream.file'),
		);
		$client = new Zend_Http_Client($url, $config);
		try {
			$result = $client->request();
		} catch (Zend_Http_Client_Exception $e) {
			$this->fail('Unexpected exception was thrown');
		}
		// we can safely return until we can verify link is still active
		// @todo verify link is still active
		return;
    }

    /**
     * Testing if the connection can be closed
     *
     * @group ZF-9685
     */
    public function testOpenTempStreamWithBogusFileClosesTheConnection()
    {
    	$url = 'http://www.example.com';
    	$config = array (
			'output_stream' => '/path/to/bogus/file.ext',
		);
		$client = new Zend_Http_Client($url, $config);
		try {
			$result = $client->request();
			$this->fail('Expected exception was not thrown');
		} catch (Zend_Http_Client_Exception $e) {
			// we return since we expect the exception
			return;
		}
    }

	/**
     * Test that we can handle trailing space in location header
     *
     * @group ZF-11283
     * @link http://framework.zend.com/issues/browse/ZF-11283
     */
    public function testRedirectWithTrailingSpaceInLocationHeaderZF11283()
    {
        $this->_client->setUri('http://example.com/');
        $this->_client->setAdapter('Zend_Http_Client_Adapter_Test');

        $adapter = $this->_client->getAdapter(); /* @var $adapter Zend_Http_Client_Adapter_Test */

        $adapter->setResponse(<<<RESPONSE
HTTP/1.1 302 Redirect
Content-Type: text/html; charset=UTF-8
Location: /test
Server: Microsoft-IIS/7.0
Date: Tue, 19 Apr 2011 11:23:48 GMT

RESPONSE
        );

        $res = $this->_client->request('GET');

        $lastUri = $this->_client->getUri();

        $this->assertEquals("/test", $lastUri->getPath());
    }

    /**
     * @group ZF-11162
     */
    function testClientDoesNotModifyPassedUri() {
        $uri = Zend_Uri_Http::fromString('http://example.org/');
        $orig = clone $uri;
        $client = new Zend_Http_Client($uri);
        $this->assertEquals((string)$orig, (string)$uri);
    }
    /*
     * @group ZF-9206
     */
    function testStreamWarningRewind()
    {
        $httpClient = new Zend_Http_Client();
        $httpClient->setUri('http://example.org');
        $httpClient->setMethod(Zend_Http_Client::GET);
        ob_start();
        $httpClient->setStream('php://output')->request();
        ob_clean();
    }
    /**
     * Data providers
     */

    /**
     * Data provider of valid non-standard HTTP methods
     *
     * @return array
     */
    static public function validMethodProvider()
    {
        return array(
            array('OPTIONS'),
            array('POST'),
            array('DOSOMETHING'),
            array('PROPFIND'),
            array('Some_Characters'),
            array('X-MS-ENUMATTS')
        );
    }

    /**
     * Data provider of invalid HTTP methods
     *
     * @return array
     */
    static public function invalidMethodProvider()
    {
        return array(
            array('N@5TYM3T#0D'),
            array('TWO WORDS'),
            array('GET http://foo.com/?'),
            array("Injected\nnewline")
        );
    }

    /**
     * Data provider for invalid configuration containers
     *
     * @return array
     */
    static public function invalidConfigProvider()
    {
        return array(
            array(false),
            array('foo => bar'),
            array(null),
            array(new stdClass),
            array(55)
        );
    }
}

class Zend_Http_Client_StaticTest_Mock extends Zend_Http_Client
{
    public $config = array(
        'maxredirects'    => 5,
        'strictredirects' => false,
        'useragent'       => 'Zend_Http_Client',
        'timeout'         => 10,
        'adapter'         => 'Zend_Http_Client_Adapter_Socket',
        'httpversion'     => self::HTTP_1,
        'keepalive'       => false,
        'storeresponse'   => true,
        'strict'          => true,
        'output_stream'   => false,
        'encodecookies'   => true,
    );
}

class Zend_Http_Client_StaticTest_TestAdapter_Mock extends Zend_Http_Client_Adapter_Test
{
    public $config = array();
}
