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
class Zend_Feed_Reader_Entry_AtomTest extends PHPUnit_Framework_TestCase
{

    protected $_feedSamplePath = null;
    
    protected $_expectedCats = array();
    
    protected $_expectedCatsDc = array();

    public function setup()
    {
        Zend_Feed_Reader::reset();
        if (Zend_Registry::isRegistered('Zend_Locale')) {
            $registry = Zend_Registry::getInstance();
            unset($registry['Zend_Locale']);
        }
        $this->_feedSamplePath = dirname(__FILE__) . '/_files/Atom';
        $this->_options = Zend_Date::setOptions();
        foreach($this->_options as $k=>$v) {
            if (is_null($v)) {
                unset($this->_options[$k]);
            }
        }
        Zend_Date::setOptions(array('format_type'=>'iso'));
        $this->_expectedCats = array(
            array(
                'term' => 'topic1',
                'scheme' => 'http://example.com/schema1',
                'label' => 'topic1'
            ),
            array(
                'term' => 'topic1',
                'scheme' => 'http://example.com/schema2',
                'label' => 'topic1'
            ),
            array(
                'term' => 'cat_dog',
                'scheme' => 'http://example.com/schema1',
                'label' => 'Cat & Dog'
            )
        );
        $this->_expectedCatsDc = array(
            array(
                'term' => 'topic1',
                'scheme' => null,
                'label' => 'topic1'
            ),
            array(
                'term' => 'topic2',
                'scheme' => null,
                'label' => 'topic2'
            )
        );
    }
    
    public function teardown()
    {
        Zend_Date::setOptions($this->_options);
    }

