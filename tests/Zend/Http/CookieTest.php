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
 * @package    Zend_Http_Cookie
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Http/Cookie.php';

/**
 * Zend_Http_Cookie unit tests
 *
 * @category   Zend
 * @package    Zend_Http_Cookie
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Http
 * @group      Zend_Http_Cookie
 */
class Zend_Http_CookieTest extends PHPUnit_Framework_TestCase
{
    /**
     * Cookie creation and data accessors tests
     */

    /**
     * Make sure we can't set invalid names
     *
     * @dataProvider invalidCookieNameCharProvider
     * @expectedException Zend_Http_Exception
     */
    public function testSetInvalidName($char)
    {
        $cookie = new Zend_Http_Cookie("cookie_$char", 'foo', 'example.com');
    }

    /**
     * Test we get the cookie name properly
     *
     * @dataProvider validCookieWithInfoProvider
     */
    public function testGetName($cStr, $cInfo)
    {
        $cookie = Zend_Http_Cookie::fromString($cStr);
        if (! $cookie instanceof Zend_Http_Cookie) {
            $this->fail("Failed creating a cookie object from '$cStr'");
        }

        if (isset($cInfo['name'])) {
            $this->assertEquals($cInfo['name'], $cookie->getName());
        }
    }

    /**
     * Make sure we get the correct value if it was set through the constructor
     *
     * @param        string $value
     * @dataProvider validCookieValueProvider
     */
    public function testGetValueConstructor($val)
    {
        $cookie = new Zend_Http_Cookie('cookie', $val, 'example.com', time(), '/', true);
        $this->assertEquals($val, $cookie->getValue());
    }

    /**
     * Make sure we get the correct value if it was set through fromString()
     *
     * @param        string $value
     * @dataProvider validCookieValueProvider
     */
    public function testGetValueFromString($val)
    {
        $cookie = Zend_Http_Cookie::fromString('cookie=' . urlencode($val) . '; domain=example.com');
        $this->assertEquals($val, $cookie->getValue());
    }

    /**
     * Make sure we get the correct value if it was set through fromString()
     *
     * @param        string $value
     * @dataProvider validCookieValueProvider
     */
    public function testGetRawValueFromString($val)
    {
        // Because ';' has special meaning in the cookie, strip it out for this test.
        $val = str_replace(';', '', $val);
        $cookie = Zend_Http_Cookie::fromString('cookie=' . $val . '; domain=example.com', null, false);
        $this->assertEquals($val, $cookie->getValue());
    }

    /**
     * Make sure we get the correct value if it was set through fromString()
     *
     * @param        string $value
     * @dataProvider validCookieValueProvider
     */
    public function testGetRawValueFromStringToString($val)
    {
        // Because ';' has special meaning in the cookie, strip it out for this test.
        $val = str_replace(';', '', $val);
        $cookie = Zend_Http_Cookie::fromString('cookie=' . $val . '; domain=example.com', null, false);
        $this->assertEquals('cookie=' . $val . ';', (string)$cookie);
    }

    /**
     * Make sure we get the correct value if it was set through fromString()
     *
     * @param        string $value
     * @dataProvider validCookieValueProvider
     */
    public function testGetValueFromStringEncodedToString($val)
    {
        // Because ';' has special meaning in the cookie, strip it out for this test.
        $val = str_replace(';', '', $val);
        $cookie = Zend_Http_Cookie::fromString('cookie=' . $val . '; domain=example.com', null, true);
        $this->assertEquals('cookie=' . urlencode($val) . ';', (string)$cookie);
    }

    /**
     * Make sure we get the correct domain when it's set in the cookie string
     *
     * @dataProvider validCookieWithInfoProvider
     */
    public function testGetDomainInStr($cStr, $cInfo)
    {
        $cookie = Zend_Http_Cookie::fromString($cStr);
        if (! $cookie instanceof Zend_Http_Cookie) {
            $this->fail("Failed creating a cookie object from '$cStr'");
        }

        if (isset($cInfo['domain'])) {
            $this->assertEquals($cInfo['domain'], $cookie->getDomain());
        }
    }

