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
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Feed/Reader.php';
require_once 'Zend/Registry.php';

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Feed
 * @group      Zend_Feed_Reader
 */
class Zend_Feed_Reader_Feed_CommonTest extends PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;

    public function setup()
    {
        Zend_Feed_Reader::reset();
        if (Zend_Registry::isRegistered('Zend_Locale')) {
            $registry = Zend_Registry::getInstance();
            unset($registry['Zend_Locale']);
        }
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/Common';
    }

    /**
     * Check DOM Retrieval and Information Methods
     */
    public function testGetsDomDocumentObject()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertTrue($feed->getDomDocument() instanceof DOMDocument);
    }

    public function testGetsDomXpathObject()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertTrue($feed->getXpath() instanceof DOMXPath);
    }

    public function testGetsXpathPrefixString()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertTrue($feed->getXpathPrefix() == '/atom:feed');
    }

    public function testGetsDomElementObject()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertTrue($feed->getElement() instanceof DOMElement);
    }

    public function testSaveXmlOutputsXmlStringForFeed()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertEquals($feed->saveXml(), file_get_contents($this->_feedSamplePath.'/atom_rewrittenbydom.xml'));
    }

    public function testGetsNamedExtension()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertTrue($feed->getExtension('Atom') instanceof Zend_Feed_Reader_Extension_Atom_Feed);
    }

    public function testReturnsNullIfExtensionDoesNotExist()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertEquals(null, $feed->getExtension('Foo'));
    }
    
    /**
     * @group ZF-8213
     */
    public function testReturnsEncodingOfFeed()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom.xml')
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }
    
    /**
     * @group ZF-8213
     */
    public function testReturnsEncodingOfFeedAsUtf8IfUndefined()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/atom_noencodingdefined.xml')
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }


}
