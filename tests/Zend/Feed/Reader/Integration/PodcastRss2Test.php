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
class Zend_Feed_Reader_Integration_PodcastRss2Test extends PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;

    public function setup()
    {
        Zend_Feed_Reader::reset();
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/podcast.xml';
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

    public function testGetsNewFeedUrl()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('http://newlocation.com/example.rss', $feed->getNewFeedUrl());
    }

    public function testGetsOwner()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('john.doe@example.com (John Doe)', $feed->getOwner());
    }

    /*
    public function testGetsCategories()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals(array(
            'Technology' => array(
                'Gadgets' => null
            ),
            'TV & Film' => null
        ), $feed->getCategories());
    }
    */

    public function testGetsTitle()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('All About Everything', $feed->getTitle());
    }

    public function testGetsCastAuthor()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('John Doe', $feed->getCastAuthor());
    }

    public function testGetsFeedBlock()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('no', $feed->getBlock());
    }

    public function testGetsCopyright()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('℗ & © 2005 John Doe & Family', $feed->getCopyright());
    }

    public function testGetsDescription()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('All About Everything is a show about everything.
            Each week we dive into any subject known to man and talk
            about it as much as we can. Look for our Podcast in the
            iTunes Store', $feed->getDescription());
    }

    public function testGetsLanguage()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('en-us', $feed->getLanguage());
    }

    public function testGetsLink()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('http://www.example.com/podcasts/everything/index.html', $feed->getLink());
    }

    public function testGetsEncoding()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    public function testGetsFeedExplicit()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('yes', $feed->getExplicit());
    }

    public function testGetsEntryCount()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals(3, $feed->count());
    }

    /*
    public function testGetsImage()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $this->assertEquals('http://example.com/podcasts/everything/AllAboutEverything.jpg', $feed->getImage());
    }
    */

    /**
     * Entry level testing
     */

    public function testGetsEntryBlock()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('yes', $entry->getBlock());
    }

    public function testGetsEntryId()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://example.com/podcasts/archive/aae20050615.m4a', $entry->getId());
    }

    public function testGetsEntryTitle()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('Shake Shake Shake Your Spices', $entry->getTitle());
    }

    public function testGetsEntryCastAuthor()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('John Doe', $entry->getCastAuthor());
    }

    public function testGetsEntryExplicit()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('no', $entry->getExplicit());
    }

    public function testGetsSubtitle()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('A short primer on table spices
            ', $entry->getSubtitle());
    }

    public function testGetsSummary()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('This week we talk about salt and pepper
                shakers, comparing and contrasting pour rates,
                construction materials, and overall aesthetics. Come and
                join the party!', $entry->getSummary());
    }

    public function testGetsDuration()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('7:04', $entry->getDuration());
    }

    public function testGetsKeywords()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('salt, pepper, shaker, exciting
            ', $entry->getKeywords());
    }

    public function testGetsEntryEncoding()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('UTF-8', $entry->getEncoding());
    }

    public function testGetsEnclosure()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath)
        );
        $entry = $feed->current();

        $expected = new stdClass();
        $expected->url    = 'http://example.com/podcasts/everything/AllAboutEverythingEpisode3.m4a';
        $expected->length = '8727310';
        $expected->type   = 'audio/x-m4a';

        $this->assertEquals($expected, $entry->getEnclosure());
    }
}