    /**
     * Make sure we get the correct domain when it's set in a reference URL
     *
     * @dataProvider refUrlProvider
     */
    public function testGetDomainInRefUrl(Zend_Uri $uri)
    {
        $domain = $uri->getHost();
        $cookie = Zend_Http_Cookie::fromString('foo=baz; path=/', 'http://' . $domain);
        if (! $cookie instanceof Zend_Http_Cookie) {
            $this->fail("Failed creating a cookie object with URL '$uri'");
        }

        $this->assertEquals($domain, $cookie->getDomain());
    }

    /**
     * Make sure we get the correct path when it's set in the cookie string
     *
     * @dataProvider validCookieWithInfoProvider
     */
    public function testGetPathInStr($cStr, $cInfo)
    {
        $cookie = Zend_Http_Cookie::fromString($cStr);
        if (! $cookie instanceof Zend_Http_Cookie) {
            $this->fail("Failed creating a cookie object from '$cStr'");
        }

        if (isset($cInfo['path'])) {
            $this->assertEquals($cInfo['path'], $cookie->getPath());
        }
    }

    /**
     * Make sure we get the correct path when it's set a reference URL
     *
     * @dataProvider refUrlProvider
     */
    public function testGetPathInRefUrl(Zend_Uri $uri)
    {
        $path = $uri->getPath();
        if (substr($path, -1, 1) == '/') $path .= 'x';
        $path = dirname($path);
        if ($path == DIRECTORY_SEPARATOR) {
            $path = '/';
        }

        $cookie = Zend_Http_Cookie::fromString('foo=bar', (string) $uri);
        if (! $cookie instanceof Zend_Http_Cookie) {
            $this->fail("Failed creating a cookie object with URL '$uri'");
        }

        $this->assertEquals($path, $cookie->getPath());
    }

    /**
     * Test we get the correct expiry time
     *
     * @dataProvider validCookieWithInfoProvider
     */
    public function testGetExpiryTime($cStr, $cInfo)
    {
        $cookie = Zend_Http_Cookie::fromString($cStr);
        if (! $cookie instanceof Zend_Http_Cookie) {
            $this->fail("Failed creating a cookie object from '$cStr'");
        }

        if (isset($cInfo['expires'])) {
            $this->assertEquals($cInfo['expires'], $cookie->getExpiryTime());
        }
    }

    /**
     * Make sure the "is secure" flag is correctly set
     *
     * @dataProvider validCookieWithInfoProvider
     */
    public function testIsSecure($cStr, $cInfo)
    {
        $cookie = Zend_Http_Cookie::fromString($cStr);
        if (! $cookie instanceof Zend_Http_Cookie) {
            $this->fail("Failed creating a cookie object from '$cStr'");
        }

        if (isset($cInfo['secure'])) {
            $this->assertEquals($cInfo['secure'], $cookie->isSecure());
        }
    }

    /**
     * Cookie expiry time tests
     */

    /**
     * Make sure we get the correct value for 'isExpired'
     *
     * @dataProvider cookieWithExpiredFlagProvider
     */
    public function testIsExpired($cStr, $expired)
    {
        $cookie = Zend_Http_Cookie::fromString($cStr);
        if (! $cookie) {
            $this->fail("Failed creating a cookie object from '$cStr'");
        }
        $this->assertEquals($expired, $cookie->isExpired());
    }

    /**
     * Make sure we get the correct value for 'isExpired', when time is manually set
     */
    public function testIsExpiredDifferentTime()
    {
        $notexpired = time() + 3600;
        $expired = time() - 3600;
        $now = time() + 7200;

        $cookies = array(
            'cookie=foo; domain=example.com; expires=' . date(DATE_COOKIE, $notexpired),
            'cookie=foo; domain=example.com; expires=' . date(DATE_COOKIE, $expired)
        );

        // Make sure all cookies are expired
        foreach ($cookies as $cstr) {
            $cookie = Zend_Http_Cookie::fromString($cstr);
            if (! $cookie) $this->fail('Got no cookie object from a valid cookie string');
            $this->assertTrue($cookie->isExpired($now), 'Cookie is expected to be expired');
        }

        // Make sure all cookies are not expired
        $now = time() - 7200;
        foreach ($cookies as $cstr) {
            $cookie = Zend_Http_Cookie::fromString($cstr);
            if (! $cookie) $this->fail('Got no cookie object from a valid cookie string');
            $this->assertFalse($cookie->isExpired($now), 'Cookie is expected not to be expired');
        }
    }

