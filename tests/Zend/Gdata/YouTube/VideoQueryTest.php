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
 * @package    Zend_Gdata_YouTube
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once 'Zend/Gdata/YouTube/VideoQuery.php';
require_once 'Zend/Gdata/YouTube.php';

/**
 * @category   Zend
 * @package    Zend_Gdata_YouTube
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_YouTube
 */
class Zend_Gdata_YouTube_VideoQueryTest extends PHPUnit_Framework_TestCase
{

    public function testQueryStringConstruction()
    {
        $yt = new Zend_Gdata_YouTube();
        $query = $yt->newVideoQuery();
        $query->setOrderBy('viewCount');
        $query->setVideoQuery('foobar');
        $expectedString = '?orderby=viewCount&vq=foobar';
        $this->assertEquals($expectedString, $query->getQueryString());
    }

    public function testQueryStringConstructionV2()
    {
        $yt = new Zend_Gdata_YouTube();
        $query = $yt->newVideoQuery();
        $query->setOrderBy('viewCount');
        $query->setVideoQuery('version2');
        $expectedString = '?orderby=viewCount&q=version2';
        $this->assertEquals($expectedString, $query->getQueryString(2));
    }

    public function testSafeSearchQueryV2()
    {
        $yt = new Zend_Gdata_YouTube();
        $query = $yt->newVideoQuery();
        $exceptionCaught = false;
        $query->setRacy('include');
        try {
            $query->getQueryString(2);
        } catch (Zend_Gdata_App_VersionException $e) {
          $exceptionCaught = true;
        }
        $this->assertTrue($exceptionCaught, 'Zend_Gdata_App_VersionException' .
            ' expected but not found');
    }

    public function testLocationRadiusV1()
    {
        $yt = new Zend_Gdata_YouTube();
        $query = $yt->newVideoQuery();
        $exceptionCaught = false;
        $query->setLocationRadius('1km');
        try {
            $query->getQueryString(1);
        } catch (Zend_Gdata_App_VersionException $e) {
          $exceptionCaught = true;
        }
        $this->assertTrue($exceptionCaught, 'Zend_Gdata_App_VersionException' .
            ' expected but not found');
    }

    public function testLocationV2()
    {
        $yt = new Zend_Gdata_YouTube();
        $query = $yt->newVideoQuery();
        $query->setLocation('-37.122,122.01');
        $expectedString = '?location=-37.122%2C122.01';
        $this->assertEquals($expectedString, $query->getQueryString(2));
    }

    public function testLocationExceptionOnNonNumericV2()
    {
        $yt = new Zend_Gdata_YouTube();
        $query = $yt->newVideoQuery();
        $exceptionCaught = false;

        try {
            $query->setLocation('mars');
        } catch (Zend_Gdata_App_InvalidArgumentException $e) {
            $exceptionCaught = true;
        }

        $this->assertTrue($exceptionCaught, 'Expected Zend_Gdata_App_' .
            'IllegalArgumentException when using alpha in setLocation');
    }

    public function testLocationExceptionOnOnlyOneCoordinateV2()
    {
        $yt = new Zend_Gdata_YouTube();
        $query = $yt->newVideoQuery();
        $exceptionCaught = false;

        try {
            $query->setLocation('-25.001');
        } catch (Zend_Gdata_App_InvalidArgumentException $e) {
            $exceptionCaught = true;
        }

        $this->assertTrue($exceptionCaught, 'Expected Zend_Gdata_App_' .
            'IllegalArgumentException when using only 1 coordinate ' .
            'in setLocation');
    }

    public function testUploaderExceptionOnInvalidV2()
    {
        $yt = new Zend_Gdata_YouTube();
        $query = $yt->newVideoQuery();
        $exceptionCaught = false;

        try {
            $query->setUploader('invalid');
        } catch (Zend_Gdata_App_InvalidArgumentException $e) {
            $exceptionCaught = true;
        }

        $this->assertTrue($exceptionCaught, 'Expected Zend_Gdata_App_' .
            'IllegalArgumentException when using invalid string in ' .
            'setUploader.');
    }

