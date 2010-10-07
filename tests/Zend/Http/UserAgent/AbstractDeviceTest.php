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
 * @package    Zend_Http_UserAgent
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * Zend_Http_UserAgent
 */
require_once 'Zend/Http/UserAgent.php';
require_once 'Zend/Http/UserAgent/AbstractDevice.php';
require_once 'Zend/Http/UserAgent/Bot.php';
require_once 'Zend/Http/UserAgent/Checker.php';
require_once 'Zend/Http/UserAgent/Console.php';
require_once 'Zend/Http/UserAgent/Desktop.php';
require_once 'Zend/Http/UserAgent/Email.php';
require_once 'Zend/Http/UserAgent/Feed.php';
require_once 'Zend/Http/UserAgent/Mobile.php';
require_once 'Zend/Http/UserAgent/Offline.php';
require_once 'Zend/Http/UserAgent/Probe.php';
require_once 'Zend/Http/UserAgent/Spam.php';
require_once 'Zend/Http/UserAgent/Text.php';
require_once 'Zend/Http/UserAgent/Validator.php';

/**
 * @category   Zend
 * @package    Zend_Http_UserAgent
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Http_UserAgent_AbstractDeviceTest extends PHPUnit_Framework_TestCase
{
    public function testUserAgentSafari()
    {
        $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US) AppleWebKit/533.17.8 (KHTML, like Gecko) Version/5.0.1 Safari/533.17.8';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('AppleWebKit', $extract['browser_engine']);
        $this->assertEquals('Safari', $extract['browser_name']);
        $this->assertEquals('5.0.1', $extract['browser_version']);
        $this->assertEquals('Windows Server 2003', $extract['device_os_name']);
        
        $userAgent = 'Mozilla/5.0 (iPad; U; CPU iPhone OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B314 Safari/531.21.10gin_lib.cc';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('AppleWebKit', $extract['browser_engine']);
        $this->assertEquals('Safari Mobile', $extract['browser_name']);
        $this->assertEquals('4.0.4', $extract['browser_version']);
        $this->assertEquals('iPhone OS', $extract['device_os_token']);
        
        $userAgent = 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_4_11; hu-hu) AppleWebKit/531.21.8 (KHTML, like Gecko) Version/4.0.4 Safari/531.21.10';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('AppleWebKit', $extract['browser_engine']);
        $this->assertEquals('Safari', $extract['browser_name']);
        $this->assertEquals('4.0.4', $extract['browser_version']);
        $this->assertEquals('PPC Mac OS X 10_4_11', $extract['device_os_token']);
        
        $userAgent = 'Mozilla/5.0 (iPad; U; CPU iPhone OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B314';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('AppleWebKit', $extract['browser_engine']);
        $this->assertEquals('Safari Mobile', $extract['browser_name']);
        $this->assertEquals('4.0.4', $extract['browser_version']);
        $this->assertEquals('iPhone OS', $extract['device_os_token']);
        
        $userAgent = 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; pt-pt) AppleWebKit/418.9.1 (KHTML, like Gecko) Safari/419.3';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('AppleWebKit', $extract['browser_engine']);
        $this->assertEquals('Safari', $extract['browser_name']);
        $this->assertEquals('2.0', $extract['browser_version']);
        $this->assertEquals('PPC Mac OS X', $extract['device_os_token']);
        
        $userAgent = 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; fr-ch) AppleWebKit/312.1.1 (KHTML, like Gecko) Safari/312';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Safari', $extract['browser_name']);
        $this->assertEquals('1.3', $extract['browser_version']);
        
        $userAgent = 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; fr) AppleWebKit/312.5.2 (KHTML, like Gecko) Safari/312.3.3';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Safari', $extract['browser_name']);
        $this->assertEquals('1.3', $extract['browser_version']);
        
        $userAgent = 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; fr-fr) AppleWebKit/85.7 (KHTML, like Gecko) Safari/85.5';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Safari', $extract['browser_name']);
        $this->assertEquals('1.0', $extract['browser_version']);
        
        $userAgent = 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-gb) AppleWebKit/85.8.5 (KHTML, like Gecko) Safari/85.8.1';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Safari', $extract['browser_name']);
        $this->assertEquals('1.0', $extract['browser_version']);
        
        $userAgent = 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/124 (KHTML, like Gecko) Safari/125';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Safari', $extract['browser_name']);
        $this->assertEquals('1.2', $extract['browser_version']);
        
        $userAgent = 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-us) AppleWebKit/418.9 (KHTML, like Gecko) Safari/419.3';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Safari', $extract['browser_name']);
        $this->assertEquals('2.0', $extract['browser_version']);
        
        $userAgent = 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/412.6.2 (KHTML, like Gecko) Safari/412.2.2';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Safari', $extract['browser_name']);
        $this->assertEquals('2.0', $extract['browser_version']);
    }

    public function testUserAgentInternetExplorer()
    {
        $userAgent = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 2.0.50727; Media Center PC 6.0)';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('MSIE', $extract['browser_engine']);
        $this->assertEquals('Internet Explorer', $extract['browser_name']);
        $this->assertEquals('9.0', $extract['browser_version']);
        $this->assertEquals('Windows 7', $extract['device_os_name']);
        
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; FDM; OfficeLiveConnector.1.4; OfficeLivePatch.1.3; .NET CLR 1.1.4322)';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('MSIE', $extract['browser_engine']);
        $this->assertEquals('Internet Explorer', $extract['browser_name']);
        $this->assertEquals('8.0', $extract['browser_version']);
        $this->assertEquals('Windows 7', $extract['device_os_name']);
        
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('MSIE', $extract['browser_engine']);
        $this->assertEquals('Internet Explorer', $extract['browser_name']);
        $this->assertEquals('7.0', $extract['browser_version']);
        $this->assertEquals('Windows 7', $extract['device_os_name']);
        
        $userAgent = 'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('MSIE', $extract['browser_engine']);
        $this->assertEquals('Internet Explorer', $extract['browser_name']);
        $this->assertEquals('7.0', $extract['browser_version']);
        $this->assertEquals('Windows Vista', $extract['device_os_name']);
        
        $userAgent = 'Mozilla/4.0 (Windows; MSIE 7.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('MSIE', $extract['browser_engine']);
        $this->assertEquals('Internet Explorer', $extract['browser_name']);
        $this->assertEquals('7.0', $extract['browser_version']);
        $this->assertEquals('Windows XP', $extract['device_os_name']);
        
        $userAgent = 'Mozilla/45.0 (compatible; MSIE 6.0; Windows NT 5.1)';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('MSIE', $extract['browser_engine']);
        $this->assertEquals('Internet Explorer', $extract['browser_name']);
        $this->assertEquals('6.0', $extract['browser_version']);
        $this->assertEquals('Windows XP', $extract['device_os_name']);
        
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.2; .NET CLR 1.1.4322)';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('MSIE', $extract['browser_engine']);
        $this->assertEquals('Internet Explorer', $extract['browser_name']);
        $this->assertEquals('5.5', $extract['browser_version']);
        $this->assertEquals('Windows Server 2003', $extract['device_os_name']);
        
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 5.12; Mac_PowerPC)';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('MSIE', $extract['browser_engine']);
        $this->assertEquals('Internet Explorer', $extract['browser_name']);
        $this->assertEquals('5.12', $extract['browser_version']);
        $this->assertEquals('Mac_PowerPC', $extract['device_os_token']);
        
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 4.5; Windows 98; )';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('MSIE', $extract['browser_engine']);
        $this->assertEquals('Internet Explorer', $extract['browser_name']);
        $this->assertEquals('4.5', $extract['browser_version']);
        $this->assertEquals('Windows 98', $extract['device_os_name']);
    }

    public function testUserAgentFirefox()
    {
        $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; rv:1.9.2.3) Gecko/20100401 Firefox/4.0 (.NET CLR 3.5.30729)';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Firefox', $extract['browser_name']);
        $this->assertEquals('4.0', $extract['browser_version']);
        $this->assertEquals('Windows 7', $extract['device_os_name']);
        
        $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; nb-NO; rv:1.9.2.4) Gecko/20100611 Firefox/3.6.4 (.NET CLR 3.5.30729)';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Firefox', $extract['browser_name']);
        $this->assertEquals('3.6.4', $extract['browser_version']);
        $this->assertEquals('Windows XP', $extract['device_os_name']);
        
        $userAgent = 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.6) Gecko/2009020518 Ubuntu/9.04 (jaunty) Firefox/3.0.6';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Firefox', $extract['browser_name']);
        $this->assertEquals('3.0.6', $extract['browser_version']);
        $this->assertEquals('Linux i686', $extract['device_os_token']);
        
        $userAgent = 'Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Firefox', $extract['browser_name']);
        $this->assertEquals('2.0.0.9', $extract['browser_version']);
        $this->assertEquals('Linux i686 (x86_64)', $extract['device_os_token']);
        
        $userAgent = 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.8) Gecko/20071019 Fedora/2.0.0.8-1.fc7 Firefox/2.0.0.8';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Firefox', $extract['browser_name']);
        $this->assertEquals('2.0.0.8', $extract['browser_version']);
        $this->assertEquals('Linux i686', $extract['device_os_token']);
        
        $userAgent = 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.1) Gecko/20060313 Debian/1.5.dfsg+1.5.0.1-4 Firefox/1.5.0.1';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Firefox', $extract['browser_name']);
        $this->assertEquals('1.5.0.1', $extract['browser_version']);
        $this->assertEquals('Linux i686', $extract['device_os_token']);
    }

    public function testUserAgentMozilla()
    {
        $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; it; rv:2.0b4) Gecko/20100818';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Mozilla', $extract['browser_name']);
        $this->assertEquals('2.0b4', $extract['browser_version']);
        
        $userAgent = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en; rv:1.8.1.4pre) Gecko/20070521 Camino/1.6a1pre';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Camino', $extract['browser_name']);
        $this->assertEquals('1.6a1pre', $extract['browser_version']);
        
        $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-GB; rv:1.9.0.7) Gecko/2009021910 MEGAUPLOAD 1.0';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Mozilla', $extract['browser_name']);
        $this->assertEquals('1.9.0.7', $extract['browser_version']);
        
        $userAgent = 'Mozilla/5.0 (X11; U; Linux i686; pl-PL; rv:1.9.0.6) Gecko/2009020911';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Mozilla', $extract['browser_name']);
        $this->assertEquals('1.9.0.6', $extract['browser_version']);
        
        $userAgent = 'Mozilla/5.001 (X11; U; Linux i686; rv:1.8.1.6; de-ch) Gecko/25250101 (ubuntu-feisty)';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Mozilla', $extract['browser_name']);
        $this->assertEquals('1.8.1.6', $extract['browser_version']);
        
        $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.18) Gecko/20081029';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Mozilla', $extract['browser_name']);
        $this->assertEquals('1.8.1.18', $extract['browser_version']);
        
        $userAgent = 'Mozilla/5.0 (X11; U; Linux i586; de-AT; rv:1.4) Gecko/20030908 Debian/1.4-4';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Mozilla', $extract['browser_name']);
        $this->assertEquals('1.4', $extract['browser_version']);
    }

    public function testUserAgentChrome()
    {
        $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US) AppleWebKit/534.4 (KHTML, like Gecko) Chrome/6.0.481.0 Safari/534.4';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('AppleWebKit', $extract['browser_engine']);
        $this->assertEquals('Chrome', $extract['browser_name']);
        $this->assertEquals('6.0.481.0', $extract['browser_version']);
        $this->assertEquals('Windows Server 2003', $extract['device_os_name']);
        
        $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/534.7 (KHTML, like Gecko) Chrome/7.0.514.0 Safari/534.7';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('AppleWebKit', $extract['browser_engine']);
        $this->assertEquals('Chrome', $extract['browser_name']);
        $this->assertEquals('7.0.514.0', $extract['browser_version']);
        $this->assertEquals('Windows XP', $extract['device_os_name']);
        
        $userAgent = 'Mozilla/5.0 (X11; U; Linux i686; en-US) AppleWebKit/534.1 (KHTML, like Gecko) Chrome/6.0.416.0 Safari/534.1';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('AppleWebKit', $extract['browser_engine']);
        $this->assertEquals('Chrome', $extract['browser_name']);
        $this->assertEquals('6.0.416.0', $extract['browser_version']);
        $this->assertEquals('Linux i686', $extract['device_os_token']);
    }

    public function testUserAgentNetscape()
    {
        $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9pre) Gecko/20071102 Firefox/2.0.0.9 Navigator/9.0.0.3';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Gecko', $extract['browser_engine']);
        $this->assertEquals('Netscape', $extract['browser_name']);
        $this->assertEquals('9.0.0.3', $extract['browser_version']);
        $this->assertEquals('Windows XP', $extract['device_os_name']);
        
        $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.2) Gecko/20050208 Netscape/7.20';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Gecko', $extract['browser_engine']);
        $this->assertEquals('Netscape', $extract['browser_name']);
        $this->assertEquals('7.20', $extract['browser_version']);
        $this->assertEquals('Windows 2000', $extract['device_os_name']);
        
        $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.0; fr-FR; rv:0.9.4) Gecko/20011128 Netscape6/6.2.1';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Gecko', $extract['browser_engine']);
        $this->assertEquals('Netscape', $extract['browser_name']);
        $this->assertEquals('6.2.1', $extract['browser_version']);
        $this->assertEquals('Windows 2000', $extract['device_os_name']);
        
        $userAgent = 'Mozilla/4.79 [en] (X11; U; SunOS 5.7 sun4u)';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Netscape', $extract['browser_name']);
        $this->assertEquals('4.79', $extract['browser_version']);
        $this->assertEquals('SunOS 5.7 sun4u', $extract['device_os_token']);
        
        $userAgent = 'Mozilla/4.04 [fr] (Macintosh; I; PPC, Nav)';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Netscape', $extract['browser_name']);
        $this->assertEquals('4.04', $extract['browser_version']);
        $this->assertEquals('Macintosh', $extract['compatibility_flag']);
    }

    public function testUserAgentOpera()
    {
        $userAgent = 'Opera/9.99 (Windows NT 5.1; U; pl) Presto/9.9.9';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Presto', $extract['browser_engine']);
        $this->assertEquals('Opera', $extract['browser_name']);
        $this->assertEquals('9.99', $extract['browser_version']);
        
        $userAgent = 'Opera/9.80 (J2ME/MIDP; Opera Mini/5.0 (Windows; U; Windows NT 5.1; en) AppleWebKit/886; U; en) Presto/2.4.15';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Presto', $extract['browser_engine']);
        $this->assertEquals('opera mini', strtolower($extract['browser_name']));
        
        $userAgent = 'Opera/9.70 (Linux ppc64 ; U; en) Presto/2.2.1';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Presto', $extract['browser_engine']);
        $this->assertEquals('Opera', $extract['browser_name']);
        $this->assertEquals('9.70', $extract['browser_version']);
        
        $userAgent = 'Mozilla/5.0 (Windows NT 5.1; U; en-GB; rv:1.8.1) Gecko/20061208 Firefox/2.0.0 Opera 9.61';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Gecko', $extract['browser_engine']);
        $this->assertEquals('Opera', $extract['browser_name']);
        $this->assertEquals('9.61', $extract['browser_version']);
        
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; X11; Linux x86_64; en) Opera 9.60';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('MSIE', $extract['browser_engine']);
        $this->assertEquals('Opera', $extract['browser_name']);
        $this->assertEquals('9.60', $extract['browser_version']);
        
        $userAgent = 'Opera/9.52 (Windows NT 6.0; U; Opera/9.52 (X11; Linux x86_64; U); en)';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Opera', $extract['browser_name']);
        $this->assertEquals('9.52', $extract['browser_version']);
        
        $userAgent = 'Opera/9.20 (Windows NT 6.0; U; de)';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Opera', $extract['browser_name']);
        $this->assertEquals('9.20', $extract['browser_version']);
        
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; ru) Opera 8.54';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('MSIE', $extract['browser_engine']);
        $this->assertEquals('Opera', $extract['browser_name']);
        $this->assertEquals('8.54', $extract['browser_version']);
        
        $userAgent = 'Opera/9.80 (Windows NT 5.1; U; zh-cn) Presto/2.2.15 Version/10.00';
        $extract = Zend_Http_UserAgent_Desktop::extractFromUserAgent($userAgent);
        $this->assertEquals('Presto', $extract['browser_engine']);
        $this->assertEquals('Opera', $extract['browser_name']);
        $this->assertEquals('10.00', $extract['browser_version']);
    }

    /** 
     * examples from http://en.wikipedia.org/wiki/List_of_user_agents_for_mobile_phones
     */
    public function testMatchMobile()
    {
        $userAgent = 'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleW1ebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/4A102 Safari/419.3';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = '8500: HTC-8500/1.2 Mozilla/4.0 (compatible; MSIE 5.5; Windows CE; PPC; 240x320) ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = '8500: HTC-8500/1.2 Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 7.6) UP.Link/6.3.1.17.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Alcatel OT-708: Alcatel-OT-708/1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 ObigoInternetBrowser/Q03C ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Android SDK 1.5r3: Mozilla/5.0 (Linux; U; Android 1.5; de-; sdk Build/CUPCAKE) AppleWebkit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mobile Safari/525.20.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Apple iPad: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B367 Safari/531.21.10 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Apple iPhone OS 4: Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_0 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8A293 Safari/6531.22.7 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Apple iPhone: Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/1A542a Safari/419.3 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 7100i: BlackBerry7100i/4.1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/103 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 7130e: BlackBerry7130e/4.1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/104 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 7230: BlackBerry7230/3.7.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 7250: BlackBerry7250/4.0.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 7520: BlackBerry7520/4.0.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 7730: BlackBerry7730/3.7.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 8100: Mozilla/4.0 BlackBerry8100/4.2.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/100 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 8130: BlackBerry8130/4.3.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/109 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 8310: BlackBerry8310/4.2.2 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/121 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 8320: BlackBerry8320/4.3.1 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 8700: BlackBerry8700/4.1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/100 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 8703e: BlackBerry8703e/4.1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/105 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 8820: BlackBerry8820/4.2.2 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/102 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 8830: BlackBerry8830/4.2.2 Profile/MIDP-2.0 Configuration/CLOC-1.1 VendorID/105 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 8900: BlackBerry8900/4.5.1.231 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/100 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 9000: BlackBerry9000/4.6.0.65 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/102 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 9530: BlackBerry9530/4.7.0.167 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/102 UP.Link/6.3.1.20.0';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 9630 Tour BlackBerry9630/4.7.1.40 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/104 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 9700 Bold: BlackBerry9700/5.0.0.423 Profile/MIDP-2.1 Configuration/CLDC-1.1 VendorID/100 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry 9800 Torch: Mozilla/5.0 (BlackBerry; U; BlackBerry 9800; en) AppleWebKit/534.1+ (KHTML, Like Gecko) Version/6.0.0.141 Mobile Safari/534.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'BlackBerry9530/5.0.0.328 Profile/MIDP-2.1 Configuration/CLDC-1.1 VendorID/105 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Desire: Mozilla/5.0 (Linux; U; Android 2.1-update1; fr-fr; desire_A8181 Build/ERE27) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'EF81: SIE-EF81/58 UP.Browser/7.0.0.1.181 (GUI) MMP/2.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Espresso: Mozilla/5.0 (Linux; U; Android 2.1-update1; en-us; T-Mobile_Espresso Build/ERE27) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'EVO 4G: Mozilla/5.0 (Linux; U; Android 2.1-update1; en-us; Sprint APA9292KT Build/ERE27) AppleWebKit/530.17 (KHTML, like Gecko) ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Hero: Mozilla/5.0 (Linux; U; Android 1.5; en-za; HTC Hero Build/CUPCAKE) AppleWebKit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mobile Safari/525.20.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'iPod Touch: Mozilla/5.0 (iPod; U; CPU iPhone OS 3_1_1 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Mobile/7C145 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Legend: Mozilla/5.0 (Linux; U; Android 2.1; fr-fr; HTC Legend 1.32.163.1 Build/ERD79) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG B2050: LG-B2050 MIC/WAP2.0 MIDP-2.0/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG C1100: LG-C1100 MIC/WAP2.0 MIDP-2.0/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG CU720: LG-CU720/V1.0|Obigo/Q05A Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG CU8080: LGE-CU8080/1.0 UP.Browser/4.1.26l ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G1800: LG-G1800 MIC/WAP2.0 MIDP-2.0/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G210: LG-G210/SW100/WAP2.0 Profile/MIDP-2.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G220: LG-G220/V100/WAP2.0 Profile/MIDP-2.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G232: LG-G232/V100/WAP2.0 Profile/MIDP-2.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G262: LG-G262/V100/WAP2.0 Profile/MIDP-2.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G5200: LG-G5200 AU/4.10 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G5600: LG-G5600 MIC/WAP2.0 MIDP-2.0/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G610: LG-G610 V100 AU/4.10 Profile/MIDP-1.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G622: LG-G622/V100/WAP2.0 Profile/MIDP-2.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G650: LG-G650 V100 AU/4.10 Profile/MIDP-1.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G660: LG-G660/V100/WAP2.0 Profile/MIDP-2.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G672: LG-G672/V100/WAP2.0 Profile/MIDP-2.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G682: LG-G682 /V100/WAP2.0 Profile/MIDP-2.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G688: LG-G688 MIC/V100/WAP2.0 MIDP-2.0/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G7000: LG-G7000 AU/4.10 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G7050: LG-G7050 UP.Browser/6.2.2 (GUI) MMP/1.0 Profile/MIDP-1.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G7100: LG-G7100 AU/4.10 Profile/MIDP-1.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G7200: LG-G7200 UP.Browser/6.2.2 (GUI) MMP/1.0 Profile/MIDP-1.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G822: LG-G822/SW100/WAP2.0 Profile/MIDP-2.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G850: LG-G850 V100 UP.Browser/6.2.2 (GUI) MMP/1.0 Profile/MIDP-1.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G920: LG-G920/V122/WAP2.0 Profile/MIDP-1.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G922: LG-G922 Obigo/WAP2.0 MIDP-2.0/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG G932: LG-G932 UP.Browser/6.2.3(GUI)MMP/1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG KP500: LG-KP500 Teleca/WAP2.0 MIDP-2.0/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG KS360: LG-KS360 Teleca/WAP2.0 MIDP-2.0/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG L1100: LG-L1100 UP.Browser/6.2.2 (GUI) MMP/1.0 Profile/MIDP-1.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG MX8700: LGE-MX8700/1.0 UP.Browser/6.2.3.2 (GUI) MMP/2.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG T5100: LG-T5100 UP.Browser/6.2.3 (GUI) MMP/1.0 Profile/MIDP-1.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG U8120: LG/U8120/v1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG U8130: LG/U8130/v1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG U8138: LG/U8138/v2.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG U8180: LG/U8180/v1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG U880: LG/U880/v1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'LG VX9100: LGE-VX9100/1.0 UP.Browser/6.2.3.2 (GUI) MMP/2.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Magic: Mozilla/5.0 (Linux; U; Android 1.5; en-dk; HTC Magic Build/CUPCAKE) AppleWebKit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mobile Safari/525.20.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola CLIQ: Mozilla/5.0 (Linux; U; Android 1.5; en-us; MB200 Build/CUPCAKE) AppleWebKit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mob ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola Droid V2.01: Mozilla/5.0 (Linux; U; Android 2.0.1; en-us; Droid Build/ESD56) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola Droid X Mozilla/5.0 (Linux; U; Android 2.1-update1; en-us; DROIDX Build/VZW) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17 480X854 motorola DROIDX ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola E398: MOT-E398/0E.20.59R MIB/2.2.1 Profile/MIDP-2.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola EM30: MOT-EM30/R6716_G_71.01.24R Mozilla/5.0 (compatible; OSS/1.0; Chameleon; Linux) BER/2.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 [es-co] ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola KRZR K1: MOT-K1/08.03.08R MIB/BER2.2 Profile/MIDP-2.0 Configuration/CLDC-1.1 EGE/1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola Motoroi(XT720) V2.01: Mozilla/5.0 (Linux; U; Android 2.0.1; ko-kr; XT720 Build/STSKT_N_79.11.31R) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola Motoroi(XT720) V2.1-update1: Mozilla/5.0 (Linux; U; Android 2.1-update1; ko-kr; XT720 Build/STSKT_N_79.11.33R) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola RAZR V3: MOT-V3/0E.42.0ER MIB/2.2.1 Profile/MIDP-2.0 Configuration/CLDC-1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola RAZR V3r: MOT-V3r/08.BD.43R MIB/2.2.1 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola RAZR V3xx: MOT-RAZRV3xx/96.64.21P BER2.2 Mozilla/4.0 (compatible; MSIE 6.0; 11003002) Profile/MIDP-2.0 Configuration/CLDC-1.1 Opera 8.00 [en] UP.Link/6.3.0.0.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola RAZR2 V8: motorazrV8/R601_G_80.42.0FRP Mozilla/4.0 (compatible; MSIE 6.0 Linux; Motorola V8;nnn) Profile/MIDP-2.0 Configuration/CLDC-1.1 Opera 8.50[yy] ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola RAZR2 V9: MOT-MOTORAZRV9/4 BER2.2 Mozilla/4.0 (compatible; MSIE 6.0; 14003181) Profile/MIDP-2.0 Configuration/CLDC-1.1 Op! era 8.00 [en] UP.Link/6.3.0.0.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola RAZR2 V9x: MOT-MOTORAZRV9x/9E.03.15R BER2.2 Mozilla/4.0 (compatible; MSIE 6.0; 13003337) Profile/MIDP-2.0 Configuration/CLDC-1.1 Opera 8.60 [en] UP.Link/6.3.0.0.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola ROKR E8: Mozilla/5.0 (compatible; OSS/1.0; Chameleon; Linux) MOT-E8/R6713_G_71.02.07R BER/2.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola ROKR Z6: MOTOROKR Z6/R60_G_80.xx.yyl Mozilla/4.0 (compatible; MSIE 6.0 Linux; MOTOROKRZ6;nnn) Profile/MIDP-2.0 Configuration/CLDC-1.1 Opera 8.50[yy] ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola SLVR L6: MOT-L6/0A.52.2BR MIB/2.2.1 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola SLVR L7: MOT-L7/NA.ACR_RB MIB/2.2.1 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola U9: Mozilla/5.0 (compatible; OSS/1.0; Chameleon; Linux) MOT-U9/R6632_G_81.11.29R BER/2.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola Z9: MOT-MOTOZ9/9E.01.03R BER2.2 Mozilla/4.0 (compatible; MSIE 6.0; 11003002) Profile/MIDP-2.0 Configuration/CLDC-1.1 Opera 8.60 [en] UP.Link/6.3.0.0.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Motorola ZN5: MOT-ZN5/R6637_G_81.03.05R Mozilla/4.0 (compatible; OSS/1.0; Linux MOTOZINE ZN5) Profile/MIDP-2.0 Configuration/CLDC-1.1 Symphony 1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Mozilla/5.0 (SymbianOS/9.1; U; en-us) AppleWebKit/413 (KHTML, like Gecko) Safari/413 es65';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nexus One: Mozilla/5.0 (Linux; U; Android 2.2; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia 2610: Nokia2610/2.0 (07.04a) Profile/MIDP-2.0 Configuration/CLDC-1.1 UP.Link/6.3.1.20.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia 5300: Nokia5300/2.0 (05.51) Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia 5530: Mozilla/5.0 (SymbianOS/9.4; U; Series60/5.0 Nokia5530c-2/10.0.050; Profile MIDP-2.1 Configuration/CLDC-1.1) AppleWebKit/525 (KHTML, like Gecko) Safari/525 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia 5630: Mozilla/5.0 (SymbianOS/9.3; U; Series60/3.2 Nokia5630d-1/012.020; Profile MIDP-2.1 Configuration/CLDC-1.1) AppleWebKit/413 (KHTML, like Gecko) Safari/413 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia 5800: Mozilla/5.0 (SymbianOS/9.4; U; Series60/5.0 Nokia5800d-1/31.0.101; Profile MIDP-2.1 Configuration/CLDC-1.1) AppleWebKit/413 (KHTML, like Gecko) Safari/413 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia 6030: Nokia6030/2.0 (y3.44) Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia 6230i: Nokia6230i/2.0 (03.40) Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia 6280: Nokia6280/2.0 (03.60) Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia 6650: Nokia6650d-1bh/ATT.2.15 Mozilla/5.0 (SymbianOS/9.3; U; [en]; Series60/3.2; Profile/MIDP-2.1 Configuration/CLDC-1.1) AppleWebKit/413 (KHTML, like Gecko) Safari/413 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia E51-1: Mozilla/5.0 (SymbianOS/9.2; U; Series60/3.1 NokiaE51-1/220.34.37; Profile/MIDP-2.0 Configuration/CLDC-1.1) AppleWebKit/413 (KHTML, like Gecko) Safari/413 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia E71x: NokiaE71x/ATT.03.11.1 Mozilla/5.0 SymbianOS/9.3; U; [en]; Series60/3.2; Profile/MIDP-2.1 Configuration/CLDC-1.1 AppleWebKit/413 KHTML, like Gecko) Safari/413 UP.Link/6.3.0.0.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia N70: NokiaN70-1/5.0616.2.0.3 Series60/2.8 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia N75: NokiaN75-3/3.0 (1.0635.0.0.6); SymbianOS/9.1 Series60/3.0 Profile/MIDP-2.0 Configuration/CLDC-1.1) UP.Link/6.3.0.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia N78: Mozilla/5.0 (SymbianOS/9.3; U; Series60/3.2 NokiaN78-1/12.046; Profile/MIDP-2.1 Configuration/CLDC-1.1) AppleWebKit/413 (KHTML, like Gecko) Safari/413 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia N80: NokiaN80-1/3.0(4.0632.0.10) Series60/3.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia N90: NokiaN90-1/5.0607.7.3 Series60/2.8 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia N900: Mozilla/5.0 (X11; U; Linux armv7l; en-GB; rv:1.9.2b6pre) Gecko/20100318 Firefox/3.5 Maemo Browser 1.7.4.8 RX-51 N900 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia N95: Mozilla/5.0 (SymbianOS/9.2; U; Series60/3.1 NokiaN95/11.0.026; Profile MIDP-2.0 Configuration/CLDC-1.1) AppleWebKit/413 (KHTML, like Gecko) Safari/413 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Nokia N97-3: Mozilla/5.0 (SymbianOS/9.4; Series60/5.0 NokiaN97-3/21.2.045; Profile/MIDP-2.1 Configuration/CLDC-1.1;) AppleWebKit/525 (KHTML, like Gecko) BrowserNG/7.1.4 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'nokia_e65 (partial string)';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Note that Nokia Symbian phones may have two different user-agent strings, one for the classical WAP like:';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Opera Mini on Samsung Z720: Opera/9.50 (J2ME/MIDP; Opera Mini/4.1.11355/542; U; en) ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'P3450: Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 6.12) PPC; 240x320; HTC P3450; OpVer 23.116.1.611 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'P3650: HTC_P3650 Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 7.6) ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Pixi: Mozilla/5.0 (webOS/Palm webOS 1.2.9; U; en-US) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/1.0 Safari/525.27.1 Pixi/1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Pre: Mozilla/5.0 (webOS/1.4.0; U; en-US) AppleWebKit/532.2 (KHTML, like Gecko) Version/1.0 Safari/532.2 Pre/1.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'S68: SIE-S68/36 UP.Browser/7.1.0.e.18 (GUI) MMP/2.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'S710: Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 7.6) SP; 240x320; HTC_S710/1.0 ... ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung A737: SAMSUNG-SGH-A737/1.0 SHP/VPP/R5 NetFront/3.3 SMM-MMS/1.2.0 profile/MIDP-2.0 configuration/CLDC-1.1 UP.Link/6.3.0.0.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung A737: SAMSUNG-SGH-A737/UCGI3 SHP/VPP/R5 NetFront/3.4 SMM-MMS/1.2.0 profile/MIDP-2.0 configuration/CLDC-1.1 UP.Link/6.3.1.17.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung A767: SAMSUNG-SGH-A767/A767UCHG2 SHP/VPP/R5 NetFront/3.4 SMM-MMS/1.2.0 profile/MIDP-2.0 configuration/CLDC-1.1 UP.Link/6.3.0.0.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung A867: SAMSUNG-SGH-A867/A867UCHG5 SHP/VPP/R5 NetFront/3.4 SMM-MMS/1.2.0 profile/MIDP-2.0 configuration/CLDC-1.1 UP.Link/6.3.0.0.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung A877: SAMSUNG-SGH-A877/A877UCHK1 SHP/VPP/R5 NetFront/3.5 SMM-MMS/1.2.0 profile/MIDP-2.1 configuration/CLDC-1.1 UP.Link/6.3.0.0.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung Captivate (Galaxy for AT&T): Mozilla/5.0 (Linux; U; Android 2.1-update1; en-us; SAMSUNG-SGH-I897/I897UCJF6 Build/ECLAIR) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung D600: SAMSUNG-SGH-D600/1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 UP.Browser/6.2.3.3.c.1.101 (GUI) MMP/2.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung GT-S5230: SAMSUNG-GT-S5230/S523MXEIL2 SHP/VPP/R5 Jasmine/1.0 Nextreaming SMM-MMS/1.2.0 profile/MIDP-2.1 configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung i617: SAMSUNG-SGH-I617/1.0 Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 6.12) UP.Link/6.3.0.0.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung i7500 Galaxy: Mozilla/5.0 (Linux; U; Android 1.5; de-de; Galaxy Build/CUPCAKE) AppleWebkit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mobile Safari/525.20.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung i9000 aka Galaxy S : Mozilla/5.0 (Linux; U; Android 2.1-update1; fr-fr; GT-I9000 Build/ECLAIR) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung SGH-E250: SAMSUNG-SGH-E250/1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 UP.Browser/6.2.3.3.c.1.101 (GUI) MMP/2.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung SGH-i900 Omnia: SAMSUNG-SGH-i900/1.0 Opera 9.5 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung SGH-i907: SAMSUNG-SGH-i907/UCHI5 Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 7.11) ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung SGH-T919: SAMSUNG-SGH-T919/919UVHL3SHP/VPP/R5NetFront/3.5SMM-MMS/1.2.0profile/MIDP-2.1configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung SGH-U600: SEC-SGHU600/1.0 NetFront/3.2 Profile ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung SGH-U900: SAMSUNG-SGH-U900-Vodafone/U900BUHD6 SHP/VPP/R5 NetFront/3.4 Qtv5.3 SMM-MMS/1.2.0 profile/MIDP-2.0 configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Samsung Z720: SAMSUNG-SGH-Z720/1.0 SHP/VPR/R5 NetFront/3.3 SMM-MMS/1.2.0 profile/MIDP-2.0 configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Smart: HTC_Smart_F3188 Mozilla/5.0 (like Gecko) Obigo/Q7 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson C901: SonyEricssonC901/R1EA Browser/NetFront/3.4 Profile/MIDP-2.1 Configuration/CLDC-1.1 JavaPlatform/JP-8.4.2 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson C905: SonyEricssonC905/R1FA Browser/NetFront/3.4 Profile/MIDP-2.1 Configuration/CLDC-1.1 JavaPlatform/JP-8.4.3 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson C905a: SonyEricssonC905a/R1FA Browser/NetFront/3.4 Profile/MIDP-2.1 Configuration/CLDC-1.1 JavaPlatform/JP-8.4.3 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson K510i: SonyEricssonK510i/R4CJ Browser/NetFront/3.3 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson K550i: SonyEricssonK550i/R8BA Browser/NetFront/3.3 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson K600i: SonyEricssonK600i/R2BA Browser/SEMC-Browser/4.2 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson K610i: SonyEricssonK610i/R1CB Browser/NetFront/3.3 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson K630i: SonyEricssonK630i/R1CA Browser/NetFront/3.4 Profile/MIDP-2.1 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson K700: SonyEricssonK700/R1A Profile/MIDP-1.0 MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson K750i: SonyEricssonK750i/R1CA Browser/SEMC-Browser/4.2 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson K790i: SonyEricssonK790i/R8BF Browser/NetFront/3.3 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson K800i: SonyEricssonK800i/R8BF Browser/NetFront/3.3 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson W800i: SonyEricssonW800i/R1AA Browser/SEMC-Browser/4.2 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson W810i: SonyEricssonW810i/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson W900i: SonyEricssonW900i/R5AH Browser/NetFront/3.3 Profile/MIDP-2.0 Configuration/CLDC-1.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson W995i: SonyEricssonW995/R1DB Browser/NetFront/3.4 Profile/MIDP-2.1 Configuration/CLDC-1.1 JavaPlatform/JP-8.4.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson X10: Mozilla/5.0 (Linux; U; Android 1.6; es-es; SonyEricssonX10i Build/R1FA016) AppleWebKit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mobile Safari/525.20.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'SonyEricsson Z500a: SonyEricssonZ500a/R1A SEMC-Browser/4.0.1 Profile/MIDP-2.0 Configuration/CLDC-1.1 UP.Link/6.3.1.20.0 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Tattoo: Mozilla/5.0 (Linux; U; Android 1.6; en-us; HTC_TATTOO_A3288 Build/DRC79) AppleWebKit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mobile Safari/525.20.1 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'The string that is presented depends on the APN settings that are used for browsing (WAP/ISP). As the traditional browser string does not usually give any clues as to the type of device, the user-agent alone is not a guaranteed method of identifying Nokia devices. However, when the traditional browser user-agent is used, Nokia devices also provide the x-Device-User-Agent header, which contains the device specific user-agent.';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'Treo 650: Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; PalmSource/hspr-H102; Blazer/4.0) 16;320x320 ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        $userAgent = 'V8301: ZTE-V8301/MB6801_V1_Z1_VN_F1BPa101 Profile/MIDP-2.0 Configuration/CLDC-1.1 Obigo/Q03C ';
        $this->assertTrue(Zend_Http_UserAgent_Mobile::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
    
    }

    public function testMatchBot()
    {
        $userAgent = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
        $this->assertTrue(Zend_Http_UserAgent_Bot::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        
        $userAgent = 'Googlebot/2.1 (+http://www.googlebot.com/bot.html)';
        $this->assertTrue(Zend_Http_UserAgent_Bot::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        
        $userAgent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';
        $this->assertTrue(Zend_Http_UserAgent_Bot::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        
        $userAgent = 'YahooSeeker/1.2 (compatible; Mozilla 4.0; MSIE 5.5; yahooseeker at yahoo-inc dot com ; http://help.yahoo.com/help/us/shop/merchant/)';
        $this->assertTrue(Zend_Http_UserAgent_Bot::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        
        $userAgent = 'Mozilla/4.0 compatible ZyBorg/1.0 (wn.zyborg@looksmart.net; http://www.WISEnutbot.com)';
        $this->assertTrue(Zend_Http_UserAgent_Bot::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
    }

    public function testMatchChecker()
    {
        $userAgent = 'Mozilla/5.0 (compatible; AbiLogicBot/1.0; +http://www.abilogic.com/bot.html)';
        $this->assertTrue(Zend_Http_UserAgent_Checker::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        
        $userAgent = 'W3C-checklink/4.5 [4.160] libwww-perl/5.823';
        $this->assertTrue(Zend_Http_UserAgent_Checker::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
    }

    public function testMatchConsole()
    {
        $userAgent = 'Mozilla/5.0 (PLAYSTATION 3; 1.10)';
        $this->assertTrue(Zend_Http_UserAgent_Console::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        
        $userAgent = 'Opera/9.30 (Nintendo Wii; U; ; 2071; Wii Shop Channel/1.0; en)';
        $this->assertTrue(Zend_Http_UserAgent_Console::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
    }

    public function testMatchEmail()
    {
        $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.9.1.9) Gecko/20100317 Lightning/1.0b1 Thunderbird/3.0.4';
        $this->assertTrue(Zend_Http_UserAgent_Email::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
    }

    public function testMatchFeed()
    {
        $userAgent = 'Bloglines/3.0-rho (http://www.bloglines.com; 3 subscribers)';
        $this->assertTrue(Zend_Http_UserAgent_Feed::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
    }

    public function testMatchOffline()
    {
        $userAgent = 'Offline Explorer/2.5';
        $this->assertTrue(Zend_Http_UserAgent_Offline::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        
        $userAgent = 'Wget/1.9.1';
        $this->assertTrue(Zend_Http_UserAgent_Offline::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        
        $userAgent = 'WebCopier v3.2a';
        $this->assertTrue(Zend_Http_UserAgent_Offline::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
    }

    public function _testMatchProbe()
    {
        $this->markTestIncomplete();
    }

    public function _testMatchSpam()
    {
        $this->markTestIncomplete();
    }

    public function testMatchText()
    {
        $userAgent = 'Lynx/2.8.6rel.4 libwww-FM/2.14 SSL-MM/1.4.1 OpenSSL/0.9.8k';
        $this->assertTrue(Zend_Http_UserAgent_Text::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
        
        $userAgent = 'w3m/0.5.1+cvs-1.968';
        $this->assertTrue(Zend_Http_UserAgent_Text::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
    }

    public function testMatchValidator()
    {
        $userAgent = 'CSE HTML Validator Lite Online (http://online.htmlvalidator.com/php/onlinevallite.php)';
        $this->assertTrue(Zend_Http_UserAgent_Validator::match($userAgent,array('HTTP_USER_AGENT'=>$userAgent)));
    }
}