    /**
     * Test we can properly check if a cookie is a session cookie (has no expiry time)
     *
     * @dataProvider validCookieWithInfoProvider
     */
    public function testIsSessionCookie($cStr, $cInfo)
    {
        $cookie = Zend_Http_Cookie::fromString($cStr);
        if (! $cookie instanceof Zend_Http_Cookie) {
            $this->fail("Failed creating a cookie object from '$cStr'");
        }

        if (array_key_exists('expires', $cInfo)) {
            $this->assertEquals(($cInfo['expires'] === null), $cookie->isSessionCookie());
        }
    }


    /**
     * Make sure cookies are properly converted back to strings
     *
     * @dataProvider validCookieWithInfoProvider
     */
    public function testToString($cStr, $cInfo)
    {
        $cookie = Zend_Http_Cookie::fromString($cStr);
        if (! $cookie instanceof Zend_Http_Cookie) {
            $this->fail("Failed creating a cookie object from '$cStr'");
        }

        $expected = substr($cStr, 0, strpos($cStr, ';') + 1);
        $this->assertEquals($expected, (string) $cookie);
    }

    public function testGarbageInStrIsIgnored()
    {
        $cookies = array(
            'name=value; domain=foo.com; silly=place; secure',
            'foo=value; someCrap; secure; domain=foo.com; ',
            'anothercookie=value; secure; has some crap; ignore=me; domain=foo.com; '
        );

        foreach ($cookies as $cstr) {
            $cookie = Zend_Http_Cookie::fromString($cstr);
            if (! $cookie) $this->fail('Got no cookie object from a valid cookie string');
            $this->assertEquals('value', $cookie->getValue(), 'Value is not as expected');
            $this->assertEquals('foo.com', $cookie->getDomain(), 'Domain is not as expected');
            $this->assertTrue($cookie->isSecure(), 'Cookie is expected to be secure');
        }
    }

    /**
     * Test the match() method against a domain
     *
     * @dataProvider domainMatchTestProvider
     */
    public function testMatchDomain($cookieStr, $uri, $match)
    {
        $cookie = Zend_Http_Cookie::fromString($cookieStr);
        $this->assertEquals($match, $cookie->match($uri));
    }

    static public function domainMatchTestProvider()
    {
        $uri = Zend_Uri::factory('http://www.foo.com/some/file.txt');

        return array(
            array('foo=bar; domain=.example.com;', 'http://www.example.com/foo/bar.php', true),
            array('foo=bar; domain=.example.com;', 'http://example.com/foo/bar.php', true),
            array('foo=bar; domain=.example.com;', 'http://www.somexample.com/foo/bar.php', false),
            array('foo=bar; domain=example.com;', 'http://www.somexample.com/foo/bar.php', false),
            array('cookie=value; domain=www.foo.com', $uri, true),
            array('cookie=value; domain=www.foo.com', 'http://il.www.foo.com', true),
            array('cookie=value; domain=www.foo.com', 'http://bar.foo.com', false)
        );
    }

    /**
     * Test the match() method against a domain
     *
     */
    public function testMatchPath()
    {
        $cookie = Zend_Http_Cookie::fromString('foo=bar; domain=.example.com; path=/foo');
        $this->assertTrue($cookie->match('http://www.example.com/foo/bar.php'), 'Cookie expected to match, but didn\'t');
        $this->assertFalse($cookie->match('http://www.example.com/bar.php'), 'Cookie expected not to match, but did');

        $cookie = Zend_Http_Cookie::fromString('cookie=value; domain=www.foo.com; path=/some/long/path');
        $this->assertTrue($cookie->match('http://www.foo.com/some/long/path/file.txt'), 'Cookie expected to match, but didn\'t');
        $this->assertTrue($cookie->match('http://www.foo.com/some/long/path/and/even/more'), 'Cookie expected to match, but didn\'t');
        $this->assertFalse($cookie->match('http://www.foo.com/some/long/file.txt'), 'Cookie expected not to match, but did');
        $this->assertFalse($cookie->match('http://www.foo.com/some/different/path/file.txt'), 'Cookie expected not to match, but did');
    }

