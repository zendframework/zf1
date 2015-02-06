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
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TestCase.php';

/**
 * @see Zend_Service_Technorati_TagsResult
 */
require_once 'Zend/Service/Technorati/TagResult.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class Zend_Service_Technorati_TagResultTest extends Zend_Service_Technorati_TestCase
{
    public function setUp()
    {
        $this->domElements = self::getTestFileElementsAsDom('TestTagResultSet.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend_Service_Technorati_TagResult', array($this->domElements->item(0)));
    }

    public function testConstructThrowsExceptionWithInvalidDom()
    {
        $this->_testConstructThrowsExceptionWithInvalidDom('Zend_Service_Technorati_TagResult', 'DOMElement');
    }

    public function testTagResult()
    {
        $object = new Zend_Service_Technorati_TagResult($this->domElements->item(1));

        // check properties
        $this->assertTrue(is_string($object->getTitle()));
        $this->assertContains('Permalink for : VerveEarth', $object->getTitle());
        $this->assertTrue(is_string($object->getExcerpt()));
        $this->assertContains('VerveEarth: Locate Your Blog!', $object->getExcerpt());
        $this->assertTrue($object->getPermalink() instanceof Zend_Uri_Http);
        $this->assertEquals(Zend_Uri::factory('http://scienceroll.com/2007/11/14/verveearth-locate-your-blog/'), $object->getPermalink());
        $this->assertTrue($object->getCreated() instanceof Zend_Date);
        $this->assertEquals(new Zend_Date('2007-11-14 21:52:11'), $object->getCreated());
        $this->assertTrue($object->getUpdated() instanceof Zend_Date);
        $this->assertEquals(new Zend_Date('2007-11-14 21:57:59'), $object->getUpdated());

        // check weblog
        $this->assertTrue($object->getWeblog() instanceof Zend_Service_Technorati_Weblog);
        $this->assertEquals(' ScienceRoll', $object->getWeblog()->getName());
    }

    public function testTagResultSerialization()
    {
        $this->_testResultSerialization(new Zend_Service_Technorati_TagResult($this->domElements->item(0)));
    }
}
