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

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Feed
 * @group      Zend_Feed_Reader
 */
class Zend_Feed_Reader_Integration_LautDeRdfTest extends PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;

    public function setup()
    {
        Zend_Feed_Reader::reset();
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/laut.de-rdf.xml';
        $this->_options = Zend_Date::setOptions();
        foreach($this->_options as $k=>$v) {
            if (is_null($v)) {
                unset($this->_options[$k]);
            }
        }
        Zend_Date::setOptions(array('format_type'=>'iso'));
    }
    
    public function teardown()
    {
        Zend_Date::setOptions($this->_options);
    }

    /**
     * Feed level testing
     */

    public function testGetsTitle()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('laut.de - news', $feed->getTitle());
    }

    public function testGetsAuthors()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals(array(array('name'=>'laut.de')), (array) $feed->getAuthors());
    }

    public function testGetsSingleAuthor()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals(array('name'=>'laut.de'), $feed->getAuthor());
    }

    public function testGetsCopyright()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('Copyright © 2004 laut.de', $feed->getCopyright());
    }

    public function testGetsDescription()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('laut.de: aktuelle News', $feed->getDescription());
    }

    public function testGetsLanguage()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLink()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('http://www.laut.de', $feed->getLink());
    }

    public function testGetsEncoding()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('ISO-8859-1', $feed->getEncoding());
    }



    /**
     * Entry level testing
     */

    public function testGetsEntryId()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.laut.de/vorlaut/news/2009/07/04/22426/index.htm', $entry->getId());
    }

    public function testGetsEntryTitle()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('Angelika Express: MySpace-Aus wegen Sido-Werbung', $entry->getTitle());
    }

    public function testGetsEntryAuthors()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(array(array('name'=>'laut.de')), (array) $entry->getAuthors());
    }

    public function testGetsEntrySingleAuthor()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(array('name'=>'laut.de'), $entry->getAuthor());
    }

    // Technically, the next two tests should not pass. However the source feed has an encoding
    // problem - it's stated as ISO-8859-1 but sent as UTF-8. The result is that a) it's
    // broken itself, or b) We should consider a fix in the future for similar feeds such
    // as using a more limited XML based decoding method (not html_entity_decode())

    public function testGetsEntryDescription()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('Schon lÃ¤nger haderten die KÃ¶lner mit der Plattform des "fiesen Rupert Murdoch". Das Fass zum Ãberlaufen brachte aber ein Werbebanner von Deutschrapper Sido.', $entry->getDescription());
    }

    public function testGetsEntryContent()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('Schon lÃ¤nger haderten die KÃ¶lner mit der Plattform des "fiesen Rupert Murdoch". Das Fass zum Ãberlaufen brachte aber ein Werbebanner von Deutschrapper Sido.', $entry->getContent());
    }

    public function testGetsEntryLinks()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(array('http://www.laut.de/vorlaut/news/2009/07/04/22426/index.htm'), $entry->getLinks());
    }

    public function testGetsEntryLink()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.laut.de/vorlaut/news/2009/07/04/22426/index.htm', $entry->getLink());
    }

    public function testGetsEntryPermaLink()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.laut.de/vorlaut/news/2009/07/04/22426/index.htm',
            $entry->getPermaLink());
    }

    public function testGetsEntryEncoding()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('ISO-8859-1', $entry->getEncoding());
    }

}