    /**
     * Test the match() method against secure / non secure connections
     *
     */
    public function testMatchSecure()
    {
        // A non secure cookie, should match both
        $cookie = Zend_Http_Cookie::fromString('foo=bar; domain=.example.com;');
        $this->assertTrue($cookie->match('http://www.example.com/foo/bar.php'), 'Cookie expected to match, but didn\'t');
        $this->assertTrue($cookie->match('https://www.example.com/bar.php'), 'Cookie expected to match, but didn\'t');

        // A secure cookie, should match secure connections only
        $cookie = Zend_Http_Cookie::fromString('foo=bar; domain=.example.com; secure');
        $this->assertFalse($cookie->match('http://www.example.com/foo/bar.php'), 'Cookie expected not to match, but it did');
        $this->assertTrue($cookie->match('https://www.example.com/bar.php'), 'Cookie expected to match, but didn\'t');
    }

    /**
     * Test the match() method against different expiry times
     *
     */
    public function testMatchExpire()
    {
        // A session cookie - should always be valid
        $cookie = Zend_Http_Cookie::fromString('foo=bar; domain=.example.com;');
        $this->assertTrue($cookie->match('http://www.example.com/'), 'Cookie expected to match, but didn\'t');
        $this->assertTrue($cookie->match('http://www.example.com/', true, time() + 3600), 'Cookie expected to match, but didn\'t');

        // A session cookie, should not match
        $this->assertFalse($cookie->match('https://www.example.com/', false), 'Cookie expected not to match, but it did');
        $this->assertFalse($cookie->match('https://www.example.com/', false, time() - 3600), 'Cookie expected not to match, but it did');

        // A cookie with expiry time in the future
        $cookie = Zend_Http_Cookie::fromString('foo=bar; domain=.example.com; expires=' . date(DATE_COOKIE, time() + 3600));
        $this->assertTrue($cookie->match('http://www.example.com/'), 'Cookie expected to match, but didn\'t');
        $this->assertFalse($cookie->match('https://www.example.com/', true, time() + 7200), 'Cookie expected not to match, but it did');

        // A cookie with expiry time in the past
        $cookie = Zend_Http_Cookie::fromString('foo=bar; domain=.example.com; expires=' . date(DATE_COOKIE, time() - 3600));
        $this->assertFalse($cookie->match('http://www.example.com/'), 'Cookie expected not to match, but it did');
        $this->assertTrue($cookie->match('https://www.example.com/', true, time() - 7200), 'Cookie expected to match, but didn\'t');
    }

    public function testFromStringFalse()
    {
        $cookie = Zend_Http_Cookie::fromString('foo; domain=www.exmaple.com');
        $this->assertEquals(false, $cookie, 'fromString was expected to fail and return false');

        $cookie = Zend_Http_Cookie::fromString('=bar; secure; domain=foo.nl');
        $this->assertEquals(false, $cookie, 'fromString was expected to fail and return false');

        $cookie = Zend_Http_Cookie::fromString('fo;o=bar; secure; domain=foo.nl');
        $this->assertEquals(false, $cookie, 'fromString was expected to fail and return false');
    }

    /**
     * Test that cookies with far future expiry date (beyond the 32 bit unsigned int range) are
     * not mistakenly marked as 'expired'
     *
     * @link http://framework.zend.com/issues/browse/ZF-5690
     */
    public function testZF5690OverflowingExpiryDate()
    {
        $expTime = "Sat, 29-Jan-2039 00:54:42 GMT";
        $cookie = Zend_Http_Cookie::fromString("foo=bar; domain=.example.com; expires=$expTime");
        $this->assertFalse($cookie->isExpired(), 'Expiry: ' . $cookie->getExpiryTime());
    }

    /**
     * @group ZF-10506
     */
    public function testPregMatchIsQuoted()
    {
        $this->assertFalse(Zend_Http_Cookie::matchCookieDomain('foo.bar.com', 'www.foozbar.com'));
    }

    /**
     * Data Providers
     */

    /**
     * Provide characters which are invalid in cookie names
     *
     * @return array
     */
    static public function invalidCookieNameCharProvider()
    {
        return array(
            array("="),
            array(","),
            array(";"),
            array("\t"),
            array("\r"),
            array("\n"),
            array("\013"),
            array("\014")
        );
    }

