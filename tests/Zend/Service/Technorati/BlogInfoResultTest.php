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
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TestCase.php';

/**
 * @see Zend_Service_Technorati_BlogInfoResult
 */
require_once 'Zend/Service/Technorati/BlogInfoResult.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class Zend_Service_Technorati_BlogInfoResultTest extends Zend_Service_Technorati_TestCase
{
    public function setUp()
    {
        $this->dom = self::getTestFileContentAsDom('TestBlogInfoResult.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend_Service_Technorati_BlogInfoResult', array($this->dom));
    }

    public function testConstructThrowsExceptionWithInvalidDom()
    {
        $this->_testConstructThrowsExceptionWithInvalidDom('Zend_Service_Technorati_BlogInfoResult', 'DOMDocument');
    }

    public function testBlogInfoResult()
    {
        $object = new Zend_Service_Technorati_BlogInfoResult($this->dom);

        // check weblog
        $weblog = $object->getWeblog();
        $this->assertTrue($weblog instanceof Zend_Service_Technorati_Weblog);
        $this->assertEquals('Simone Carletti\'s Blog', $weblog->getName());

        // check url
        $this->assertTrue($object->getUrl() instanceof Zend_Uri_Http);
        $this->assertEquals(Zend_Uri::factory('http://www.simonecarletti.com/blog'), $object->getUrl());

        // check inboundblogs
        $this->assertTrue(is_int($object->getInboundBlogs()));
        $this->assertEquals(86, $object->getInboundBlogs());

        // check inboundlinks
        $this->assertTrue(is_int($object->getInboundLinks()));
        $this->assertEquals(114, $object->getInboundLinks());
    }

    public function testBlogInfoResultUrlWithInvalidSchemaEqualsToWeblogUrl()
    {
        $dom = self::getTestFileContentAsDom('TestBlogInfoResultUrlWithInvalidSchema.xml');
        $object = new Zend_Service_Technorati_BlogInfoResult($dom);

        // check url
        $this->assertTrue($object->getUrl() instanceof Zend_Uri_Http);
        $this->assertEquals($object->getWeblog()->getUrl(), $object->getUrl());
    }
}