    public function testProjectionPresentInV2Query()
    {
        $yt = new Zend_Gdata_YouTube();
        $query = $yt->newVideoQuery();
        $query->setVideoQuery('foo');
        $expectedString = 'https://gdata.youtube.com/feeds/api/videos?q=foo';
        $this->assertEquals($expectedString, $query->getQueryUrl(2));
    }

    public function testSafeSearchParametersInV2()
    {
        $yt = new Zend_Gdata_YouTube();
        $query = $yt->newVideoQuery();
        $exceptionCaught = false;
        try {
            $query->setSafeSearch('invalid');
        } catch (Zend_Gdata_App_InvalidArgumentException $e) {
            $exceptionCaught = true;
        }
        $this->assertTrue($exceptionCaught, 'Expected Zend_Gdata_App_' .
            'InvalidArgumentException when using invalid value for ' .
            'safeSearch.');
    }

    /**
     * @group ZF-8720
     * @expectedException Zend_Gdata_App_InvalidArgumentException
     */
    public function testVideoQuerySetLocationException()
    {
        $yt = new Zend_Gdata_YouTube();
        $query = $yt->newVideoQuery();
        $location = 'foobar';
        $this->assertNull($query->setLocation($location));
    }

    /**
     * @group ZF-8720
     * @expectedException Zend_Gdata_App_InvalidArgumentException
     */
    public function testVideoQuerySetLocationExceptionV2()
    {
        $yt = new Zend_Gdata_YouTube();
        $query = $yt->newVideoQuery();
        $location = '-100x,-200y';
        $this->assertNull($query->setLocation($location));
    }

    /**
     * @group ZF-8720
     * @expectedException Zend_Gdata_App_InvalidArgumentException
     */
    public function testVideoQuerySetLocationExceptionV3()
    {
        $yt = new Zend_Gdata_YouTube();
        $query = $yt->newVideoQuery();
        $location = '-100x,-200y!';
        $this->assertNull($query->setLocation($location));
    }

    /**
     * @group ZF-8720
     */
    public function testQueryExclamationMarkRemoveBug()
    {
        $yt = new Zend_Gdata_YouTube();
        $query = $yt->newVideoQuery();

        $location = '37.42307,-122.08427';
        $this->assertNull($query->setLocation($location));
        $this->assertEquals($location, $query->getLocation());

        $location = '37.42307,-122.08427!';
        $this->assertNull($query->setLocation($location));
        $this->assertEquals($location, $query->getLocation());
    }

    /**
     * @group ZF-12500
     */
    public function testQueryUrlForFeedTypRelated()
    {
        $yt = new Zend_Gdata_YouTube();

        // Query
        $query = $yt->newVideoQuery();
        $query->setFeedType('related', 'foo');

        // Test
        $this->assertSame(
            'https://gdata.youtube.com/feeds/api/videos/foo/related',
            $query->getQueryUrl()
        );
    }

    /**
     * @group ZF-12500
     */
    public function testQueryUrlForFeedTypResponses()
    {
        $yt = new Zend_Gdata_YouTube();

        // Query
        $query = $yt->newVideoQuery();
        $query->setFeedType('responses', 'foo');

        // Test
        $this->assertSame(
            'https://gdata.youtube.com/feeds/api/videos/foo/responses',
            $query->getQueryUrl()
        );
    }

    /**
     * @group ZF-12500
     */
    public function testQueryUrlForFeedTypComments()
    {
        $yt = new Zend_Gdata_YouTube();

        // Query
        $query = $yt->newVideoQuery();
        $query->setFeedType('comments', 'foo');

        // Test
        $this->assertSame(
            'https://gdata.youtube.com/feeds/api/videos/foo/comments',
            $query->getQueryUrl()
        );
    }
}