    /**
     * Provide valid cookie values
     *
     * @return array
     */
    static public function validCookieValueProvider()
    {
        return array(
            array('simpleCookie'),
            array('space cookie'),
            array('!@#$%^*&()* ][{}?;'),
            array("line\n\rbreaks"),
            array("0000j8CydACPu_-J9bE8uTX91YU:12a83ks4k"), // value from: Alexander Cheshchevik's comment on issue: ZF-1850

            // Long cookie value - 2kb
            array(str_repeat(md5(time()), 64))
        );
    }

    /**
     * Provider of valid reference URLs to be used for creating cookies
     *
     * @return array
     */
    static public function refUrlProvider()
    {
        return array(
            array(Zend_Uri::factory('http://example.com/')),
            array(Zend_Uri::factory('http://www.example.com/foo/bar/')),
            array(Zend_Uri::factory('http://some.really.deep.domain.com')),
            array(Zend_Uri::factory('http://localhost/path/to/very/deep/file.php')),
            array(Zend_Uri::factory('http://arr.gr/some%20path/text%2Ffile'))
        );
    }

    /**
     * Provide valid cookie strings with information about them
     *
     * @return array
     */
    static public function validCookieWithInfoProvider()
    {
        $now = time();
        $yesterday = $now - (3600 * 24);

        return array(
            array(
                'justacookie=foo; domain=example.com',
                array(
                    'name'    => 'justacookie',
                    'domain'  => 'example.com',
                    'path'    => '/',
                    'expires' => null,
                    'secure'  => false
                )
            ),
            array(
                'expires=tomorrow; secure; path=/Space Out/; expires=Tue, 21-Nov-2006 08:33:44 GMT; domain=.example.com',
                array(
                    'name'    => 'expires',
                    'domain'  => '.example.com',
                    'path'    => '/Space Out/',
                    'expires' => strtotime('Tue, 21-Nov-2006 08:33:44 GMT'),
                    'secure'  => true
                )
            ),
            array(
                'domain=unittests; expires=' . date(DATE_COOKIE, $now) . '; domain=example.com; path=/some%20value/',
                array(
                    'name'    => 'domain',
                    'domain'  => 'example.com',
                    'path'    => '/some%20value/',
                    'expires' => $now,
                    'secure'  => false,
                )
            ),
            array(
                'path=indexAction; path=/; domain=.foo.com; expires=' . date(DATE_COOKIE, $yesterday),
                array(
                    'name'    => 'path',
                    'domain'  => '.foo.com',
                    'path'    => '/',
                    'expires' => $yesterday,
                    'secure'  => false
                )
            ),

            array(
                'secure=sha1; secure; SECURE; domain=some.really.deep.domain.com',
                array(
                    'name'    => 'secure',
                    'domain'  => 'some.really.deep.domain.com',
                    'path'    => '/',
                    'expires' => null,
                    'secure'  => true
                )
            ),
            array(
                'PHPSESSID=123456789+abcd%2Cef; secure; domain=.localdomain; path=/foo/baz; expires=Tue, 21-Nov-2006 08:33:44 GMT;',
                array(
                    'name'    => 'PHPSESSID',
                    'domain'  => '.localdomain',
                    'path'    => '/foo/baz',
                    'expires' => strtotime('Tue, 21-Nov-2006 08:33:44 GMT'),
                    'secure'  => true
                )
            ),
        );
    }

    /**
     * Cookie with 'expired' flag, used to test if Cookie->isExpired()
     *
     * @return array
     */
    public static function cookieWithExpiredFlagProvider()
    {
        return array(
            array('cookie=foo;domain=example.com;expires=' . date(DATE_COOKIE, time() +  12 * 3600), false),
            array('cookie=foo;domain=example.com;expires=' . date(DATE_COOKIE, time() - 15), true),
            array('cookie=foo;domain=example.com;', false),
            array('cookie=foo;domain=example.com;expires=Fri, 01-Mar-2109 00:19:21 GMT', false),
            array('cookie=foo;domain=example.com;expires=Fri, 06-Jun-1966 00:19:21 GMT', true),
        );
    }
}