    /**
     * Get Id (Unencoded Text)
     * @group ZFR003
     */
    public function testGetsIdFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/id/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('1', $entry->getId());
    }

    public function testGetsIdFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/id/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('1', $entry->getId());
    }

    /**
     * Get creation date (Unencoded Text)
     */
    public function testGetsDateCreatedFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datecreated/plain/atom03.xml')
        );
        $entry = $feed->current();
        $edate = new Zend_Date;
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($entry->getDateCreated()));
    }

    public function testGetsDateCreatedFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datecreated/plain/atom10.xml')
        );
        $entry = $feed->current();
        $edate = new Zend_Date;
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($entry->getDateCreated()));
    }

    /**
     * Get modification date (Unencoded Text)
     */
    public function testGetsDateModifiedFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datemodified/plain/atom03.xml')
        );
        $entry = $feed->current();
        $edate = new Zend_Date;
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($entry->getDateModified()));
    }

    public function testGetsDateModifiedFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/datemodified/plain/atom10.xml')
        );
        $entry = $feed->current();
        $edate = new Zend_Date;
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($entry->getDateModified()));
    }

    /**
     * Get Title (Unencoded Text)
     */
    public function testGetsTitleFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/title/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    public function testGetsTitleFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/title/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Title', $entry->getTitle());
    }

    /**
     * Get Authors (Unencoded Text)
     */
    public function testGetsAuthorsFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/author/plain/atom03.xml')
        );

        $authors = array(
            array('email'=>'joe@example.com','name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs'),
            array('email'=>'joe@example.com','uri'=>'http://www.example.com'),
            array('uri'=>'http://www.example.com'),
            array('email'=>'joe@example.com')
        );

        $entry = $feed->current();
        $this->assertEquals($authors, (array) $entry->getAuthors());
    }

    public function testGetsAuthorsFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/author/plain/atom10.xml')
        );

        $authors = array(
            array('email'=>'joe@example.com','name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs','uri'=>'http://www.example.com'),
            array('name'=>'Joe Bloggs'),
            array('email'=>'joe@example.com','uri'=>'http://www.example.com'),
            array('uri'=>'http://www.example.com'),
            array('email'=>'joe@example.com')
        );

        $entry = $feed->current();
        $this->assertEquals($authors, (array) $entry->getAuthors());
    }

    /**
     * Get Author (Unencoded Text)
     */
    public function testGetsAuthorFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/author/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('name'=>'Joe Bloggs','email'=>'joe@example.com','uri'=>'http://www.example.com'), $entry->getAuthor());
    }

    public function testGetsAuthorFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/author/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array('name'=>'Joe Bloggs','email'=>'joe@example.com','uri'=>'http://www.example.com'), $entry->getAuthor());
    }

    /**
     * Get Description (Unencoded Text)
     */
    public function testGetsDescriptionFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/description/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    public function testGetsDescriptionFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/description/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Description', $entry->getDescription());
    }

    /**
     * Get enclosure
     */
    public function testGetsEnclosureFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/enclosure/plain/atom03.xml')
        );
        $entry = $feed->current();

        $expected = new stdClass();
        $expected->url    = 'http://www.example.org/myaudiofile.mp3';
        $expected->length = '1234';
        $expected->type   = 'audio/mpeg';

        $this->assertEquals($expected, $entry->getEnclosure());
    }

    public function testGetsEnclosureFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/enclosure/plain/atom10.xml')
        );
        $entry = $feed->current();

        $expected = new stdClass();
        $expected->url    = 'http://www.example.org/myaudiofile.mp3';
        $expected->length = '1234';
        $expected->type   = 'audio/mpeg';

        $this->assertEquals($expected, $entry->getEnclosure());
    }

    /**
     * Get Content (Unencoded Text)
     */
    public function testGetsContentFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/content/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Content', $entry->getContent());
    }

    /**
     * TEXT
     * @group ZFRATOMCONTENT
     */
    public function testGetsContentFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/content/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('Entry Content &amp;', $entry->getContent());
    }
    
    /**
     * HTML Escaped
     * @group ZFRATOMCONTENT
     */
    public function testGetsContentFromAtom10Html()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/content/plain/atom10_Html.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('<p>Entry Content &amp;</p>', $entry->getContent());
    }
    
    /**
     * HTML CDATA Escaped
     * @group ZFRATOMCONTENT
     */
    public function testGetsContentFromAtom10HtmlCdata()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/content/plain/atom10_HtmlCdata.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('<p>Entry Content &amp;</p>', $entry->getContent());
    }
    
    /**
     * XHTML
     * @group ZFRATOMCONTENT
     */
    public function testGetsContentFromAtom10XhtmlNamespaced()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/content/plain/atom10_Xhtml.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('<p class="x:"><em>Entry Content &amp;x:</em></p>', $entry->getContent());
    }

    /**
     * Get Link (Unencoded Text)
     */
    public function testGetsLinkFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/link/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    public function testGetsLinkFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/link/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    public function testGetsLinkFromAtom10_WithNoRelAttribute()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/link/plain/atom10-norel.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    public function testGetsLinkFromAtom10_WithRelativeUrl()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/link/plain/atom10-relative.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry', $entry->getLink());
    }

    /**
     * Get Base Uri
     */
    public function testGetsBaseUriFromAtom10_FromFeedElement()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/baseurl/plain/atom10-feedlevel.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com', $entry->getBaseUrl());
    }

    public function testGetsBaseUriFromAtom10_FromEntryElement()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/baseurl/plain/atom10-entrylevel.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/', $entry->getBaseUrl());
    }

    /**
     * Get Comment HTML Link
     */
    public function testGetsCommentLinkFromAtom03()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/commentlink/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/comments', $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/commentlink/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/comments', $entry->getCommentLink());
    }

    public function testGetsCommentLinkFromAtom10_RelativeLinks()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath . '/commentlink/plain/atom10-relative.xml')
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/entry/comments', $entry->getCommentLink());
    }
    
    /**
     * Get category data
     */
    
    // Atom 1.0 (Atom 0.3 never supported categories except via Atom 1.0/Dublin Core extensions)
    
    public function testGetsCategoriesFromAtom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals($this->_expectedCats, (array) $entry->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($entry->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromAtom03_Atom10Extension()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals($this->_expectedCats, (array) $entry->getCategories());
        $this->assertEquals(array('topic1','Cat & Dog'), array_values($entry->getCategories()->getValues()));
    }
    
    // DC 1.0/1.1 for Atom 0.3
    
    public function testGetsCategoriesFromAtom03_Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc10/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals($this->_expectedCatsDc, (array) $entry->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($entry->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromAtom03_Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc11/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals($this->_expectedCatsDc, (array) $entry->getCategories());
        $this->assertEquals(array('topic1','topic2'), array_values($entry->getCategories()->getValues()));
    }
    
    // No Categories In Entry
    
    public function testGetsCategoriesFromAtom10_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/atom10.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), (array) $entry->getCategories());
        $this->assertEquals(array(), array_values($entry->getCategories()->getValues()));
    }
    
    public function testGetsCategoriesFromAtom03_None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/atom03.xml')
        );
        $entry = $feed->current();
        $this->assertEquals(array(), (array) $entry->getCategories());
        $this->assertEquals(array(), array_values($entry->getCategories()->getValues()));
    }
    
}
