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
 * @package    Zend_Date
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/**
 * These const values control some testing behavior.
 * They may be defined here or in TestConfiguration.php.
 */
if (!defined('TESTS_ZEND_LOCALE_BCMATH_ENABLED')) {
    // Set to false to disable usage of bcmath extension by Zend_Date
    define('TESTS_ZEND_LOCALE_BCMATH_ENABLED', true);
}
if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE')) {
    // Set to true to run full Zend_Date unit tests.
    // Set to false to run a good subset of Zend_Date unit tests.
    define('TESTS_ZEND_I18N_EXTENDED_COVERAGE', true);
}

/**
 * Zend_Date
 */
require_once 'Zend/Loader.php';
require_once 'Zend/Date.php';
require_once 'Zend/Locale.php';
require_once 'Zend/Date/Cities.php';
require_once 'Zend/TimeSync.php';

// echo "BCMATH is ", Zend_Locale_Math::isBcmathDisabled() ? 'disabled':'not disabled', "\n";

/**
 * @category   Zend
 * @package    Zend_Date
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Date
 */
class Zend_DateTest extends PHPUnit_Framework_TestCase
{

    private $_cache = null;
    private $_orig  = array();

    public function setUp()
    {
        $this->originalTimezone = date_default_timezone_get();
        date_default_timezone_set('Indian/Maldives');
        require_once 'Zend/Cache.php';
        $this->_cache = Zend_Cache::factory('Core', 'File',
                 array('lifetime' => 120, 'automatic_serialization' => true),
                 array('cache_dir' => dirname(__FILE__) . '/_files/'));
        $this->_orig = Zend_Date::setOptions();

        Zend_Date::setOptions(array('cache' => $this->_cache));
        Zend_Date::setOptions(array('fix_dst' => true));
        Zend_Date::setOptions(array('extend_month' => false));
        Zend_Date::setOptions(array('format_type' => 'iso'));
    }

    public function tearDown()
    {
        Zend_Date::setOptions($this->_orig);
        $this->_cache->clean(Zend_Cache::CLEANING_MODE_ALL);
        date_default_timezone_set($this->originalTimezone);
    }

    /**
     * Test for date object creation
     */
    public function testCreation()
    {
        // look if locale is detectable
        try {
            $locale = new Zend_Locale();
        } catch (Zend_Locale_Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');
            return;
        }

        $date = new Zend_Date(0);
        $this->assertTrue($date instanceof Zend_Date);
    }

    /**
     * Test for date object creation using default format for a locale
     */
    public function testCreationDefaultFormat()
    {
        // look if locale is detectable
        try {
            $locale = new Zend_Locale();
        } catch (Zend_Locale_Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');
            return;
        }

        $date  = new Zend_Date('2006-01-01');
        $this->assertTrue($date instanceof Zend_Date);
        $this->assertSame('2006-01-01T00:00:00+05:00', $date->get(Zend_Date::ISO_8601));

        $date  = new Zend_Date('2006-01-01', 'en_US');
        $this->assertTrue($date instanceof Zend_Date);
        $this->assertSame('2006-01-01T00:00:00+05:00', $date->get(Zend_Date::ISO_8601));
    }

    /**
     * Test for date object creation using default format for a locale
     */
    public function testCreationDefaultFormatConsistency()
    {
        // look if locale is detectable
        try {
            $locale = new Zend_Locale();
        } catch (Zend_Locale_Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');
            return;
        }

        date_default_timezone_set('America/New_York');
        $locale = 'en_US';
        //2006-01-01T00:00:00+05:00
        $date1  = new Zend_Date('2006-01-01 01:00:00', Zend_Date::ISO_8601, $locale);
        $date1string = $date1->get(Zend_Date::ISO_8601);

        // en_US defines AM/PM, hour 0 does not exist
        // ISO defines dates without AM, 0 exists instead of 12 PM
        // therefor hour is set to 1 to verify
        $date2  = new Zend_Date('2006-01-01', Zend_Date::DATES, $locale);
        $date2->setTime('01:00:00');
        $this->assertSame($date1string, $date2->get(Zend_Date::ISO_8601));
        $date2  = new Zend_Date('01-01-2006', Zend_Date::DATES, $locale);
        $date2->setTime('01:00:00');
        $this->assertSame($date1string, $date2->get(Zend_Date::ISO_8601));
        $date2  = new Zend_Date('2006-01-01', null, $locale);
        $date2->setTime('01:00:00');
        $this->assertSame($date1string, $date2->get(Zend_Date::ISO_8601));
        $date2  = new Zend_Date('2006-01-01');
        $date2->setTime('01:00:00');
        $this->assertSame($date1string, $date2->get(Zend_Date::ISO_8601));
        $date2  = new Zend_Date('2006-01-01 01:00:00');
        $this->assertSame($date1string, $date2->get(Zend_Date::ISO_8601));
    }

    /**
     * Test for creation with timestamp
     */
    public function testCreationTimestamp()
    {
        // look if locale is detectable
        try {
            $locale = new Zend_Locale();
        } catch (Zend_Locale_Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');
            return;
        }

        $date = new Zend_Date('12345678');
        $this->assertTrue($date instanceof Zend_Date);
    }

    /**
     * Test for creation but only part of date
     */
    public function testCreationDatePart()
    {
        // look if locale is detectable
        try {
            $locale = new Zend_Locale();
        } catch (Zend_Locale_Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');
            return;
        }

        $date = new Zend_Date('13',Zend_Date::HOUR);
        $this->assertTrue($date instanceof Zend_Date);

        $date = new Zend_Date('20070802', 'YYYYMMdd');
        $this->assertSame("2007-08-02T00:00:00+05:00", $date->getIso());
    }

    /**
     * Test for creation but only a defined locale
     */
    public function testCreationLocale()
    {
        $locale = new Zend_Locale('de_AT');
        $date   = new Zend_Date('13',null,$locale);
        $this->assertTrue($date instanceof Zend_Date);
    }

    /**
     * Test for creation but only part of date with locale
     */
    public function testCreationLocalePart()
    {
        $locale = new Zend_Locale('de_AT');
        $date   = new Zend_Date('13',Zend_Date::HOUR,$locale);
        $this->assertTrue($date instanceof Zend_Date);
    }

    /**
     * Test for date object creation using default format for a locale
     */
    public function testCreationDefaultLoose()
    {
        // look if locale is detectable
        try {
            $locale = new Zend_Locale();
        } catch (Zend_Locale_Exception $e) {
            $this->markTestSkipped('Autodetection of locale failed');
            return;
        }

        $locale = 'de_AT';
        $date  = new Zend_Date();

        $date = $date->getTimestamp();
        $this->assertTrue(abs($date - time()) < 2);

        date_default_timezone_set('GMT');
        $date = new Zend_Date(Zend_Date::YEAR);

        $date = $date->getTimestamp();
        $reference = gmmktime(0,0,0,1,1,date('Y'));
        $this->assertTrue($reference == $date);

        $date = new Zend_Date('ar_EG');
        $this->assertSame('ar_EG', $date->getLocale());
        $date = $date->getTimestamp();
        $this->assertTrue(abs($date - time()) < 2);
    }

    /**
     * Test for getTimestamp
     */
    public function testGetTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
        $date   = new Zend_Date(10000000);
        $this->assertSame(10000000, $date->getTimestamp());
    }

    /**
     * Test for getUnixTimestamp
     */
    public function testgetUnixTimestamp2()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(-100000000);
        $this->assertSame(-100000000, $date->getTimestamp());
    }

    /**
     * Test for setTimestamp
     */
    public function testSetTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,Zend_Date::TIMESTAMP,$locale);
        $result = $date->setTimestamp(10000000);
        $this->assertSame('10000000', (string)$result->getTimestamp());
    }

    /**
     * Test for setTimestamp
     */
    public function testSetTimestamp2()
    {
        try {
            $locale = new Zend_Locale('de_AT');
            $date = new Zend_Date(0,null,$locale);
            $result = $date->setTimestamp('notimestamp');
            $this->Fail("exception expected");
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    /**
     * Test for addTimestamp
     */
    public function testAddTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $result = $date->addTimestamp(10000000);
        $this->assertSame('10000000', (string)$result->getTimestamp());

        $result = $date->addTimestamp(array('timestamp' => 1000));
        $this->assertSame('10001000', (string)$result->getTimestamp());

        try {
            $result = $date->addTimestamp(array('notimestamp' => 1000));
            $this->fail("exception expected");
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    /**
     * Test for addTimestamp
     */
    public function testAddTimestamp2()
    {
        try {
            $locale = new Zend_Locale('de_AT');
            $date = new Zend_Date(0,null,$locale);
            $result = $date->addTimestamp('notimestamp');
            $this->fail("exception expected");
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    /**
     * Test for subTimestamp
     */
    public function testSubTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $result = $date->subTimestamp(10000000);
        $this->assertSame('-10000000', (string)$result->getTimestamp());
    }

    /**
     * Test for subTimestamp
     */
    public function testSubTimestamp2()
    {
        try {
            $locale = new Zend_Locale('de_AT');
            $date = new Zend_Date(0,null,$locale);
            $result = $date->subTimestamp('notimestamp');
            $this->fail("exception expected");
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    /**
     * Test for compareTimestamp
     */
    public function testCompareTimestamp()
    {
        $locale = new Zend_Locale('de_AT');
        $date1 = new Zend_Date(0,null,$locale);
        $date2 = new Zend_Date(0,null,$locale);
        $this->assertSame(0, $date1->compareTimestamp($date2));

        $date2 = new Zend_Date(100,null,$locale);
        $this->assertSame(-1, $date1->compareTimestamp($date2));

        $date2 = new Zend_Date(-100,null,$locale);
        $this->assertSame(1, $date1->compareTimestamp($date2));
    }

    /**
     * Test for __toString
     */
    public function test_ToString()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $date->setTimezone(date_default_timezone_get());
        $this->assertSame('01.01.1970 05:00:00', $date->__toString());
    }

    /**
     * Test for toString
     */
    public function testToString()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $date->setTimezone(date_default_timezone_get());
        $this->assertSame('14.02.2009 04:31:30',     $date->toString(             ));
        $this->assertSame('Feb 14, 2009 4:31:30 AM', $date->toString('en_US'      ));
        $this->assertSame('Feb 14, 2009 4:31:30 AM', $date->toString(null, 'en_US'));
        $this->assertSame('2009',                    $date->toString('yyy', null  ));
        $this->assertSame('14.02.2009 04:31:30',     $date->toString(null,  null  ));

        $date->setTimeZone('UTC');
        $this->assertSame('Feb 13, 2009 11:31:30 PM', $date->toString(null, 'en_US'));

        $date->setTimeZone('Indian/Maldives');
        $this->assertSame(      "xxyy'yyxx", $date->toString("xx'yy''yy'xx"));
        $this->assertSame(             'n.', $date->toString("GGGGG"));
        $this->assertSame(        'n. Chr.', $date->toString( "GGGG"));
        $this->assertSame(        'n. Chr.', $date->toString(  "GGG"));
        $this->assertSame(        'n. Chr.', $date->toString(   "GG"));
        $this->assertSame(        'n. Chr.', $date->toString(    "G"));
        $this->assertSame(          '02009', $date->toString("yyyyy"));
        $this->assertSame(           '2009', $date->toString( "yyyy"));
        $this->assertSame(           '2009', $date->toString(  "yyy"));
        $this->assertSame(             '09', $date->toString(   "yy"));
        $this->assertSame(           '2009', $date->toString(    "y"));
        $this->assertSame(          '02009', $date->toString("YYYYY"));
        $this->assertSame(           '2009', $date->toString( "YYYY"));
        $this->assertSame(           '2009', $date->toString(  "YYY"));
        $this->assertSame(             '09', $date->toString(   "YY"));
        $this->assertSame(           '2009', $date->toString(    "Y"));
        $this->assertSame(              'F', $date->toString("MMMMM"));
        $this->assertSame(        'Februar', $date->toString( "MMMM"));
        $this->assertSame(           'Feb.', $date->toString(  "MMM"));
        $this->assertSame(             '02', $date->toString(   "MM"));
        $this->assertSame(              '2', $date->toString(    "M"));
        $this->assertSame(             '07', $date->toString(   "ww"));
        $this->assertSame(              '7', $date->toString(    "w"));
        $this->assertSame(             '14', $date->toString(   "dd"));
        $this->assertSame(             '14', $date->toString(    "d"));
        $this->assertSame(            '044', $date->toString(  "DDD"));
        $this->assertSame(             '44', $date->toString(   "DD"));
        $this->assertSame(             '44', $date->toString(    "D"));
        $this->assertSame(              'S', $date->toString("EEEEE"));
        $this->assertSame(        'Samstag', $date->toString( "EEEE"));
        $this->assertSame(            'Sam', $date->toString(  "EEE"));
        $this->assertSame(            'Sa.', $date->toString(   "EE"));
        $this->assertSame(              'S', $date->toString(    "E"));
        $this->assertSame(             '06', $date->toString(   "ee"));
        $this->assertSame(              '6', $date->toString(    "e"));
        $this->assertSame(          'vorm.', $date->toString(    "a"));
        $this->assertSame(             '04', $date->toString(   "hh"));
        $this->assertSame(              '4', $date->toString(    "h"));
        $this->assertSame(             '04', $date->toString(   "HH"));
        $this->assertSame(              '4', $date->toString(    "H"));
        $this->assertSame(             '31', $date->toString(   "mm"));
        $this->assertSame(             '31', $date->toString(    "m"));
        $this->assertSame(             '30', $date->toString(   "ss"));
        $this->assertSame(             '30', $date->toString(    "s"));
        $this->assertSame(              '0', $date->toString(    "S"));
        $this->assertSame('Indian/Maldives', $date->toString( "zzzz"));
        $this->assertSame(            'MVT', $date->toString(  "zzz"));
        $this->assertSame(            'MVT', $date->toString(   "zz"));
        $this->assertSame(            'MVT', $date->toString(    "z"));
        $this->assertSame(         '+05:00', $date->toString( "ZZZZ"));
        $this->assertSame(          '+0500', $date->toString(  "ZZZ"));
        $this->assertSame(          '+0500', $date->toString(   "ZZ"));
        $this->assertSame(          '+0500', $date->toString(    "Z"));
        $this->assertSame(       '16290000', $date->toString("AAAAA"));
        $this->assertSame(       '16290000', $date->toString( "AAAA"));
        $this->assertSame(       '16290000', $date->toString(  "AAA"));
        $this->assertSame(       '16290000', $date->toString(   "AA"));
        $this->assertSame(       '16290000', $date->toString(    "A"));

        $date = new Zend_Date("1-1-01",null,$locale);
        $date->setTimezone(date_default_timezone_get());
        $this->assertSame('01', $date->toString("yy"));
    }

    /**
     * Test for toValue
     */
    public function testToValue()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $date->setTimezone(date_default_timezone_get());
        $this->assertSame(1234567890, $date->toValue()              );
        $this->assertSame(        14, $date->toValue(Zend_Date::DAY));

        $date->setTimezone('UTC');
        $this->assertSame(        13, $date->toValue(Zend_Date::DAY              ));
        $this->assertFalse(           $date->toValue(Zend_Date::WEEKDAY_SHORT    ));
        $this->assertSame(        13, $date->toValue(Zend_Date::DAY_SHORT        ));
        $this->assertFalse(           $date->toValue(Zend_Date::WEEKDAY          ));
        $this->assertSame(         5, $date->toValue(Zend_Date::WEEKDAY_8601     ));
        $this->assertFalse(           $date->toValue(Zend_Date::DAY_SUFFIX       ));
        $this->assertSame(         5, $date->toValue(Zend_Date::WEEKDAY_DIGIT    ));
        $this->assertSame(        43, $date->toValue(Zend_Date::DAY_OF_YEAR      ));
        $this->assertFalse(           $date->toValue(Zend_Date::WEEKDAY_NARROW   ));
        $this->assertFalse(           $date->toValue(Zend_Date::WEEKDAY_NAME     ));
        $this->assertSame(         7, $date->toValue(Zend_Date::WEEK             ));
        $this->assertFalse(           $date->toValue(Zend_Date::MONTH_NAME       ));
        $this->assertSame(         2, $date->toValue(Zend_Date::MONTH            ));
        $this->assertFalse(           $date->toValue(Zend_Date::MONTH_NAME_SHORT ));
        $this->assertSame(         2, $date->toValue(Zend_Date::MONTH_SHORT      ));
        $this->assertSame(        28, $date->toValue(Zend_Date::MONTH_DAYS       ));
        $this->assertFalse(           $date->toValue(Zend_Date::MONTH_NAME_NARROW));
        $this->assertSame(         0, $date->toValue(Zend_Date::LEAPYEAR         ));
        $this->assertSame(      2009, $date->toValue(Zend_Date::YEAR_8601        ));
        $this->assertSame(      2009, $date->toValue(Zend_Date::YEAR             ));
        $this->assertSame(         9, $date->toValue(Zend_Date::YEAR_SHORT       ));
        $this->assertSame(         9, $date->toValue(Zend_Date::YEAR_SHORT_8601  ));
        $this->assertFalse(           $date->toValue(Zend_Date::MERIDIEM         ));
        $this->assertSame(        21, $date->toValue(Zend_Date::SWATCH           ));
        $this->assertSame(        11, $date->toValue(Zend_Date::HOUR_SHORT_AM    ));
        $this->assertSame(        23, $date->toValue(Zend_Date::HOUR_SHORT       ));
        $this->assertSame(        11, $date->toValue(Zend_Date::HOUR_AM          ));
        $this->assertSame(        23, $date->toValue(Zend_Date::HOUR             ));
        $this->assertSame(        31, $date->toValue(Zend_Date::MINUTE           ));
        $this->assertSame(        30, $date->toValue(Zend_Date::SECOND           ));
        $this->assertSame(         0, $date->toValue(Zend_Date::MILLISECOND      ));
        $this->assertSame(        31, $date->toValue(Zend_Date::MINUTE_SHORT     ));
        $this->assertSame(        30, $date->toValue(Zend_Date::SECOND_SHORT     ));
        $this->assertFalse(           $date->toValue(Zend_Date::TIMEZONE_NAME    ));
        $this->assertSame(         0, $date->toValue(Zend_Date::DAYLIGHT         ));
        $this->assertSame(         0, $date->toValue(Zend_Date::GMT_DIFF         ));
        $this->assertFalse(           $date->toValue(Zend_Date::GMT_DIFF_SEP     ));
        $this->assertFalse(           $date->toValue(Zend_Date::TIMEZONE         ));
        $this->assertSame(         0, $date->toValue(Zend_Date::TIMEZONE_SECS    ));
        $this->assertFalse(           $date->toValue(Zend_Date::ISO_8601         ));
        $this->assertFalse(           $date->toValue(Zend_Date::RFC_2822         ));
        $this->assertSame(1234567890, $date->toValue(Zend_Date::TIMESTAMP        ));
        $this->assertFalse(           $date->toValue(Zend_Date::ERA              ));
        $this->assertFalse(           $date->toValue(Zend_Date::ERA_NAME         ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATES            ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATE_FULL        ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATE_LONG        ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATE_MEDIUM      ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATE_SHORT       ));
        $this->assertFalse(           $date->toValue(Zend_Date::TIMES            ));
        $this->assertFalse(           $date->toValue(Zend_Date::TIME_FULL        ));
        $this->assertFalse(           $date->toValue(Zend_Date::TIME_LONG        ));
        $this->assertFalse(           $date->toValue(Zend_Date::TIME_MEDIUM      ));
        $this->assertFalse(           $date->toValue(Zend_Date::TIME_SHORT       ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATETIME         ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATETIME_FULL    ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATETIME_LONG    ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATETIME_MEDIUM  ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATETIME_SHORT   ));
        $this->assertFalse(           $date->toValue(Zend_Date::ATOM             ));
        $this->assertFalse(           $date->toValue(Zend_Date::COOKIE           ));
        $this->assertFalse(           $date->toValue(Zend_Date::RFC_822          ));
        $this->assertFalse(           $date->toValue(Zend_Date::RFC_850          ));
        $this->assertFalse(           $date->toValue(Zend_Date::RFC_1036         ));
        $this->assertFalse(           $date->toValue(Zend_Date::RFC_1123         ));
        $this->assertFalse(           $date->toValue(Zend_Date::RFC_3339         ));
        $this->assertFalse(           $date->toValue(Zend_Date::RSS              ));
        $this->assertFalse(           $date->toValue(Zend_Date::W3C              ));

        $date->setTimezone('Indian/Maldives');
        $this->assertFalse(           $date->toValue(Zend_Date::WEEKDAY_SHORT    ));
        $this->assertSame(        14, $date->toValue(Zend_Date::DAY_SHORT        ));
        $this->assertFalse(           $date->toValue(Zend_Date::WEEKDAY          ));
        $this->assertSame(         6, $date->toValue(Zend_Date::WEEKDAY_8601     ));
        $this->assertFalse(           $date->toValue(Zend_Date::DAY_SUFFIX       ));
        $this->assertSame(         6, $date->toValue(Zend_Date::WEEKDAY_DIGIT    ));
        $this->assertSame(        44, $date->toValue(Zend_Date::DAY_OF_YEAR      ));
        $this->assertFalse(           $date->toValue(Zend_Date::WEEKDAY_NARROW   ));
        $this->assertFalse(           $date->toValue(Zend_Date::WEEKDAY_NAME     ));
        $this->assertSame(         7, $date->toValue(Zend_Date::WEEK             ));
        $this->assertFalse(           $date->toValue(Zend_Date::MONTH_NAME       ));
        $this->assertSame(         2, $date->toValue(Zend_Date::MONTH            ));
        $this->assertFalse(           $date->toValue(Zend_Date::MONTH_NAME_SHORT ));
        $this->assertSame(         2, $date->toValue(Zend_Date::MONTH_SHORT      ));
        $this->assertSame(        28, $date->toValue(Zend_Date::MONTH_DAYS       ));
        $this->assertFalse(           $date->toValue(Zend_Date::MONTH_NAME_NARROW));
        $this->assertSame(         0, $date->toValue(Zend_Date::LEAPYEAR         ));
        $this->assertSame(      2009, $date->toValue(Zend_Date::YEAR_8601        ));
        $this->assertSame(      2009, $date->toValue(Zend_Date::YEAR             ));
        $this->assertSame(         9, $date->toValue(Zend_Date::YEAR_SHORT       ));
        $this->assertSame(         9, $date->toValue(Zend_Date::YEAR_SHORT_8601  ));
        $this->assertFalse(           $date->toValue(Zend_Date::MERIDIEM         ));
        $this->assertSame(        21, $date->toValue(Zend_Date::SWATCH           ));
        $this->assertSame(         4, $date->toValue(Zend_Date::HOUR_SHORT_AM    ));
        $this->assertSame(         4, $date->toValue(Zend_Date::HOUR_SHORT       ));
        $this->assertSame(         4, $date->toValue(Zend_Date::HOUR_AM          ));
        $this->assertSame(         4, $date->toValue(Zend_Date::HOUR             ));
        $this->assertSame(        31, $date->toValue(Zend_Date::MINUTE           ));
        $this->assertSame(        30, $date->toValue(Zend_Date::SECOND           ));
        $this->assertSame(         0, $date->toValue(Zend_Date::MILLISECOND      ));
        $this->assertSame(        31, $date->toValue(Zend_Date::MINUTE_SHORT     ));
        $this->assertSame(        30, $date->toValue(Zend_Date::SECOND_SHORT     ));
        $this->assertFalse(           $date->toValue(Zend_Date::TIMEZONE_NAME    ));
        $this->assertSame(         0, $date->toValue(Zend_Date::DAYLIGHT         ));
        $this->assertSame(       500, $date->toValue(Zend_Date::GMT_DIFF         ));
        $this->assertFalse(           $date->toValue(Zend_Date::GMT_DIFF_SEP     ));
        $this->assertFalse(           $date->toValue(Zend_Date::TIMEZONE         ));
        $this->assertSame(     18000, $date->toValue(Zend_Date::TIMEZONE_SECS    ));
        $this->assertFalse(           $date->toValue(Zend_Date::ISO_8601         ));
        $this->assertFalse(           $date->toValue(Zend_Date::RFC_2822         ));
        $this->assertSame(1234567890, $date->toValue(Zend_Date::TIMESTAMP        ));
        $this->assertFalse(           $date->toValue(Zend_Date::ERA              ));
        $this->assertFalse(           $date->toValue(Zend_Date::ERA_NAME         ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATES            ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATE_FULL        ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATE_LONG        ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATE_MEDIUM      ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATE_SHORT       ));
        $this->assertFalse(           $date->toValue(Zend_Date::TIMES            ));
        $this->assertFalse(           $date->toValue(Zend_Date::TIME_FULL        ));
        $this->assertFalse(           $date->toValue(Zend_Date::TIME_LONG        ));
        $this->assertFalse(           $date->toValue(Zend_Date::TIME_MEDIUM      ));
        $this->assertFalse(           $date->toValue(Zend_Date::TIME_SHORT       ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATETIME         ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATETIME_FULL    ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATETIME_LONG    ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATETIME_MEDIUM  ));
        $this->assertFalse(           $date->toValue(Zend_Date::DATETIME_SHORT   ));
        $this->assertFalse(           $date->toValue(Zend_Date::ATOM             ));
        $this->assertFalse(           $date->toValue(Zend_Date::COOKIE           ));
        $this->assertFalse(           $date->toValue(Zend_Date::RFC_822          ));
        $this->assertFalse(           $date->toValue(Zend_Date::RFC_850          ));
        $this->assertFalse(           $date->toValue(Zend_Date::RFC_1036         ));
        $this->assertFalse(           $date->toValue(Zend_Date::RFC_1123         ));
        $this->assertFalse(           $date->toValue(Zend_Date::RFC_3339         ));
        $this->assertFalse(           $date->toValue(Zend_Date::RSS              ));
        $this->assertFalse(           $date->toValue(Zend_Date::W3C              ));
    }

    /**
     * Test for toValue
     */
    public function testGet()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);

        $date->setTimezone('UTC');
        $this->assertSame(                             '13', $date->get(Zend_Date::DAY              ));
        $this->assertSame(                            'Fre', $date->get(Zend_Date::WEEKDAY_SHORT    ));
        $this->assertSame(                             '13', $date->get(Zend_Date::DAY_SHORT        ));
        $this->assertSame(                        'Freitag', $date->get(Zend_Date::WEEKDAY          ));
        $this->assertSame(                              '5', $date->get(Zend_Date::WEEKDAY_8601     ));
        $this->assertSame(                             'th', $date->get(Zend_Date::DAY_SUFFIX       ));
        $this->assertSame(                              '5', $date->get(Zend_Date::WEEKDAY_DIGIT    ));
        $this->assertSame(                             '43', $date->get(Zend_Date::DAY_OF_YEAR      ));
        $this->assertSame(                              'F', $date->get(Zend_Date::WEEKDAY_NARROW   ));
        $this->assertSame(                            'Fr.', $date->get(Zend_Date::WEEKDAY_NAME     ));
        $this->assertSame(                             '07', $date->get(Zend_Date::WEEK             ));
        $this->assertSame(                        'Februar', $date->get(Zend_Date::MONTH_NAME       ));
        $this->assertSame(                             '02', $date->get(Zend_Date::MONTH            ));
        $this->assertSame(                           'Feb.', $date->get(Zend_Date::MONTH_NAME_SHORT ));
        $this->assertSame(                              '2', $date->get(Zend_Date::MONTH_SHORT      ));
        $this->assertSame(                             '28', $date->get(Zend_Date::MONTH_DAYS       ));
        $this->assertSame(                              'F', $date->get(Zend_Date::MONTH_NAME_NARROW));
        $this->assertSame(                              '0', $date->get(Zend_Date::LEAPYEAR         ));
        $this->assertSame(                           '2009', $date->get(Zend_Date::YEAR_8601        ));
        $this->assertSame(                           '2009', $date->get(Zend_Date::YEAR             ));
        $this->assertSame(                             '09', $date->get(Zend_Date::YEAR_SHORT       ));
        $this->assertSame(                             '09', $date->get(Zend_Date::YEAR_SHORT_8601  ));
        $this->assertSame(                         'nachm.', $date->get(Zend_Date::MERIDIEM         ));
        $this->assertSame(                            '021', $date->get(Zend_Date::SWATCH           ));
        $this->assertSame(                             '11', $date->get(Zend_Date::HOUR_SHORT_AM    ));
        $this->assertSame(                             '23', $date->get(Zend_Date::HOUR_SHORT       ));
        $this->assertSame(                             '11', $date->get(Zend_Date::HOUR_AM          ));
        $this->assertSame(                             '23', $date->get(Zend_Date::HOUR             ));
        $this->assertSame(                             '31', $date->get(Zend_Date::MINUTE           ));
        $this->assertSame(                             '30', $date->get(Zend_Date::SECOND           ));
        $this->assertSame(                              '0', $date->get(Zend_Date::MILLISECOND      ));
        $this->assertSame(                             '31', $date->get(Zend_Date::MINUTE_SHORT     ));
        $this->assertSame(                             '30', $date->get(Zend_Date::SECOND_SHORT     ));
        $this->assertSame(                            'UTC', $date->get(Zend_Date::TIMEZONE_NAME    ));
        $this->assertSame(                              '0', $date->get(Zend_Date::DAYLIGHT         ));
        $this->assertSame(                          '+0000', $date->get(Zend_Date::GMT_DIFF         ));
        $this->assertSame(                         '+00:00', $date->get(Zend_Date::GMT_DIFF_SEP     ));
        $this->assertSame(                            'UTC', $date->get(Zend_Date::TIMEZONE         ));
        $this->assertSame(                              '0', $date->get(Zend_Date::TIMEZONE_SECS    ));
        $this->assertSame(      '2009-02-13T23:31:30+00:00', $date->get(Zend_Date::ISO_8601         ));
        $this->assertSame('Fri, 13 Feb 2009 23:31:30 +0000', $date->get(Zend_Date::RFC_2822         ));
        $this->assertSame(                     '1234567890', $date->get(Zend_Date::TIMESTAMP        ));
        $this->assertSame(                        'n. Chr.', $date->get(Zend_Date::ERA              ));
        $this->assertSame(                        'n. Chr.', $date->get(Zend_Date::ERA_NAME         ));
        $this->assertSame(                     '13.02.2009', $date->get(Zend_Date::DATES            ));
        $this->assertSame(      'Freitag, 13. Februar 2009', $date->get(Zend_Date::DATE_FULL        ));
        $this->assertSame(               '13. Februar 2009', $date->get(Zend_Date::DATE_LONG        ));
        $this->assertSame(                     '13.02.2009', $date->get(Zend_Date::DATE_MEDIUM      ));
        $this->assertSame(                       '13.02.09', $date->get(Zend_Date::DATE_SHORT       ));
        $this->assertSame(                       '23:31:30', $date->get(Zend_Date::TIMES            ));
        $this->assertSame(                   '23:31:30 UTC', $date->get(Zend_Date::TIME_FULL        ));
        $this->assertSame(                   '23:31:30 UTC', $date->get(Zend_Date::TIME_LONG        ));
        $this->assertSame(                       '23:31:30', $date->get(Zend_Date::TIME_MEDIUM      ));
        $this->assertSame(                          '23:31', $date->get(Zend_Date::TIME_SHORT       ));
        $this->assertSame(            '13.02.2009 23:31:30', $date->get(Zend_Date::DATETIME         ));
        $this->assertSame('Freitag, 13. Februar 2009 23:31:30 UTC', $date->get(Zend_Date::DATETIME_FULL    ));
        $this->assertSame(  '13. Februar 2009 23:31:30 UTC', $date->get(Zend_Date::DATETIME_LONG    ));
        $this->assertSame(            '13.02.2009 23:31:30', $date->get(Zend_Date::DATETIME_MEDIUM  ));
        $this->assertSame(                 '13.02.09 23:31', $date->get(Zend_Date::DATETIME_SHORT   ));
        $this->assertSame(      '2009-02-13T23:31:30+00:00', $date->get(Zend_Date::ATOM             ));
        $this->assertSame( 'Friday, 13-Feb-09 23:31:30 UTC', $date->get(Zend_Date::COOKIE           ));
        $this->assertSame(  'Fri, 13 Feb 09 23:31:30 +0000', $date->get(Zend_Date::RFC_822          ));
        $this->assertSame( 'Friday, 13-Feb-09 23:31:30 UTC', $date->get(Zend_Date::RFC_850          ));
        $this->assertSame(  'Fri, 13 Feb 09 23:31:30 +0000', $date->get(Zend_Date::RFC_1036         ));
        $this->assertSame('Fri, 13 Feb 2009 23:31:30 +0000', $date->get(Zend_Date::RFC_1123         ));
        $this->assertSame(      '2009-02-13T23:31:30+00:00', $date->get(Zend_Date::RFC_3339         ));
        $this->assertSame('Fri, 13 Feb 2009 23:31:30 +0000', $date->get(Zend_Date::RSS              ));
        $this->assertSame(      '2009-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C              ));

        $this->assertSame(                             '13', $date->get(Zend_Date::DAY,               'es'));
        $this->assertSame(                            'vie', $date->get(Zend_Date::WEEKDAY_SHORT,     'es'));
        $this->assertSame(                             '13', $date->get(Zend_Date::DAY_SHORT,         'es'));
        $this->assertSame(                        'viernes', $date->get(Zend_Date::WEEKDAY,           'es'));
        $this->assertSame(                              '5', $date->get(Zend_Date::WEEKDAY_8601,      'es'));
        $this->assertSame(                             'th', $date->get(Zend_Date::DAY_SUFFIX,        'es'));
        $this->assertSame(                              '5', $date->get(Zend_Date::WEEKDAY_DIGIT,     'es'));
        $this->assertSame(                             '43', $date->get(Zend_Date::DAY_OF_YEAR,       'es'));
        $this->assertSame(                              'v', $date->get(Zend_Date::WEEKDAY_NARROW,    'es'));
        $this->assertSame(                           'vie.', $date->get(Zend_Date::WEEKDAY_NAME,      'es'));
        $this->assertSame(                             '07', $date->get(Zend_Date::WEEK,              'es'));
        $this->assertSame(                        'febrero', $date->get(Zend_Date::MONTH_NAME,        'es'));
        $this->assertSame(                             '02', $date->get(Zend_Date::MONTH,             'es'));
        $this->assertSame(                           'feb.', $date->get(Zend_Date::MONTH_NAME_SHORT,  'es'));
        $this->assertSame(                              '2', $date->get(Zend_Date::MONTH_SHORT,       'es'));
        $this->assertSame(                             '28', $date->get(Zend_Date::MONTH_DAYS,        'es'));
        $this->assertSame(                              'f', $date->get(Zend_Date::MONTH_NAME_NARROW, 'es'));
        $this->assertSame(                              '0', $date->get(Zend_Date::LEAPYEAR,          'es'));
        $this->assertSame(                           '2009', $date->get(Zend_Date::YEAR_8601,         'es'));
        $this->assertSame(                           '2009', $date->get(Zend_Date::YEAR,              'es'));
        $this->assertSame(                             '09', $date->get(Zend_Date::YEAR_SHORT,        'es'));
        $this->assertSame(                             '09', $date->get(Zend_Date::YEAR_SHORT_8601,   'es'));
        $this->assertSame(                          'p. m.', $date->get(Zend_Date::MERIDIEM,          'es'));
        $this->assertSame(                            '021', $date->get(Zend_Date::SWATCH,            'es'));
        $this->assertSame(                             '11', $date->get(Zend_Date::HOUR_SHORT_AM,     'es'));
        $this->assertSame(                             '23', $date->get(Zend_Date::HOUR_SHORT,        'es'));
        $this->assertSame(                             '11', $date->get(Zend_Date::HOUR_AM,           'es'));
        $this->assertSame(                             '23', $date->get(Zend_Date::HOUR,              'es'));
        $this->assertSame(                             '31', $date->get(Zend_Date::MINUTE,            'es'));
        $this->assertSame(                             '30', $date->get(Zend_Date::SECOND,            'es'));
        $this->assertSame(                              '0', $date->get(Zend_Date::MILLISECOND,       'es'));
        $this->assertSame(                             '31', $date->get(Zend_Date::MINUTE_SHORT,      'es'));
        $this->assertSame(                             '30', $date->get(Zend_Date::SECOND_SHORT,      'es'));
        $this->assertSame(                            'UTC', $date->get(Zend_Date::TIMEZONE_NAME,     'es'));
        $this->assertSame(                              '0', $date->get(Zend_Date::DAYLIGHT,          'es'));
        $this->assertSame(                          '+0000', $date->get(Zend_Date::GMT_DIFF,          'es'));
        $this->assertSame(                         '+00:00', $date->get(Zend_Date::GMT_DIFF_SEP,      'es'));
        $this->assertSame(                            'UTC', $date->get(Zend_Date::TIMEZONE,          'es'));
        $this->assertSame(                              '0', $date->get(Zend_Date::TIMEZONE_SECS,     'es'));
        $this->assertSame(      '2009-02-13T23:31:30+00:00', $date->get(Zend_Date::ISO_8601,          'es'));
        $this->assertSame('Fri, 13 Feb 2009 23:31:30 +0000', $date->get(Zend_Date::RFC_2822,          'es'));
        $this->assertSame(                     '1234567890', $date->get(Zend_Date::TIMESTAMP,         'es'));
        $this->assertSame(                          'd. C.', $date->get(Zend_Date::ERA,               'es'));
        $this->assertSame(                    'anno Dómini', $date->get(Zend_Date::ERA_NAME,          'es'));
        $this->assertSame(                      '13/2/2009', $date->get(Zend_Date::DATES,             'es'));
        $this->assertSame( 'viernes, 13 de febrero de 2009', $date->get(Zend_Date::DATE_FULL,         'es'));
        $this->assertSame(          '13 de febrero de 2009', $date->get(Zend_Date::DATE_LONG,         'es'));
        $this->assertSame(                      '13/2/2009', $date->get(Zend_Date::DATE_MEDIUM,       'es'));
        $this->assertSame(                        '13/2/09', $date->get(Zend_Date::DATE_SHORT,        'es'));
        $this->assertSame(                       '23:31:30', $date->get(Zend_Date::TIMES,             'es'));
        $this->assertSame(                 '23:31:30 (UTC)', $date->get(Zend_Date::TIME_FULL,         'es'));
        $this->assertSame(                   '23:31:30 UTC', $date->get(Zend_Date::TIME_LONG,         'es'));
        $this->assertSame(                       '23:31:30', $date->get(Zend_Date::TIME_MEDIUM,       'es'));
        $this->assertSame(                          '23:31', $date->get(Zend_Date::TIME_SHORT,        'es'));
        $this->assertSame(             '13/2/2009 23:31:30', $date->get(Zend_Date::DATETIME,          'es'));
        $this->assertSame('viernes, 13 de febrero de 2009, 23:31:30 (UTC)', $date->get(Zend_Date::DATETIME_FULL, 'es'));
        $this->assertSame('13 de febrero de 2009, 23:31:30 UTC', $date->get(Zend_Date::DATETIME_LONG,  'es'));
        $this->assertSame(             '13/2/2009 23:31:30', $date->get(Zend_Date::DATETIME_MEDIUM,   'es'));
        $this->assertSame(                  '13/2/09 23:31', $date->get(Zend_Date::DATETIME_SHORT,    'es'));
        $this->assertSame(      '2009-02-13T23:31:30+00:00', $date->get(Zend_Date::ATOM,              'es'));
        $this->assertSame( 'Friday, 13-Feb-09 23:31:30 UTC', $date->get(Zend_Date::COOKIE,            'es'));
        $this->assertSame(  'Fri, 13 Feb 09 23:31:30 +0000', $date->get(Zend_Date::RFC_822,           'es'));
        $this->assertSame( 'Friday, 13-Feb-09 23:31:30 UTC', $date->get(Zend_Date::RFC_850,           'es'));
        $this->assertSame(  'Fri, 13 Feb 09 23:31:30 +0000', $date->get(Zend_Date::RFC_1036,          'es'));
        $this->assertSame('Fri, 13 Feb 2009 23:31:30 +0000', $date->get(Zend_Date::RFC_1123,          'es'));
        $this->assertSame(      '2009-02-13T23:31:30+00:00', $date->get(Zend_Date::RFC_3339,          'es'));
        $this->assertSame('Fri, 13 Feb 2009 23:31:30 +0000', $date->get(Zend_Date::RSS,               'es'));
        $this->assertSame(      '2009-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C,               'es'));

        $date->setTimezone('Indian/Maldives');
        $this->assertSame(                                  '1234567890', $date->get(                            ));
        $this->assertSame(                                          '14', $date->get(Zend_Date::DAY              ));
        $this->assertSame(                                         'Sam', $date->get(Zend_Date::WEEKDAY_SHORT    ));
        $this->assertSame(                                          '14', $date->get(Zend_Date::DAY_SHORT        ));
        $this->assertSame(                                     'Samstag', $date->get(Zend_Date::WEEKDAY          ));
        $this->assertSame(                                           '6', $date->get(Zend_Date::WEEKDAY_8601     ));
        $this->assertSame(                                          'th', $date->get(Zend_Date::DAY_SUFFIX       ));
        $this->assertSame(                                           '6', $date->get(Zend_Date::WEEKDAY_DIGIT    ));
        $this->assertSame(                                          '44', $date->get(Zend_Date::DAY_OF_YEAR      ));
        $this->assertSame(                                           'S', $date->get(Zend_Date::WEEKDAY_NARROW   ));
        $this->assertSame(                                         'Sa.', $date->get(Zend_Date::WEEKDAY_NAME     ));
        $this->assertSame(                                          '07', $date->get(Zend_Date::WEEK             ));
        $this->assertSame(                                     'Februar', $date->get(Zend_Date::MONTH_NAME       ));
        $this->assertSame(                                          '02', $date->get(Zend_Date::MONTH            ));
        $this->assertSame(                                        'Feb.', $date->get(Zend_Date::MONTH_NAME_SHORT ));
        $this->assertSame(                                           '2', $date->get(Zend_Date::MONTH_SHORT      ));
        $this->assertSame(                                          '28', $date->get(Zend_Date::MONTH_DAYS       ));
        $this->assertSame(                                           'F', $date->get(Zend_Date::MONTH_NAME_NARROW));
        $this->assertSame(                                           '0', $date->get(Zend_Date::LEAPYEAR         ));
        $this->assertSame(                                        '2009', $date->get(Zend_Date::YEAR_8601        ));
        $this->assertSame(                                        '2009', $date->get(Zend_Date::YEAR             ));
        $this->assertSame(                                          '09', $date->get(Zend_Date::YEAR_SHORT       ));
        $this->assertSame(                                          '09', $date->get(Zend_Date::YEAR_SHORT_8601  ));
        $this->assertSame(                                       'vorm.', $date->get(Zend_Date::MERIDIEM         ));
        $this->assertSame(                                         '021', $date->get(Zend_Date::SWATCH           ));
        $this->assertSame(                                           '4', $date->get(Zend_Date::HOUR_SHORT_AM    ));
        $this->assertSame(                                           '4', $date->get(Zend_Date::HOUR_SHORT       ));
        $this->assertSame(                                          '04', $date->get(Zend_Date::HOUR_AM          ));
        $this->assertSame(                                          '04', $date->get(Zend_Date::HOUR             ));
        $this->assertSame(                                          '31', $date->get(Zend_Date::MINUTE           ));
        $this->assertSame(                                          '30', $date->get(Zend_Date::SECOND           ));
        $this->assertSame(                                           '0', $date->get(Zend_Date::MILLISECOND      ));
        $this->assertSame(                                          '31', $date->get(Zend_Date::MINUTE_SHORT     ));
        $this->assertSame(                                          '30', $date->get(Zend_Date::SECOND_SHORT     ));
        $this->assertSame(                             'Indian/Maldives', $date->get(Zend_Date::TIMEZONE_NAME    ));
        $this->assertSame(                                           '0', $date->get(Zend_Date::DAYLIGHT         ));
        $this->assertSame(                                       '+0500', $date->get(Zend_Date::GMT_DIFF         ));
        $this->assertSame(                                      '+05:00', $date->get(Zend_Date::GMT_DIFF_SEP     ));
        $this->assertSame(                                         'MVT', $date->get(Zend_Date::TIMEZONE         ));
        $this->assertSame(                                       '18000', $date->get(Zend_Date::TIMEZONE_SECS    ));
        $this->assertSame(                   '2009-02-14T04:31:30+05:00', $date->get(Zend_Date::ISO_8601         ));
        $this->assertSame(             'Sat, 14 Feb 2009 04:31:30 +0500', $date->get(Zend_Date::RFC_2822         ));
        $this->assertSame(                                  '1234567890', $date->get(Zend_Date::TIMESTAMP        ));
        $this->assertSame(                                     'n. Chr.', $date->get(Zend_Date::ERA              ));
        $this->assertSame(                                     'n. Chr.', $date->get(Zend_Date::ERA_NAME         ));
        $this->assertSame(                                  '14.02.2009', $date->get(Zend_Date::DATES            ));
        $this->assertSame(                   'Samstag, 14. Februar 2009', $date->get(Zend_Date::DATE_FULL        ));
        $this->assertSame(                            '14. Februar 2009', $date->get(Zend_Date::DATE_LONG        ));
        $this->assertSame(                                  '14.02.2009', $date->get(Zend_Date::DATE_MEDIUM      ));
        $this->assertSame(                                    '14.02.09', $date->get(Zend_Date::DATE_SHORT       ));
        $this->assertSame(                                    '04:31:30', $date->get(Zend_Date::TIMES            ));
        $this->assertSame(                    '04:31:30 Indian/Maldives', $date->get(Zend_Date::TIME_FULL        ));
        $this->assertSame(                                '04:31:30 MVT', $date->get(Zend_Date::TIME_LONG        ));
        $this->assertSame(                                    '04:31:30', $date->get(Zend_Date::TIME_MEDIUM      ));
        $this->assertSame(                                       '04:31', $date->get(Zend_Date::TIME_SHORT       ));
        $this->assertSame(                         '14.02.2009 04:31:30', $date->get(Zend_Date::DATETIME         ));
        $this->assertSame('Samstag, 14. Februar 2009 04:31:30 Indian/Maldives', $date->get(Zend_Date::DATETIME_FULL    ));
        $this->assertSame(               '14. Februar 2009 04:31:30 MVT', $date->get(Zend_Date::DATETIME_LONG    ));
        $this->assertSame(                         '14.02.2009 04:31:30', $date->get(Zend_Date::DATETIME_MEDIUM  ));
        $this->assertSame(                              '14.02.09 04:31', $date->get(Zend_Date::DATETIME_SHORT   ));
        $this->assertSame(                   '2009-02-14T04:31:30+05:00', $date->get(Zend_Date::ATOM             ));
        $this->assertSame('Saturday, 14-Feb-09 04:31:30 Indian/Maldives', $date->get(Zend_Date::COOKIE           ));
        $this->assertSame(               'Sat, 14 Feb 09 04:31:30 +0500', $date->get(Zend_Date::RFC_822          ));
        $this->assertSame('Saturday, 14-Feb-09 04:31:30 Indian/Maldives', $date->get(Zend_Date::RFC_850          ));
        $this->assertSame(               'Sat, 14 Feb 09 04:31:30 +0500', $date->get(Zend_Date::RFC_1036         ));
        $this->assertSame(             'Sat, 14 Feb 2009 04:31:30 +0500', $date->get(Zend_Date::RFC_1123         ));
        $this->assertSame(                   '2009-02-14T04:31:30+05:00', $date->get(Zend_Date::RFC_3339         ));
        $this->assertSame(             'Sat, 14 Feb 2009 04:31:30 +0500', $date->get(Zend_Date::RSS              ));
        $this->assertSame(                   '2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C              ));

        // when get() receives a format string it responses like toString();
        $date->setTimezone('Indian/Maldives');
        $this->assertSame('2009', $date->get('Y'));
    }

    /**
     * Test for toValue
     */
    public function testGet2()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(-62362925370,null,$locale);
        $this->assertSame('v. Chr.', $date->get(Zend_Date::ERA));
        $this->assertSame('v. Chr.', $date->get(Zend_Date::ERA_NAME));
    }

    /**
     * Test for set
     */
    public function testSet()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);
        $date->setTimezone(date_default_timezone_get());
        $d2->setTimezone(date_default_timezone_get());

        $retour = $date->set(1234567890);
        $this->assertSame('1234567890', $retour->getTimestamp());
        $this->assertSame('1010101010', $date->set($d2)->getTimestamp());
        $this->assertSame('1234567891', $date->set(1234567891)->getTimestamp());

        try {
            $date->set('noday', Zend_Date::DAY);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2, Zend_Date::DAY);
        $this->assertSame('2009-02-04T04:31:31+05:00', $date->get(Zend_Date::W3C));
        $date->set( 10, Zend_Date::DAY);
        $this->assertSame('2009-02-10T04:31:31+05:00', $date->get(Zend_Date::W3C));
        $date->set( 40, Zend_Date::DAY);
        $this->assertSame('2009-03-12T04:31:31+05:00', $date->get(Zend_Date::W3C));
        $date->set(-10, Zend_Date::DAY);
        $this->assertSame('2009-02-18T04:31:31+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('UTC');
        $date->set( 10, Zend_Date::DAY);
        $this->assertSame('2009-02-10T23:31:31+00:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set($d2, Zend_Date::DAY);
        $this->assertSame('2009-02-04T04:31:31+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('UTC');
        $date->set( 10, Zend_Date::DAY, 'en_US');
        $this->assertSame('2009-02-10T23:31:31+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::DAY, 'en_US');
        $this->assertSame('2009-02-04T04:31:31+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set(-20, Zend_Date::DAY, 'en_US');
        $this->assertSame('2009-01-11T04:31:31+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::DAY, 'en_US');
        $this->assertSame('2009-01-04T04:31:31+05:00', $date->get(Zend_Date::W3C));

        $date->set('10.April.2007', 'dd.MMMM.YYYY');
        $this->assertSame('2007-04-10T00:00:00+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for set
     */
    public function testSet2()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $date->setTimezone(date_default_timezone_get());
        $d2->setTimezone(date_default_timezone_get());
        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::WEEKDAY_SHORT);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY_SHORT);
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('Son', Zend_Date::WEEKDAY_SHORT);
        $this->assertSame('2009-02-08T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('Mon', Zend_Date::WEEKDAY_SHORT);
        $this->assertSame('2009-02-09T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('UTC');
        $date->set('Fre', Zend_Date::WEEKDAY_SHORT);
        $this->assertSame('2009-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY_SHORT);
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set('Thu', Zend_Date::WEEKDAY_SHORT, 'en_US');
        $this->assertSame('2009-02-12T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY_SHORT, 'en_US');
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('UTC');
        $date->set('Wed', Zend_Date::WEEKDAY_SHORT , 'en_US');
        $this->assertSame('2009-02-11T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY_SHORT, 'en_US');
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('xxx', Zend_Date::DAY_SHORT);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DAY_SHORT);
        $this->assertSame('2009-02-04T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set( 10, Zend_Date::DAY_SHORT);
        $this->assertSame('2009-02-10T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set( 40, Zend_Date::DAY_SHORT);
        $this->assertSame('2009-03-12T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(-10, Zend_Date::DAY_SHORT);
        $this->assertSame('2009-02-18T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set( 10, Zend_Date::DAY_SHORT);
        $this->assertSame('2009-02-10T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::DAY_SHORT);
        $this->assertSame('2009-02-04T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set( 10, Zend_Date::DAY_SHORT, 'en_US');
        $this->assertSame('2009-02-10T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::DAY_SHORT, 'en_US');
        $this->assertSame('2009-02-04T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-20, Zend_Date::DAY_SHORT, 'en_US');
        $this->assertSame('2009-01-11T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::DAY_SHORT, 'en_US');
        $this->assertSame('2009-01-04T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::WEEKDAY);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY);
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('Sonntag', Zend_Date::WEEKDAY);
        $this->assertSame('2009-02-08T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('Montag', Zend_Date::WEEKDAY);
        $this->assertSame('2009-02-09T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set('Freitag', Zend_Date::WEEKDAY);
        $this->assertSame('2009-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY);
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set('Wednesday', Zend_Date::WEEKDAY, 'en_US');
        $this->assertSame('2009-02-11T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY, 'en_US');
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set('Thursday', Zend_Date::WEEKDAY, 'en_US');
        $this->assertSame('2009-02-12T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY, 'en_US');
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set(0, Zend_Date::WEEKDAY_8601);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        try {
            $date->set('noday', Zend_Date::WEEKDAY_8601);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY_8601);
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1, Zend_Date::WEEKDAY_8601);
        $this->assertSame('2009-02-09T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(5, Zend_Date::WEEKDAY_8601);
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(2, Zend_Date::WEEKDAY_8601);
        $this->assertSame('2009-02-10T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY_8601);
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set(4, Zend_Date::WEEKDAY_8601, 'en_US');
        $this->assertSame('2009-02-12T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY_8601, 'en_US');
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(3, Zend_Date::WEEKDAY_8601, 'en_US');
        $this->assertSame('2009-02-11T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY_8601, 'en_US');
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set($d2, Zend_Date::DAY_SUFFIX);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set(7, Zend_Date::WEEKDAY_DIGIT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        try {
            $date->set('noday', Zend_Date::WEEKDAY_DIGIT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame('2009-02-09T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(5, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(2, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame('2009-02-10T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set(4, Zend_Date::WEEKDAY_DIGIT, 'en_US');
        $this->assertSame('2009-02-12T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY_DIGIT, 'en_US');
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(3, Zend_Date::WEEKDAY_DIGIT, 'en_US');
        $this->assertSame('2009-02-11T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY_DIGIT, 'en_US');
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DAY_OF_YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DAY_OF_YEAR);
        $this->assertSame('2009-01-03T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set( 124, Zend_Date::DAY_OF_YEAR);
        $this->assertSame('2009-05-04T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set( 524, Zend_Date::DAY_OF_YEAR);
        $this->assertSame('2010-06-08T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(-135, Zend_Date::DAY_OF_YEAR);
        $this->assertSame('2009-08-18T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set( 422, Zend_Date::DAY_OF_YEAR);
        $this->assertSame('2010-02-26T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::DAY_OF_YEAR);
        $this->assertSame('2010-01-03T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set( 12, Zend_Date::DAY_OF_YEAR, 'en_US');
        $this->assertSame('2010-01-12T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::DAY_OF_YEAR, 'en_US');
        $this->assertSame('2010-01-03T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-253, Zend_Date::DAY_OF_YEAR, 'en_US');
        $this->assertSame('2009-04-22T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::DAY_OF_YEAR, 'en_US');
        $this->assertSame('2009-01-03T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::WEEKDAY_NARROW);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY_NARROW);
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('S', Zend_Date::WEEKDAY_NARROW);
        $this->assertSame('2009-02-08T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('M', Zend_Date::WEEKDAY_NARROW);
        $this->assertSame('2009-02-09T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set('F', Zend_Date::WEEKDAY_NARROW);
        $this->assertSame('2009-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY_NARROW);
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set('W', Zend_Date::WEEKDAY_NARROW, 'en_US');
        $this->assertSame('2009-02-11T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY_NARROW, 'en_US');
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set('W', Zend_Date::WEEKDAY_NARROW, 'en_US');
        $this->assertSame('2009-02-11T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY_NARROW, 'en_US');
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::WEEKDAY_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEKDAY_NAME);
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('So.', Zend_Date::WEEKDAY_NAME);
        $this->assertSame('2009-02-08T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('Mo.', Zend_Date::WEEKDAY_NAME);
        $this->assertSame('2009-02-09T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set('Fr.', Zend_Date::WEEKDAY_NAME);
        $this->assertSame('2009-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY_NAME);
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set('Thu', Zend_Date::WEEKDAY_NAME, 'en_US');
        $this->assertSame('2009-02-12T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY_NAME, 'en_US');
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set('Wed', Zend_Date::WEEKDAY_NAME, 'en_US');
        $this->assertSame('2009-02-11T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEKDAY_NAME, 'en_US');
        $this->assertSame('2009-02-13T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::WEEK);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::WEEK);
        $this->assertSame('2009-01-03T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set( 1, Zend_Date::WEEK);
        $this->assertSame('2009-01-03T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set( 55, Zend_Date::WEEK);
        $this->assertSame('2010-01-16T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(-57, Zend_Date::WEEK);
        $this->assertSame('2008-11-29T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set( 50, Zend_Date::WEEK);
        $this->assertSame('2008-12-12T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEK);
        $this->assertSame('2008-01-05T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set( 10, Zend_Date::WEEK, 'en_US');
        $this->assertSame('2008-03-08T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEK, 'en_US');
        $this->assertSame('2008-01-05T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-25, Zend_Date::WEEK, 'en_US');
        $this->assertSame('2007-07-06T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::WEEK, 'en_US');
        $this->assertSame('2007-01-06T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MONTH_NAME);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MONTH_NAME);
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('März', Zend_Date::MONTH_NAME);
        $this->assertSame('2009-03-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('Dezember', Zend_Date::MONTH_NAME);
        $this->assertSame('2009-12-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set('August', Zend_Date::MONTH_NAME);
        $this->assertSame('2009-08-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MONTH_NAME);
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set('April', Zend_Date::MONTH_NAME, 'en_US');
        $this->assertSame('2009-04-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MONTH_NAME, 'en_US');
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set('July', Zend_Date::MONTH_NAME, 'en_US');
        $this->assertSame('2009-07-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MONTH_NAME, 'en_US');
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MONTH);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MONTH);
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('03', Zend_Date::MONTH);
        $this->assertSame('2009-03-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set( 14, Zend_Date::MONTH);
        $this->assertSame('2010-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(-6, Zend_Date::MONTH);
        $this->assertSame('2009-06-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set( 10, Zend_Date::MONTH);
        $this->assertSame('2009-10-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MONTH);
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::MONTH, 'en_US');
        $this->assertSame('2009-09-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MONTH, 'en_US');
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-20, Zend_Date::MONTH, 'en_US');
        $this->assertSame('2007-04-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MONTH, 'en_US');
        $this->assertSame('2007-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MONTH_NAME_SHORT);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MONTH_NAME_SHORT);
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('März', Zend_Date::MONTH_NAME_SHORT);
        $this->assertSame('2009-03-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('Dez.', Zend_Date::MONTH_NAME_SHORT);
        $this->assertSame('2009-12-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set('Aug.', Zend_Date::MONTH_NAME_SHORT);
        $this->assertSame('2009-08-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MONTH_NAME_SHORT);
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set('Apr', Zend_Date::MONTH_NAME_SHORT, 'en_US');
        $this->assertSame('2009-04-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MONTH_NAME_SHORT, 'en_US');
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set('Jul', Zend_Date::MONTH_NAME_SHORT, 'en_US');
        $this->assertSame('2009-07-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MONTH_NAME_SHORT, 'en_US');
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MONTH_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MONTH_SHORT);
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(  3, Zend_Date::MONTH_SHORT);
        $this->assertSame('2009-03-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set( 14, Zend_Date::MONTH_SHORT);
        $this->assertSame('2010-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(-6, Zend_Date::MONTH_SHORT);
        $this->assertSame('2009-06-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set( 10, Zend_Date::MONTH_SHORT);
        $this->assertSame('2009-10-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MONTH_SHORT);
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::MONTH_SHORT, 'en_US');
        $this->assertSame('2009-09-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MONTH_SHORT, 'en_US');
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-20, Zend_Date::MONTH_SHORT, 'en_US');
        $this->assertSame('2007-04-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MONTH_SHORT, 'en_US');
        $this->assertSame('2007-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set($d2, Zend_Date::MONTH_DAYS);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('xxday', Zend_Date::MONTH_NAME_NARROW);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MONTH_NAME_NARROW);
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('M', Zend_Date::MONTH_NAME_NARROW);
        $this->assertSame('2009-03-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('D', Zend_Date::MONTH_NAME_NARROW);
        $this->assertSame('2009-12-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set('A', Zend_Date::MONTH_NAME_NARROW);
        $this->assertSame('2009-04-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MONTH_NAME_NARROW);
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set('A', Zend_Date::MONTH_NAME_NARROW, 'en_US');
        $this->assertSame('2009-04-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MONTH_NAME_NARROW, 'en_US');
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set('J', Zend_Date::MONTH_NAME_NARROW, 'en_US');
        $this->assertSame('2009-01-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MONTH_NAME_NARROW, 'en_US');
        $this->assertSame('2009-01-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set($d2, Zend_Date::LEAPYEAR);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::YEAR_8601);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::YEAR_8601);
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1970, Zend_Date::YEAR_8601);
        $this->assertSame('1970-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(2020, Zend_Date::YEAR_8601);
        $this->assertSame('2020-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(2040, Zend_Date::YEAR_8601);
        $this->assertSame('2040-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(1900, Zend_Date::YEAR_8601);
        $this->assertSame('1900-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::YEAR_8601);
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set(2500, Zend_Date::YEAR_8601, 'en_US');
        $this->assertSame('2500-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::YEAR_8601, 'en_US');
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-20, Zend_Date::YEAR_8601, 'en_US');
        $this->assertSame('-20-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::YEAR_8601, 'en_US');
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::YEAR);
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1970, Zend_Date::YEAR);
        $this->assertSame('1970-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(2020, Zend_Date::YEAR);
        $this->assertSame('2020-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(2040, Zend_Date::YEAR);
        $this->assertSame('2040-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(1900, Zend_Date::YEAR);
        $this->assertSame('1900-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::YEAR);
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set(2500, Zend_Date::YEAR, 'en_US');
        $this->assertSame('2500-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::YEAR, 'en_US');
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-20, Zend_Date::YEAR, 'en_US');
        $this->assertSame('-20-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::YEAR, 'en_US');
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::YEAR_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::YEAR_SHORT);
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(70, Zend_Date::YEAR_SHORT);
        $this->assertSame('1970-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(20, Zend_Date::YEAR_SHORT);
        $this->assertSame('2020-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(40, Zend_Date::YEAR_SHORT);
        $this->assertSame('2040-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(0, Zend_Date::YEAR_SHORT);
        $this->assertSame('2000-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::YEAR_SHORT);
        $date->setTimezone('Indian/Maldives');
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(30, Zend_Date::YEAR_SHORT, 'en_US');
        $this->assertSame('2030-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::YEAR_SHORT, 'en_US');
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-20, Zend_Date::YEAR_SHORT, 'en_US');
        $this->assertSame('-20-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::YEAR_SHORT, 'en_US');
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::YEAR_SHORT_8601);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(70, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame('1970-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(20, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame('2020-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(40, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame('2040-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(0, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame('2000-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set(30, Zend_Date::YEAR_SHORT_8601, 'en_US');
        $this->assertSame('2030-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::YEAR_SHORT_8601, 'en_US');
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-20, Zend_Date::YEAR_SHORT_8601, 'en_US');
        $this->assertSame('-20-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::YEAR_SHORT_8601, 'en_US');
        $this->assertSame('2002-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MERIDIEM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::SWATCH);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::SWATCH);
        $this->assertSame('2009-02-14T00:36:00+05:00', $date->get(Zend_Date::W3C));
        $date->set(0, Zend_Date::SWATCH);
        $this->assertSame('2009-02-14T00:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->set(600, Zend_Date::SWATCH);
        $this->assertSame('2009-02-14T14:23:59+05:00', $date->get(Zend_Date::W3C));
        $date->set(1700, Zend_Date::SWATCH);
        $this->assertSame('2009-02-15T16:47:59+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(1900, Zend_Date::SWATCH);
        $this->assertSame('2009-02-16T21:36:00+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::SWATCH);
        $this->assertSame('2009-02-17T00:36:00+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set(3700, Zend_Date::SWATCH, 'en_US');
        $this->assertSame('2009-02-20T16:48:00+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::SWATCH, 'en_US');
        $this->assertSame('2009-02-20T00:36:00+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-200, Zend_Date::SWATCH, 'en_US');
        $this->assertSame('2009-02-18T19:12:00+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::SWATCH, 'en_US');
        $this->assertSame('2009-02-19T00:36:00+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::HOUR_SHORT_AM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(  3, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame('2009-02-14T03:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set( 14, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame('2009-02-14T14:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(-6, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame('2009-02-13T18:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame('2009-02-14T06:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::HOUR_SHORT_AM, 'en_US');
        $this->assertSame('2009-02-14T09:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::HOUR_SHORT_AM, 'en_US');
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-26, Zend_Date::HOUR_SHORT_AM, 'en_US');
        $this->assertSame('2009-02-11T22:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::HOUR_SHORT_AM, 'en_US');
        $this->assertSame('2009-02-12T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::HOUR_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::HOUR_SHORT);
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(  3, Zend_Date::HOUR_SHORT);
        $this->assertSame('2009-02-14T03:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set( 14, Zend_Date::HOUR_SHORT);
        $this->assertSame('2009-02-14T14:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(-6, Zend_Date::HOUR_SHORT);
        $this->assertSame('2009-02-13T18:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::HOUR_SHORT);
        $this->assertSame('2009-02-14T06:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::HOUR_SHORT);
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::HOUR_SHORT, 'en_US');
        $this->assertSame('2009-02-14T09:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::HOUR_SHORT, 'en_US');
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-26, Zend_Date::HOUR_SHORT, 'en_US');
        $this->assertSame('2009-02-11T22:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::HOUR_SHORT, 'en_US');
        $this->assertSame('2009-02-12T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::HOUR_AM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::HOUR_AM);
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(  3, Zend_Date::HOUR_AM);
        $this->assertSame('2009-02-14T03:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set( 14, Zend_Date::HOUR_AM);
        $this->assertSame('2009-02-14T14:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(-6, Zend_Date::HOUR_AM);
        $this->assertSame('2009-02-13T18:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::HOUR_AM);
        $this->assertSame('2009-02-14T06:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::HOUR_AM);
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::HOUR_AM, 'en_US');
        $this->assertSame('2009-02-14T09:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::HOUR_AM, 'en_US');
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-26, Zend_Date::HOUR_AM, 'en_US');
        $this->assertSame('2009-02-11T22:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::HOUR_AM, 'en_US');
        $this->assertSame('2009-02-12T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::HOUR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::HOUR);
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(  3, Zend_Date::HOUR);
        $this->assertSame('2009-02-14T03:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set( 14, Zend_Date::HOUR);
        $this->assertSame('2009-02-14T14:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(-6, Zend_Date::HOUR);
        $this->assertSame('2009-02-13T18:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::HOUR);
        $this->assertSame('2009-02-14T06:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::HOUR);
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::HOUR, 'en_US');
        $this->assertSame('2009-02-14T09:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::HOUR, 'en_US');
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-26, Zend_Date::HOUR, 'en_US');
        $this->assertSame('2009-02-11T22:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::HOUR, 'en_US');
        $this->assertSame('2009-02-12T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MINUTE);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MINUTE);
        $this->assertSame('2009-02-14T04:36:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(  3, Zend_Date::MINUTE);
        $this->assertSame('2009-02-14T04:03:30+05:00', $date->get(Zend_Date::W3C));
        $date->set( 65, Zend_Date::MINUTE);
        $this->assertSame('2009-02-14T05:05:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(-6, Zend_Date::MINUTE);
        $this->assertSame('2009-02-14T04:54:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::MINUTE);
        $this->assertSame('2009-02-13T23:30:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MINUTE);
        $this->assertSame('2009-02-14T04:36:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::MINUTE, 'en_US');
        $this->assertSame('2009-02-14T04:09:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MINUTE, 'en_US');
        $this->assertSame('2009-02-14T04:36:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-65, Zend_Date::MINUTE, 'en_US');
        $this->assertSame('2009-02-13T21:55:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MINUTE, 'en_US');
        $this->assertSame('2009-02-14T02:36:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MINUTE_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MINUTE_SHORT);
        $this->assertSame('2009-02-14T04:36:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(  3, Zend_Date::MINUTE_SHORT);
        $this->assertSame('2009-02-14T04:03:30+05:00', $date->get(Zend_Date::W3C));
        $date->set( 65, Zend_Date::MINUTE_SHORT);
        $this->assertSame('2009-02-14T05:05:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(-6, Zend_Date::MINUTE_SHORT);
        $this->assertSame('2009-02-14T04:54:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::MINUTE_SHORT);
        $this->assertSame('2009-02-13T23:30:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MINUTE_SHORT);
        $this->assertSame('2009-02-14T04:36:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::MINUTE_SHORT, 'en_US');
        $this->assertSame('2009-02-14T04:09:30+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MINUTE_SHORT, 'en_US');
        $this->assertSame('2009-02-14T04:36:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-65, Zend_Date::MINUTE_SHORT, 'en_US');
        $this->assertSame('2009-02-13T21:55:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::MINUTE_SHORT, 'en_US');
        $this->assertSame('2009-02-14T02:36:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::SECOND);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::SECOND);
        $this->assertSame('2009-02-14T04:31:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(  3, Zend_Date::SECOND);
        $this->assertSame('2009-02-14T04:31:03+05:00', $date->get(Zend_Date::W3C));
        $date->set( 65, Zend_Date::SECOND);
        $this->assertSame('2009-02-14T04:32:05+05:00', $date->get(Zend_Date::W3C));
        $date->set(-6, Zend_Date::SECOND);
        $this->assertSame('2009-02-14T04:31:54+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::SECOND);
        $this->assertSame('2009-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::SECOND);
        $this->assertSame('2009-02-14T04:31:50+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::SECOND, 'en_US');
        $this->assertSame('2009-02-14T04:31:09+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::SECOND, 'en_US');
        $this->assertSame('2009-02-14T04:31:50+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-65, Zend_Date::SECOND, 'en_US');
        $this->assertSame('2009-02-13T23:29:55+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::SECOND, 'en_US');
        $this->assertSame('2009-02-14T04:29:50+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::SECOND_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::SECOND_SHORT);
        $this->assertSame('2009-02-14T04:31:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(  3, Zend_Date::SECOND_SHORT);
        $this->assertSame('2009-02-14T04:31:03+05:00', $date->get(Zend_Date::W3C));
        $date->set( 65, Zend_Date::SECOND_SHORT);
        $this->assertSame('2009-02-14T04:32:05+05:00', $date->get(Zend_Date::W3C));
        $date->set(-6, Zend_Date::SECOND_SHORT);
        $this->assertSame('2009-02-14T04:31:54+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set( 30, Zend_Date::SECOND_SHORT);
        $this->assertSame('2009-02-13T23:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::SECOND_SHORT);
        $this->assertSame('2009-02-14T04:31:50+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set( 9, Zend_Date::SECOND_SHORT, 'en_US');
        $this->assertSame('2009-02-14T04:31:09+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::SECOND_SHORT, 'en_US');
        $this->assertSame('2009-02-14T04:31:50+05:00', $date->get(Zend_Date::W3C));
        $date->setTimeZone('UTC');
        $date->set(-65, Zend_Date::SECOND_SHORT, 'en_US');
        $this->assertSame('2009-02-13T23:29:55+00:00', $date->get(Zend_Date::W3C));
        $date->set($d2, Zend_Date::SECOND_SHORT, 'en_US');
        $this->assertSame('2009-02-14T04:29:50+05:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::MILLISECOND);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::MILLISECOND);
        $this->assertSame('000', $date->get(Zend_Date::MILLISECOND));
        $date->set(  3, Zend_Date::MILLISECOND);
        $this->assertSame('003', $date->get(Zend_Date::MILLISECOND));
        $date->set( 1065, Zend_Date::MILLISECOND);
        $this->assertSame('065', $date->get(Zend_Date::MILLISECOND));
        $date->set(-6, Zend_Date::MILLISECOND);
        $this->assertSame('994', $date->get(Zend_Date::MILLISECOND));
        $date->set( 30, Zend_Date::MILLISECOND, true);
        $this->assertSame('030', $date->get(Zend_Date::MILLISECOND));
        $date->set($d2, Zend_Date::MILLISECOND, true);
        $this->assertSame('000', $date->get(Zend_Date::MILLISECOND));
        $date->set( 9, Zend_Date::MILLISECOND, false, 'en_US');
        $this->assertSame('009', $date->get(Zend_Date::MILLISECOND));
        $date->set($d2, Zend_Date::MILLISECOND, false, 'en_US');
        $this->assertSame('000', $date->get(Zend_Date::MILLISECOND));
        $date->set(-65, Zend_Date::MILLISECOND, true , 'en_US');
        $this->assertSame('935', $date->get(Zend_Date::MILLISECOND));
        $date->set($d2, Zend_Date::MILLISECOND, true , 'en_US');
        $this->assertSame('000', $date->get(Zend_Date::MILLISECOND));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIMEZONE_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DAYLIGHT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::GMT_DIFF);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::GMT_DIFF_SEP);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIMEZONE);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIMEZONE_SECS);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::ISO_8601);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::ISO_8601);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('2007-10-20 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('2007-10-20 201030', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('07-10-20 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('80-10-20 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('1980-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('-2007-10-20 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('-2007-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('-07-10-20 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('-7-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('2007-10-20T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('2007-10-20T201030', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('20-10-20T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('2020-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('80-10-20T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('1980-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('-2007-10-20T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('-2007-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('-07-10-20T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('-7-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('20071020 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('201020 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('2020-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('801020 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('1980-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('-071020 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('-7-10-20T20:10:30-07:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('-00071020 20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('-7-10-20T20:10:30+00:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set(1234567890);
        $date->set('20071020T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-20T20:10:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('20071020T10:30', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-20T10:30:00+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('20071020T103000', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-20T10:30:00+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('20071020T1020', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-20T10:20:00+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('-00071020T20:10:30', Zend_Date::ISO_8601);
        $this->assertSame('-7-10-20T20:10:30+00:00', $date->get(Zend_Date::W3C));
        $date->setTimezone('Indian/Maldives');
        $date->set(1234567890);
        $date->set('2007-10-20', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-20T00:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('20071020', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-20T00:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('20071020122030', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-20T12:20:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('071020', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-20T00:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('07:10:20', Zend_Date::ISO_8601);
        $this->assertSame('1970-01-01T07:10:20+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_2822);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_2822);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Thu, 05 Jan 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame('2009-01-05T01:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Thu, 05 Feb 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame('2009-02-05T01:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Thu, 05 Mar 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame('2009-03-05T01:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Thu, 05 Apr 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame('2009-04-05T01:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Thu, 05 May 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame('2009-05-05T01:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Thu, 05 Jun 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame('2009-06-05T01:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Thu, 05 Jul 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame('2009-07-05T01:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Thu, 05 Aug 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame('2009-08-05T01:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Thu, 05 Sep 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame('2009-09-05T01:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Thu, 05 Oct 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame('2009-10-05T01:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Thu, 05 Nov 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame('2009-11-05T01:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Thu, 05 Dec 2009 01:31:30 +0500', Zend_Date::RFC_2822);
        $this->assertSame('2009-12-05T01:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        try {
            $date->set('Thu, 05 Fxx 2009 01:31:30 +0500', Zend_Date::RFC_2822);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIMESTAMP);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIMESTAMP);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('1010101099', Zend_Date::TIMESTAMP);
        $this->assertSame('2002-01-04T04:38:19+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::ERA);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::ERA_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATES);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATES);
        $this->assertSame('2002-01-04T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('14.02.2009', Zend_Date::DATES);
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATE_FULL);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATE_FULL);
        $this->assertSame('2002-01-04T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Samstag, 14. Februar 2009', Zend_Date::DATE_FULL);
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATE_LONG);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATE_LONG);
        $this->assertSame('2002-01-04T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('14. Februar 2009', Zend_Date::DATE_LONG);
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATE_MEDIUM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATE_MEDIUM);
        $this->assertSame('2002-01-04T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('14.02.2009', Zend_Date::DATE_MEDIUM);
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATE_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATE_SHORT);
        $this->assertSame('2002-01-04T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('14.02.09', Zend_Date::DATE_SHORT);
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIMES);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIMES);
        $this->assertSame('2009-02-14T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('15:26:40', Zend_Date::TIMES);
        $this->assertSame('2009-02-14T15:26:40+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIME_FULL);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIME_FULL);
        $this->assertSame('2009-02-14T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('15:26 Uhr CET', Zend_Date::TIME_FULL);
        $this->assertSame('2009-02-14T15:26:00+01:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIME_LONG);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIME_LONG);
        $this->assertSame('2009-02-14T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('15:26:40 CET', Zend_Date::TIME_LONG);
        $this->assertSame('2009-02-14T15:26:40+01:00', $date->get(Zend_Date::W3C));

        $date->setTimezone('Indian/Maldives');
        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIME_MEDIUM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIME_MEDIUM);
        $this->assertSame('2009-02-14T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('15:26:40', Zend_Date::TIME_MEDIUM);
        $this->assertSame('2009-02-14T15:26:40+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::TIME_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::TIME_SHORT);
        $this->assertSame('2009-02-14T04:36:00+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('15:26', Zend_Date::TIME_SHORT);
        $this->assertSame('2009-02-14T15:26:00+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATETIME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATETIME);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('14.02.2009 15:26:03', Zend_Date::DATETIME);
        $this->assertSame('2009-02-14T15:26:03+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATETIME_FULL);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATETIME_FULL);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Samstag, 14. Februar 2009 15:26 Uhr CET', Zend_Date::DATETIME_FULL);
        $this->assertSame('2009-02-14T15:26:00+01:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATETIME_LONG);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATETIME_LONG);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('14. Februar 2009 15:26:03 CET', Zend_Date::DATETIME_LONG);
        $this->assertSame('2009-02-14T15:26:03+01:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATETIME_MEDIUM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATETIME_MEDIUM);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('14.02.2009 15:26:31', Zend_Date::DATETIME_MEDIUM);
        $this->assertSame('2009-02-14T15:26:31+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::DATETIME_SHORT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::DATETIME_SHORT);
        $this->assertSame('2002-01-04T04:36:00+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('14.02.09 15:26', Zend_Date::DATETIME_SHORT);
        $this->assertSame('2009-02-14T15:26:00+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::ATOM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::ATOM);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('2009-02-14T00:31:30+05:00', Zend_Date::ATOM);
        $this->assertSame('2009-02-14T00:31:30+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::COOKIE);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::COOKIE);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Saturday, 14-Feb-09 00:31:30 Europe/Vienna', Zend_Date::COOKIE);
        $this->assertSame('2009-02-14T00:31:30+01:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_822);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_822);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Sat, 14 Feb 09 00:31:30 +0500', Zend_Date::RFC_822);
        $this->assertSame('2009-02-14T00:31:30+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_850);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_850);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Saturday, 14-Feb-09 00:31:30 Europe/Vienna', Zend_Date::RFC_850);
        $this->assertSame('2009-02-14T00:31:30+01:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_1036);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_1036);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Sat, 14 Feb 09 00:31:30 +0500', Zend_Date::RFC_1036);
        $this->assertSame('2009-02-14T00:31:30+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_1123);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_1123);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Sat, 14 Feb 2009 00:31:30 +0500', Zend_Date::RFC_1123);
        $this->assertSame('2009-02-14T00:31:30+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RFC_3339);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RFC_3339);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('2009-02-14T00:31:30+05:00', Zend_Date::RFC_3339);
        $this->assertSame('2009-02-14T00:31:30+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::RSS);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::RSS);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('Sat, 14 Feb 2009 00:31:30 +0500', Zend_Date::RSS);
        $this->assertSame('2009-02-14T00:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('Sat, 14 Feb 2009 00:31:30 GMT', Zend_Date::RSS);
        $this->assertSame('2009-02-14T00:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set('Sat, 14 Feb 09 00:31:30 GMT', Zend_Date::RSS);
        $this->assertSame('2009-02-14T00:31:30+00:00', $date->get(Zend_Date::W3C));
        $date->set('Sat, 14 Feb 09 00:31:30 +0500', Zend_Date::RSS);
        $this->assertSame('2009-02-14T00:31:30+05:00', $date->get(Zend_Date::W3C));

        $date->set(1234567890);
        try {
            $date->set('noday', Zend_Date::W3C);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set($d2, Zend_Date::W3C);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->set(1234567890);
        $date->set('2009-02-14T00:31:30+05:00', Zend_Date::W3C);
        $this->assertSame('2009-02-14T00:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->set('2009-02-14T00:31:30-05:00', Zend_Date::W3C);
        $this->assertSame('2009-02-14T00:31:30-05:00', $date->get(Zend_Date::W3C));

        $date->setTimezone('Indian/Maldives');
        $date->set(1234567890);
        try {
            $date->set('noday', 'xx');
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        try {
            $date->set($d2, 'xx');
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $date->set(1234567890);
        $date->set('1000', 'xx');
        $this->assertSame('1970-01-01T05:16:40+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for add
     */
    public function testAdd()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame($retour->getTimestamp(),'1234567890');
        $this->assertSame($date->add(10)->getTimestamp(),'1234567900');
        $this->assertSame($date->add(-10)->getTimestamp(),'1234567890');
        $this->assertSame($date->add(0)->getTimestamp(),'1234567890');

        $date->set($d2);
        $date->add(10, Zend_Date::DAY);
        $this->assertSame('2002-01-14T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::DAY);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('Mon', Zend_Date::WEEKDAY_SHORT);
        $this->assertSame('2002-01-05T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::DAY_SHORT);
        $this->assertSame('2002-01-14T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::DAY_SHORT);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('Montag', Zend_Date::WEEKDAY);
        $this->assertSame('2002-01-05T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(1, Zend_Date::WEEKDAY_8601);
        $this->assertSame('2002-01-05T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        try {
            $date->add($d2, Zend_Date::DAY_SUFFIX);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }


    /**
     * Test for add
     */
    public function testAdd2()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $date->set($d2);
        $date->add(1, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame('2002-01-05T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::DAY_OF_YEAR);
        $this->assertSame('2002-01-14T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::DAY_OF_YEAR);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('M', Zend_Date::WEEKDAY_NARROW);
        $this->assertSame('2002-01-05T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('Mo.', Zend_Date::WEEKDAY_NAME);
        $this->assertSame('2002-01-05T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::WEEK);
        $this->assertSame('2002-03-15T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::WEEK);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('April', Zend_Date::MONTH_NAME);
        $this->assertSame('2002-08-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::MONTH);
        $this->assertSame('2002-11-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::MONTH);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('Apr.', Zend_Date::MONTH_NAME_SHORT);
        $this->assertSame('2002-08-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::MONTH_SHORT);
        $this->assertSame('2002-11-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::MONTH_SHORT);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        try {
            $date->add($d2, Zend_Date::MONTH_DAYS);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add('M', Zend_Date::MONTH_NAME_NARROW);
        $this->assertSame('2002-06-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        try {
            $date->add($d2, Zend_Date::LEAPYEAR);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add(10, Zend_Date::YEAR_8601);
        $this->assertSame('2012-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::YEAR_8601);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::YEAR);
        $this->assertSame('2012-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::YEAR);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::YEAR_SHORT);
        $this->assertSame('2012-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::YEAR_SHORT);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame('2012-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::MERIDIEM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add(10, Zend_Date::SWATCH);
        $this->assertSame('2002-01-04T04:51:14+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::SWATCH);
        $this->assertSame('2002-01-04T04:36:49+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame('2002-01-04T14:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::HOUR_SHORT);
        $this->assertSame('2002-01-04T14:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::HOUR_SHORT);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::HOUR_AM);
        $this->assertSame('2002-01-04T14:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::HOUR_AM);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::HOUR);
        $this->assertSame('2002-01-04T14:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::HOUR);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::MINUTE);
        $this->assertSame('2002-01-04T04:46:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::MINUTE);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::MINUTE_SHORT);
        $this->assertSame('2002-01-04T04:46:50+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::MINUTE_SHORT);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::SECOND);
        $this->assertSame('2002-01-04T04:37:00+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::SECOND);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::SECOND_SHORT);
        $this->assertSame('2002-01-04T04:37:00+05:00', $date->get(Zend_Date::W3C));
        $date->add(-10, Zend_Date::SECOND_SHORT);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::MILLISECOND);
        $this->assertSame('010', $date->get(Zend_Date::MILLISECOND));
        $date->add(-10, Zend_Date::MILLISECOND);
        $this->assertSame( '000', $date->get(Zend_Date::MILLISECOND));

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::TIMEZONE_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::DAYLIGHT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::GMT_DIFF);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::GMT_DIFF_SEP);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::TIMEZONE);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::TIMEZONE_SECS);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add('1000-01-02 20:05:12', Zend_Date::ISO_8601);
        $this->assertSame('3002-02-07T19:42:02+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('Thu, 02 Jan 1000 20:05:12 +0500', Zend_Date::RFC_2822);
        $this->assertSame('3002-02-07T19:42:02+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add(10, Zend_Date::TIMESTAMP);
        $this->assertSame('2002-01-04T04:37:00+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::ERA);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->add('noday', Zend_Date::ERA_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->add('10.02.0005', Zend_Date::DATES);
        $this->assertSame('2007-03-14T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('Samstag, 10. Februar 0005', Zend_Date::DATE_FULL);
        $this->assertSame('2007-03-14T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('10. Februar 0005', Zend_Date::DATE_LONG);
        $this->assertSame('2007-03-14T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('10.02.0005', Zend_Date::DATE_MEDIUM);
        $this->assertSame('2007-03-14T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('10.02.05', Zend_Date::DATE_SHORT);
        $this->assertSame('4007-03-14T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('10:05:05', Zend_Date::TIMES);
        $this->assertSame('2002-01-04T14:41:55+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('10:05 Uhr CET', Zend_Date::TIME_FULL);
        $this->assertSame('2002-01-04T14:41:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('10:05:05 CET', Zend_Date::TIME_LONG);
        $this->assertSame('2002-01-04T14:41:55+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('10:05:05', Zend_Date::TIME_MEDIUM);
        $this->assertSame('2002-01-04T14:41:55+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('10:05', Zend_Date::TIME_SHORT);
        $this->assertSame('2002-01-04T14:41:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('10.02.0005 10:05:05', Zend_Date::DATETIME);
        $this->assertSame('2007-03-14T14:41:55+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('Samstag, 10. Februar 0005 10:05 Uhr CET', Zend_Date::DATETIME_FULL);
        $this->assertSame('2007-03-14T14:41:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('10. Februar 0005 10:05:05 CET', Zend_Date::DATETIME_LONG);
        $this->assertSame('2007-03-14T14:41:55+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('10.02.0005 10:05:05', Zend_Date::DATETIME_MEDIUM);
        $this->assertSame('2007-03-14T14:41:55+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('10.02.05 10:05', Zend_Date::DATETIME_SHORT);
        $this->assertSame('4007-03-14T14:41:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('1000-01-02T20:05:12+05:00', Zend_Date::ATOM);
        $this->assertSame('3002-02-08T00:42:02+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('Saturday, 02-Jan-00 20:05:12 Europe/Vienna', Zend_Date::COOKIE);
        $this->assertSame('4002-02-07T00:42:02+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('Sat, 02 Jan 00 20:05:12 +0500', Zend_Date::RFC_822);
        $this->assertSame('4002-02-06T19:42:02+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('Saturday, 02-Jan-00 20:05:12 Europe/Vienna', Zend_Date::RFC_850);
        $this->assertSame('4002-02-07T00:42:02+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('Sat, 02 Jan 00 20:05:12 +0500', Zend_Date::RFC_1036);
        $this->assertSame('4002-02-06T19:42:02+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('Sat, 02 Jan 1000 20:05:12 +0500', Zend_Date::RFC_1123);
        $this->assertSame('3002-02-08T00:42:02+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('1000-01-02T20:05:12+05:00', Zend_Date::RFC_3339);
        $this->assertSame('3002-02-08T00:42:02+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('Sat, 02 Jan 1000 20:05:12 +0500', Zend_Date::RSS);
        $this->assertSame('3002-02-08T00:42:02+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('1000-01-02T20:05:12+05:00', Zend_Date::W3C);
        $this->assertSame('3002-02-08T00:42:02+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->add('1000', 'xx');
        $this->assertSame('2002-01-04T04:53:30+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for sub
     */
    public function testSub()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame('1234567890', $retour->getTimestamp());
        $this->assertSame('1234567900', $date->sub(-10)->getTimestamp());
        $this->assertSame('1234567890', $date->sub( 10)->getTimestamp());
        $this->assertSame('1234567890', $date->sub(  0)->getTimestamp());

        $date->set($d2);
        $date->sub(-10, Zend_Date::DAY);
        $this->assertSame('2002-01-14T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::DAY);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for sub
     */
    public function testSub2()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $date->set($d2);
        $date->sub('Mon', Zend_Date::WEEKDAY_SHORT);
        $this->assertSame('2002-01-03T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::DAY_SHORT);
        $this->assertSame('2002-01-14T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::DAY_SHORT);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('Montag', Zend_Date::WEEKDAY);
        $this->assertSame('2002-01-03T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(1, Zend_Date::WEEKDAY_8601);
        $this->assertSame('2002-01-03T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        try {
            $date->sub($d2, Zend_Date::DAY_SUFFIX);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub(1, Zend_Date::WEEKDAY_DIGIT);
        $this->assertSame('2002-01-03T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::DAY_OF_YEAR);
        $this->assertSame('2002-01-14T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::DAY_OF_YEAR);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('M', Zend_Date::WEEKDAY_NARROW);
        $this->assertSame('2002-01-03T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('Mo.', Zend_Date::WEEKDAY_NAME);
        $this->assertSame('2002-01-03T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::WEEK);
        $this->assertSame('2002-03-15T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::WEEK);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('April', Zend_Date::MONTH_NAME);
        $this->assertSame('2001-09-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::MONTH);
        $this->assertSame('2002-11-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::MONTH);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('Apr.', Zend_Date::MONTH_NAME_SHORT);
        $this->assertSame('2001-09-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::MONTH_SHORT);
        $this->assertSame('2002-11-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::MONTH_SHORT);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        try {
            $date->sub($d2, Zend_Date::MONTH_DAYS);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub('M', Zend_Date::MONTH_NAME_NARROW);
        $this->assertSame('2001-10-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        try {
            $date->sub($d2, Zend_Date::LEAPYEAR);
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub(-10, Zend_Date::YEAR_8601);
        $this->assertSame('2012-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::YEAR_8601);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::YEAR);
        $this->assertSame('2012-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::YEAR);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(10, Zend_Date::YEAR_SHORT);
        $this->assertSame('1992-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(-10, Zend_Date::YEAR_SHORT);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(10, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame('1992-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(-10, Zend_Date::YEAR_SHORT_8601);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::MERIDIEM);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub(-10, Zend_Date::SWATCH);
        $this->assertSame('2002-01-04T04:51:15+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::SWATCH);
        $this->assertSame('2002-01-04T04:36:51+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame('2002-01-04T14:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::HOUR_SHORT_AM);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::HOUR_SHORT);
        $this->assertSame('2002-01-04T14:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::HOUR_SHORT);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::HOUR_AM);
        $this->assertSame('2002-01-04T14:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::HOUR_AM);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::HOUR);
        $this->assertSame('2002-01-04T14:36:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::HOUR);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::MINUTE);
        $this->assertSame('2002-01-04T04:46:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::MINUTE);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::MINUTE_SHORT);
        $this->assertSame('2002-01-04T04:46:50+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::MINUTE_SHORT);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::SECOND);
        $this->assertSame('2002-01-04T04:37:00+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::SECOND);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::SECOND_SHORT);
        $this->assertSame('2002-01-04T04:37:00+05:00', $date->get(Zend_Date::W3C));
        $date->sub(10, Zend_Date::SECOND_SHORT);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::MILLISECOND);
        $this->assertSame('010', $date->get(Zend_Date::MILLISECOND));
        $date->sub(10, Zend_Date::MILLISECOND);
        $this->assertSame( '000', $date->get(Zend_Date::MILLISECOND));

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::TIMEZONE_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::DAYLIGHT);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::GMT_DIFF);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::GMT_DIFF_SEP);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::TIMEZONE);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::TIMEZONE_SECS);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub('1000-01-02 20:05:12', Zend_Date::ISO_8601);
        $this->assertSame('1001-11-25T13:31:38+05:00', $date->get(Zend_Date::W3C));
        $date->set($d2);
        $date->sub('1000-01-02T20:05:12+05:00', Zend_Date::ISO_8601);
        $this->assertSame('1001-11-25T13:31:38+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('Thu, 02 Jan 1000 20:05:12 +0500', Zend_Date::RFC_2822);
        $this->assertSame('1001-11-25T13:31:38+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub(-10, Zend_Date::TIMESTAMP);
        $this->assertSame('2002-01-04T04:37:00+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::ERA);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        try {
            $date->sub('noday', Zend_Date::ERA_NAME);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set($d2);
        $date->sub('10.02.0005', Zend_Date::DATES);
        $this->assertSame('1996-10-27T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('Samstag, 10. Februar 0005', Zend_Date::DATE_FULL);
        $this->assertSame('1996-10-27T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('10. Februar 0005', Zend_Date::DATE_LONG);
        $this->assertSame('1996-10-27T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('10.02.0005', Zend_Date::DATE_MEDIUM);
        $this->assertSame('1996-10-27T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('10.02.05', Zend_Date::DATE_SHORT);
        $this->assertSame('-4-10-29T04:36:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('10:05:05', Zend_Date::TIMES);
        $this->assertSame('2002-01-03T18:31:45+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('10:05 Uhr CET', Zend_Date::TIME_FULL);
        $this->assertSame('2002-01-03T18:31:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('10:05:05 CET', Zend_Date::TIME_LONG);
        $this->assertSame('2002-01-03T18:31:45+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('10:05:05', Zend_Date::TIME_MEDIUM);
        $this->assertSame('2002-01-03T18:31:45+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('10:05', Zend_Date::TIME_SHORT);
        $this->assertSame('2002-01-03T18:31:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('10.02.0005 10:05:05', Zend_Date::DATETIME);
        $this->assertSame('1996-10-26T18:31:45+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('Samstag, 10. Februar 0005 10:05 Uhr CET', Zend_Date::DATETIME_FULL);
        $this->assertSame('1996-10-26T18:31:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('10. Februar 0005 10:05:05 CET', Zend_Date::DATETIME_LONG);
        $this->assertSame('1996-10-26T18:31:45+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('10.02.0005 10:05:05', Zend_Date::DATETIME_MEDIUM);
        $this->assertSame('1996-10-26T18:31:45+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('10.02.05 10:05', Zend_Date::DATETIME_SHORT);
        $this->assertSame('-4-10-28T18:31:50+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('1000-01-02T20:05:12+05:00', Zend_Date::ATOM);
        $this->assertSame('1001-11-25T08:31:38+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('Saturday, 02-Jan-00 20:05:12 Europe/Vienna', Zend_Date::COOKIE);
        $this->assertSame('1-12-03T08:31:38+05:00', $date->get(Zend_Date::W3C) );

        $date->set($d2);
        $date->sub('Sat, 02 Jan 00 20:05:12 +0500', Zend_Date::RFC_822);
        $this->assertSame('1-12-03T13:31:38+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('Saturday, 02-Jan-00 20:05:12 Europe/Vienna', Zend_Date::RFC_850);
        $this->assertSame('1-12-03T08:31:38+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('Sat, 02 Jan 00 20:05:12 +0500', Zend_Date::RFC_1036);
        $this->assertSame('1-12-03T13:31:38+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('Sat, 02 Jan 1000 20:05:12 +0500', Zend_Date::RFC_1123);
        $this->assertSame('1001-11-25T08:31:38+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('1000-01-02T20:05:12+05:00', Zend_Date::RFC_3339);
        $this->assertSame('1001-11-25T08:31:38+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('Sat, 02 Jan 1000 20:05:12 +0500', Zend_Date::RSS);
        $this->assertSame('1001-11-25T08:31:38+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('1000-01-02T20:05:12+05:00', Zend_Date::W3C);
        $this->assertSame('1001-11-25T08:31:38+05:00', $date->get(Zend_Date::W3C));

        $date->set($d2);
        $date->sub('1000', 'xx');
        $this->assertSame('2002-01-04T04:20:10+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for compare
     */
    public function testCompare()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);//03.01.2002 15:36:50

        $retour = $date->set(1234567890); //13.02.2009 15:31:30
        $this->assertSame('1234567890', $retour->getTimestamp());
        $this->assertSame( 0, $date->compare(1234567890));
        $this->assertSame( 1, $date->compare(1234567800));
        $this->assertSame(-1, $date->compare(1234567899));

        $date->set($d2);//03.01.2002 15:36:50
        $this->assertSame( 1, $date->compare(3,Zend_Date::DAY));
        $this->assertSame( 0, $date->compare(4,Zend_Date::DAY));
        $this->assertSame(-1, $date->compare(5,Zend_Date::DAY));

        $this->assertSame( 1, $date->compare('Mon',Zend_Date::WEEKDAY_SHORT));
        $this->assertSame(-1, $date->compare('Sam',Zend_Date::WEEKDAY_SHORT));

        $date->set($d2);//03.01.2002 15:36:50
        $this->assertSame(0, $date->compare(0,Zend_Date::MILLISECOND));
    }

    /**
     * Test for copy
     */
    public function testCopy()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $date->set(1234567890);
        $newdate = clone $date;
        $this->assertSame($date->get(),$newdate->get());

        $date->set($d2);
        $newdate = $date->copyPart(Zend_Date::DAY);
        $this->assertSame('2002-01-04T04:36:50+05:00', $date->get(Zend_Date::W3C   ));
        $this->assertSame('1970-01-04T05:00:00+05:00', $newdate->get(Zend_Date::W3C));
    }

    /**
     * Test for equals
     */
    public function testEquals()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame('1234567890', $retour->getTimestamp());
        $this->assertTrue( $date->equals(1234567890));
        $this->assertFalse($date->equals(1234567800));

        $date->set($d2);
        $this->assertFalse($date->equals(3,Zend_Date::DAY));
        $this->assertTrue( $date->equals(4,Zend_Date::DAY));
    }

    /**
     * Test for isEarlier
     */
    public function testIsEarlier()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame('1234567890', $retour->getTimestamp());
        $this->assertFalse($date->isEarlier(1234567890));
        $this->assertFalse($date->isEarlier(1234567800));
        $this->assertTrue( $date->isEarlier(1234567899));

        $date->set($d2);
        $this->assertFalse($date->isEarlier(3,Zend_Date::DAY));
        $this->assertFalse($date->isEarlier(4,Zend_Date::DAY));
        $this->assertTrue( $date->isEarlier(5,Zend_Date::DAY));
    }

    /**
     * Test for isLater
     */
    public function testIsLater()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(0,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $retour = $date->set(1234567890);
        $this->assertSame('1234567890', $retour->getTimestamp());
        $this->assertFalse($date->isLater(1234567890));
        $this->assertTrue( $date->isLater(1234567800));
        $this->assertFalse($date->isLater(1234567899));

        $date->set($d2);
        $this->assertTrue( $date->isLater(3,Zend_Date::DAY));
        $this->assertFalse($date->isLater(4,Zend_Date::DAY));
        $this->assertFalse($date->isLater(5,Zend_Date::DAY));
    }

    /**
     * Test for getTime
     */
    public function testGetTime()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $result = $date->getTime();
        $this->assertSame('1970-01-01T04:36:50+05:00', $result->get(Zend_Date::W3C));
    }

    /**
     * Test for setTime
     */
    public function testSetTime()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->setTime(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);
        $result = $date->setTime('10:20:30');
        $this->assertSame('2009-02-14T10:20:30+05:00', $result->get(Zend_Date::W3C));
        $this->assertSame('2009-02-14T10:20:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTime('30-20-10','ss:mm:HH');
        $this->assertSame('2009-02-14T10:20:30+05:00', $date->get(Zend_Date::W3C));
        $date->setTime($d2);
        $this->assertSame('2009-02-14T04:31:39+05:00', $date->get(Zend_Date::W3C));

        $date = new Zend_Date(Zend_Date::now(), $locale);
        $t1 = $date->get(Zend_Date::TIMESTAMP);
        $date->setTime(Zend_Date::now());
        $t2 = $date->get(Zend_Date::TIMESTAMP);
        $diff = abs($t2 - $t1);
        $this->assertTrue($diff < 2, "Instance of Zend_Date has a significantly different time than returned by setTime(): $diff seconds");
    }

    /**
     * Test for addTime
     */
    public function testAddTime()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->addTime(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,null,$locale);
        $result = $date->addTime('10:20:30');
        $this->assertSame('2009-02-14T14:52:00+05:00', $result->get(Zend_Date::W3C));
        $this->assertSame('2009-02-14T14:52:00+05:00', $date->get(Zend_Date::W3C));

        $date = new Zend_Date(1234567890,null,$locale);
        $date->addTime('30:20:10','ss:mm:HH');
        $this->assertSame('2009-02-14T14:52:00+05:00', $date->get(Zend_Date::W3C));

        $date = new Zend_Date(1234567890,null,$locale);
        $date->addTime($d2);
        $this->assertSame('2009-02-14T09:03:09+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for subTime
     */
    public function testSubTime()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->subTime(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,null,$locale);
        $result = $date->subTime('10:20:30');
        $this->assertSame('2009-02-13T18:11:00+05:00', $result->get(Zend_Date::W3C));
        $this->assertSame('2009-02-13T18:11:00+05:00', $date->get(Zend_Date::W3C));

        $date = new Zend_Date(1234567890,null,$locale);
        $date->subTime('30-20-10','ss:mm:HH');
        $this->assertSame('2009-02-13T18:11:00+05:00', $date->get(Zend_Date::W3C));

        $date = new Zend_Date(1234567890,null,$locale);
        $date->subTime($d2);
        $this->assertSame('2009-02-13T23:59:51+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for compareTime
     */
    public function testCompareTime()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $date = new Zend_Date(1234567890,null,$locale);
        $this->assertSame(-1, $date->compareTime('10:20:30'));
        $this->assertSame( 0, $date->compareTime('04:31:30'));
        $this->assertSame( 1, $date->compareTime('04:00:30'));
        $this->assertSame(-1, $date->compareTime($d2       ));
    }

    /**
     * Test for setTime
     */
    public function testSetHour()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1234567890,null,$locale);
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
        for($i=23; $i >= 0; $i--) {
            $date->setHour($i);
            $hour = $i;
            if ($i < 10) {
                $hour = '0' . $hour;
            }
            $this->assertSame("2009-02-14T$hour:31:30+05:00", $date->get(Zend_Date::W3C));
        }
    }

    /**
     * Test for getDate
     */
    public function testGetDate()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $result = $date->getDate();
        $this->assertSame('2002-01-04T00:00:00+05:00', $result->get(Zend_Date::W3C));
    }

    /**
     * Test for setDate
     */
    public function testSetDate()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->setDate(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);
        $result = $date->setDate('11.05.2008');
        // Hint: the hour changes from 0 to 1 because of DST...
        // An hour is added by winter->summertime change
        $this->assertSame('2008-05-11T04:31:30+05:00', $result->get(Zend_Date::W3C));
        $this->assertSame('2008-05-11T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setDate('2008-05-11','YYYY-MM-dd');
        $this->assertSame('2008-05-11T04:31:30+05:00', $date->get(Zend_Date::W3C));
        $date->setDate($d2);
        $this->assertSame('2009-02-14T04:31:30+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for addDate
     */
    public function testAddDate()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->addDate(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,null,$locale);
        $result = $date->addDate('02-03-05');
        $this->assertSame('4014-05-17T04:31:30+05:00', $result->get(Zend_Date::W3C));
        $this->assertSame('4014-05-17T04:31:30+05:00', $date->get(Zend_Date::W3C));

        $date = new Zend_Date(1234567890,null,$locale);
        $date->addDate('05-03-02','YY-MM-dd');
        $this->assertSame('4014-05-17T04:31:30+05:00', $date->get(Zend_Date::W3C));

        $date = new Zend_Date(1234567890,null,$locale);
        $date->addDate($d2);
        $this->assertSame('4018-04-28T04:31:30+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for subDate
     */
    public function testSubDate()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->subDate(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,null,$locale);
        $result = $date->subDate('03-05-1001');
        $this->assertSame('1007-09-08T04:31:30+05:00', $result->get(Zend_Date::W3C));
        $this->assertSame('1007-09-08T04:31:30+05:00', $date->get(Zend_Date::W3C));

        $date = new Zend_Date(1234567890,null,$locale);
        $date->subDate('1001-05-03','YYYY-MM-dd');
        $this->assertSame('1007-09-08T04:31:30+05:00', $date->get(Zend_Date::W3C));

        $date = new Zend_Date(1234567890,null,$locale);
        $date->subDate($d2);
        $this->assertSame('-1-12-06T04:31:30+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for compareDate
     */
    public function testCompareDate()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $date = new Zend_Date(1234567890,$locale);
        $this->assertSame( 1, $date->compareDate('10.01.2009'));
        $this->assertSame( 0, $date->compareDate('14.02.2009'));
        $this->assertSame(-1, $date->compareDate('15.02.2009'));
        $this->assertSame( 0, $date->compareDate($d2         ));
    }

    /**
     * Test for getIso
     */
    public function testGetIso()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,null,$locale);
        $d2   = new Zend_Date(1010101010,null,$locale);

        $result = $date->getIso();
        $this->assertTrue(is_string($result));
        $this->assertSame('2002-01-04T04:36:50+05:00', $result);
    }

    /**
     * Test for setIso
     */
    public function testSetIso()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->setIso(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);
        $result = $date->setIso('2002-01-04T00:00:00+0000');
        $this->assertSame('2002-01-04T00:00:00+00:00', $result->get(Zend_Date::W3C));
        $this->assertSame('2002-01-04T00:00:00+00:00', $date->get(Zend_Date::W3C));
        $date->setIso($d2);
        $this->assertSame('2009-02-14T04:31:39+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for addIso
     */
    public function testAddIso()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $result = $date->addIso(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);
    }

    /**
     * Test for addIso
     */
    public function testAddIso2()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $result = $date->setIso('2002-01-04T01:00:00+0500');
        $result = $date->addIso('0000-00-00T01:00:00+0500');
        $this->assertSame('2002-01-03T21:00:00+05:00', $result->get(Zend_Date::W3C));
        $this->assertSame('2002-01-03T21:00:00+05:00', $date->get(Zend_Date::W3C));

        $date->addIso('0001-01-01T01:01:01+0500');
        $this->assertSame('2003-02-04T17:01:01+05:00', $date->get(Zend_Date::W3C));

        $date = new Zend_Date(1234567890,$locale);
        $date->addIso($d2);
        $this->assertSame('4018-04-28T04:03:09+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for subIso
     */
    public function testSubIso()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $result = $date->subIso(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);
    }

    /**
     * Test for subIso
     */
    public function testSubIso2()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);
        $result = $date->subIso('0000-00-00T01:00:00+0500');
        $this->assertSame('2009-02-14T08:31:30+05:00', $result->get(Zend_Date::W3C));
        $this->assertSame('2009-02-14T08:31:30+05:00', $date->get(Zend_Date::W3C));

        $result = $date->subIso('0001-01-01T01:01:01+0500');
        $this->assertSame('2008-01-14T12:30:29+05:00', $date->get(Zend_Date::W3C));

        $date = new Zend_Date(1234567890,null,$locale);
        $date->subIso($d2);
        $this->assertSame('-1-12-06T04:59:51+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for compareIso
     */
    public function testCompareIso()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,null,$locale);
        $d2   = new Zend_Date(1234567899,null,$locale);

        $date = new Zend_Date(1234567890,null,$locale);
        $this->assertSame( 1, $date->compareIso('2002-01-04T04:00:00+0500'));
        $this->assertSame( 0, $date->compareIso('2009-02-14T04:31:30+0500'));
        $this->assertSame(-1, $date->compareIso('2010-01-04T05:00:00+0500'));
        $this->assertSame(-1, $date->compareIso($d2                       ));
    }

    /**
     * Test for getArpa
     */
    public function testGetArpa()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,null,$locale);

        $result = $date->getArpa();
        $this->assertTrue(is_string($result));
        $this->assertSame('Fri, 04 Jan 02 04:36:50 +0500', $result);
    }

    /**
     * Test for setArpa
     */
    public function testSetArpa()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);
        $date->setTimezone('Indian/Maldives');

        $result = $date->setArpa(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);
        $result = $date->setArpa('Sat, 03 May 01 00:00:00 +0500');
        $this->assertSame('Thu, 03 May 01 00:00:00 +0500', $result->get(Zend_Date::RFC_822));
        $this->assertSame('2001-05-03T00:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->setArpa($d2);
        $this->assertSame('2009-02-14T04:31:39+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for addArpa
     */
    public function testAddArpa()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $result = $date->addArpa(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,$locale);
        $result = $date->addArpa('Sat, 03 May 01 00:00:00 +0500');
        $this->assertSame('Sat, 17 Jul 10 23:31:30 +0500', $result->get(Zend_Date::RFC_822));
        $this->assertSame('4010-07-17T23:31:30+05:00', $date->get(Zend_Date::W3C));

        $date = new Zend_Date(1234567890,$locale);
        $date->addArpa($d2);
        $this->assertSame('4018-04-28T04:03:09+05:00', $date->get(Zend_Date::W3C));

        $result = $date->setArpa('Fri, 05 Jan 07 03:35:53 +0500');
        $arpa = $result->getArpa();
        $this->assertSame('Fri, 05 Jan 07 03:35:53 +0500', $arpa);
    }

    /**
     * Test for subArpa
     */
    public function testSubArpa()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $result = $date->subArpa(Zend_Date::now());
        $this->assertTrue($result instanceof Zend_Date);

        $date = new Zend_Date(1234567890,null,$locale);
        $result = $date->subArpa('Sat, 03 May 01 00:00:00 +0500');
        $this->assertSame('Wed, 16 Sep 7 09:31:30 +0500', $result->get(Zend_Date::RFC_822));
        $this->assertSame('7-09-16T09:31:30+05:00', $date->get(Zend_Date::W3C));

        $date = new Zend_Date(1234567890,$locale);
        $date->subArpa($d2);
        $this->assertSame('-1-12-06T04:59:51+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for compareArpa
     */
    public function testCompareArpa()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $date = new Zend_Date(1234567890,$locale);
        $this->assertSame(-1, $date->compareArpa('Sat, 14 Feb 09 05:31:30 +0500'));
        $this->assertSame( 0, $date->compareArpa('Sat, 14 Feb 09 04:31:30 +0500'));
        $this->assertSame( 1, $date->compareArpa('Sat, 13 Feb 09 04:31:30 +0500'));
        $this->assertSame(-1, $date->compareArpa($d2                            ));
    }

    /**
     * Test for false locale setting
     */
    public function testReducedParams()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,$locale);

        $date->setArpa('Sat, 03 May 01 00:00:00 +0500',$locale);
        $this->assertSame('Thu, 03 May 01 00:00:00 +0500', $date->get(Zend_Date::RFC_822));
    }

    /**
     * Test for SunFunc
     */
    public function testSunFunc()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,$locale);
        $date->setTimezone(date_default_timezone_get());

        $result = Zend_Date_Cities::City('vienna');
        $this->assertTrue(is_array($result));
        $result = $date->getSunset($result);
        $this->assertSame('2002-01-04T20:09:59+05:00', $result->get(Zend_Date::W3C));

        unset($result);
        $result = Zend_Date_Cities::City('vienna', 'civil');
        $this->assertTrue(is_array($result));
        $result = $date->getSunset($result);
        $this->assertSame('2002-01-04T20:09:20+05:00', $result->get(Zend_Date::W3C));

        unset($result);
        $result = Zend_Date_Cities::City('vienna', 'nautic');
        $this->assertTrue(is_array($result));
        $result = $date->getSunset($result);
        $this->assertSame('2002-01-04T20:08:34+05:00', $result->get(Zend_Date::W3C));

        unset($result);
        $result = Zend_Date_Cities::City('vienna', 'astronomic');
        $this->assertTrue(is_array($result));
        $result = $date->getSunset($result);
        $this->assertSame('2002-01-04T20:07:49+05:00', $result->get(Zend_Date::W3C));

        unset($result);
        $result = Zend_Date_Cities::City('BERLIN');
        $this->assertTrue(is_array($result));
        $result = $date->getSunrise($result);
        $this->assertSame('2002-01-04T12:21:21+05:00', $result->get(Zend_Date::W3C));

        unset($result);
        $result = Zend_Date_Cities::City('London');
        $this->assertTrue(is_array($result));
        $result = $date->getSunInfo($result);
        $this->assertSame('2002-01-04T13:10:10+05:00', $result['sunrise']['effective']->get(Zend_Date::W3C ));
        $this->assertSame('2002-01-04T13:10:54+05:00', $result['sunrise']['civil']->get(Zend_Date::W3C     ));
        $this->assertSame('2002-01-04T13:11:45+05:00', $result['sunrise']['nautic']->get(Zend_Date::W3C    ));
        $this->assertSame('2002-01-04T13:12:35+05:00', $result['sunrise']['astronomic']->get(Zend_Date::W3C));
        $this->assertSame('2002-01-04T21:00:52+05:00', $result['sunset']['effective']->get(Zend_Date::W3C  ));
        $this->assertSame('2002-01-04T21:00:08+05:00', $result['sunset']['civil']->get(Zend_Date::W3C      ));
        $this->assertSame('2002-01-04T20:59:18+05:00', $result['sunset']['nautic']->get(Zend_Date::W3C     ));
        $this->assertSame('2002-01-04T20:58:28+05:00', $result['sunset']['astronomic']->get(Zend_Date::W3C ));

        unset($result);
        $result = array('longitude' => 0);
        try {
            $result = $date->getSunrise($result);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        unset($result);
        $result = array('latitude' => 0);
        try {
            $result = $date->getSunrise($result);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        unset($result);
        $result = array('longitude' => 180.1, 'latitude' => 0);
        try {
            $result = $date->getSunrise($result);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        unset($result);
        $result = array('longitude' => -180.1, 'latitude' => 0);
        try {
            $result = $date->getSunrise($result);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        unset($result);
        $result = array('longitude' => 0, 'latitude' => 90.1);
        try {
            $result = $date->getSunrise($result);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        unset($result);
        $result = array('longitude' => 0, 'latitude' => -90.1);
        try {
            $result = $date->getSunrise($result);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        unset($result);
        $result = array('latitude' => 0, 'longitude' => 0);
        $result = $date->getSunInfo($result);
        $this->assertTrue(is_array($result));

        unset($result);
        $result = array('latitude' => 0, 'longitude' => 0);
        $result = $date->getSunrise($result);
        $this->assertTrue($result instanceof Zend_Date);
    }

    /**
     * Test for Timezone
     */
    public function testTimezone()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1010101010,$locale);
        $date->setTimezone(date_default_timezone_get());

        $result = $date->getTimezone();
        $this->assertSame('Indian/Maldives', $result);

        try {
            $result = $date->setTimezone('unknown');
            // if function timezone_identifiers_list is not available false should be returned
            $this->assertFalse($result);
        } catch (Zend_Date_Exception $e) {
            // success
        }
        $result = $date->getTimezone();
        $this->assertSame('Indian/Maldives', $result);

        $result = $date->setTimezone('America/Chicago');
        $this->assertTrue($result instanceof Zend_Date);
        $result = $date->getTimezone();
        $this->assertSame('America/Chicago', $result);

        $date = new Zend_Date('01.01.2000T00:00:00Z',Zend_Date::ISO_8601);
        $result = $date->getTimezone();
        $this->assertSame('Etc/UTC', $result);
    }

    /**
     * Test for LeapYear
     */
    public function testLeapYear()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date('01.01.2006', Zend_Date::DATES, $locale);
        $this->assertFalse($date->isLeapYear());

        unset($date);
        $date = new Zend_Date('01.01.2004', Zend_Date::DATES, $locale);
        $this->assertTrue($date->isLeapYear());

        try {
            $result = Zend_Date::checkLeapYear('noyear');
            $this->fail('exception expected');
        } catch (Zend_Date_Exception $e) {
            // succeed
        }
    }

    /**
     * Test for Today
     */
    public function testToday()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(Zend_Date::now());
        $d2 = new Zend_Date(1010101010,$locale);

        $this->assertFalse($d2->isToday());
        $this->assertTrue($date->isToday());
    }

    /**
     * Test for Yesterday
     */
    public function testYesterday()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(Zend_Date::now());
        $d2 = new Zend_Date(1010101010,$locale);
        $date->subDay(1);
        $this->assertFalse($d2->isYesterday());
        $this->assertTrue($date->isYesterday());
    }

    /**
     * Test for Tomorrow
     */
    public function testTomorrow()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(Zend_Date::now());
        $d2 = new Zend_Date(1010101010,$locale);

        $date->addDay(1);
        $this->assertFalse($d2->isTomorrow());
        $this->assertTrue($date->isTomorrow());
    }

    /**
     * test isToday(), isTomorrow(), and isYesterday() for cases other than time() = "now"
     */
    public function testIsDay()
    {
        date_default_timezone_set('Europe/Vienna'); // should have DST
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date_TestHelper('01.01.2006', Zend_Date::DATES, $locale);

        $date->_setTime($date->mktime(0, 0, 0, 1, 1, 2006));
        $this->assertTrue($date->isToday());
        $this->assertFalse($date->isTomorrow());
        $date->_setTime($date->mktime(0, 0, 0, 1, 1, 2006));
        $this->assertFalse($date->isYesterday());

        $date->_setTime($date->mktime(0, 0, 0, 12, 31, 2005));
        $this->assertTrue($date->isTomorrow());
        $date->_setTime($date->mktime(0, 0, 0, 12, 31, 2005));
        $this->assertFalse($date->isYesterday());

        $date->_setTime($date->mktime(0, 0, 0, 12, 31, 2006));
        $this->assertFalse($date->isTomorrow());
        $date->_setTime($date->mktime(0, 0, 0, 12, 31, 2006));
        $this->assertFalse($date->isYesterday());

        $date->_setTime($date->mktime(0, 0, 0, 1, 0, 2006));
        $this->assertTrue($date->isTomorrow());
        $date->_setTime($date->mktime(0, 0, 0, 1, 0, 2006));
        $this->assertFalse($date->isYesterday());

        $date->_setTime($date->mktime(0, 0, 0, 1, 2, 2006));
        $this->assertFalse($date->isTomorrow());
        $date->_setTime($date->mktime(0, 0, 0, 1, 2, 2006));
        $this->assertTrue($date->isYesterday());
    }

    /**
     * Test for Now
     */
    public function testNow()
    {
        $locale = new Zend_Locale('de_AT');

        $date = Zend_Date::now();

        $reference = date('U');
        $this->assertTrue(($reference - $date->get(Zend_Date::TIMESTAMP)) < 2);
    }

    /**
     * Test for getYear
     */
    public function testGetYear()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1234567890,$locale);
        $d2 = new Zend_Date(1610101010,$locale);
        $date->setTimeZone(date_default_timezone_get());
        $d2->setTimeZone(date_default_timezone_get());

        $result = $date->getYear();
        $this->assertTrue($result instanceof Zend_Date);
        $this->assertSame('01.01.2009 05:00:00', $result->toString());
        $this->assertSame('01.01.2021 05:00:00', $d2->getYear()->toString());
    }

    /**
     * Test for setYear
     */
    public function testSetYear()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1577833200,$locale);
        $date2 = new Zend_Date(2006, Zend_Date::YEAR);
        $date->setTimeZone(date_default_timezone_get());

        $date->setYear(2000);
        $this->assertSame('2000-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));

        $date->setYear(1800);
        $this->assertSame('1800-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));

        $date->setYear(2100);
        $this->assertSame('2100-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));

        $date->setYear($date2);
        $this->assertSame('2006-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));

        try {
            $date->setYear('noyear');
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    /**
     * Test for addYear
     */
    public function testAddYear()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1577833200,$locale);
        $date->setTimeZone(date_default_timezone_get());

        $date->addYear(1);
        $this->assertSame('2021-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->addYear(1);
        $this->assertSame('2022-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->addYear(1);
        $this->assertSame('2023-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->addYear(1);
        $this->assertSame('2024-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->addYear(1);
        $this->assertSame('2025-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));

        $date->setYear(1500);
        $this->assertSame('1500-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->addYear(20);
        $this->assertSame('1520-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));

        $date->setYear(2100);
        $this->assertSame('2100-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->addYear(20);
        $this->assertSame('2120-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));

        $date->setDay(4);
        $date->setMonth(4);
        $date->setYear(2020);
        $this->assertSame('2020-04-04T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->addYear(1);
        $this->assertSame('2021-04-04T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->addYear(1);
        $this->assertSame('2022-04-04T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->addYear(1);
        $this->assertSame('2023-04-04T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->addYear(1);
        $this->assertSame('2024-04-04T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->addYear(1);
        $this->assertSame('2025-04-04T04:00:00+05:00', $date->get(Zend_Date::W3C));

        $date->setYear(1500);
        $this->assertSame('1500-04-04T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->addYear(20);
        $this->assertSame('1520-04-04T04:00:00+05:00', $date->get(Zend_Date::W3C));

        $date->setYear(2100);
        $this->assertSame('2100-04-04T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->addYear(20);
        $this->assertSame('2120-04-04T04:00:00+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for subYear
     */
    public function testSubYear()
    {
        if (!defined('TESTS_ZEND_I18N_EXTENDED_COVERAGE') || TESTS_ZEND_I18N_EXTENDED_COVERAGE == false) {
            $this->markTestSkipped('Extended I18N test skipped');
            return;
        }

        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1577833200,$locale);
        $date->setTimeZone(date_default_timezone_get());

        $date->subYear(1);
        $this->assertSame('2019-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->subYear(1);
        $this->assertSame('2018-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->subYear(1);
        $this->assertSame('2017-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->subYear(1);
        $this->assertSame('2016-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->subYear(1);
        $this->assertSame('2015-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));

        $date->setYear(1500);
        $this->assertSame('1500-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->subYear(20);
        $this->assertSame('1480-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));

        $date->setYear(2100);
        $this->assertSame('2100-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->subYear(20);
        $this->assertSame('2080-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for compareYear
     */
    public function testCompareYear()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $date = new Zend_Date(1234567890,$locale);
        $this->assertSame(-1, $date->compareYear(2010));
        $this->assertSame( 0, $date->compareYear(2009));
        $this->assertSame( 1, $date->compareYear(2008));
        $this->assertSame( 0, $date->compareYear($d2 ));
    }

    /**
     * Test for getMonth
     */
    public function testGetMonth()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1234567890,$locale);
        $d2 = new Zend_Date(1610101010,$locale);
        $date->setTimeZone(date_default_timezone_get());
        $d2->setTimeZone(date_default_timezone_get());

        $result = $date->getMonth();
        $this->assertTrue($result instanceof Zend_Date);
        $this->assertSame('01.02.1970 05:00:00', $result->toString(          ));
        $this->assertSame('01.02.1970 05:00:00', $date->getMonth()->toString());
    }

    /**
     * Test for setMonth
     */
    public function testSetMonth()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1577833200,$locale);
        $date2 = new Zend_Date(2006, Zend_Date::YEAR);
        $date->setTimeZone(date_default_timezone_get());

        $date->setMonth(3);
        $this->assertSame('2020-03-01T04:00:00+05:00', $date->get(Zend_Date::W3C));

        $date->setMonth(-3);
        $this->assertSame('2019-09-01T04:00:00+05:00', $date->get(Zend_Date::W3C));

        $date->setMonth('March', 'en');
        $this->assertSame('2019-03-01T04:00:00+05:00', $date->get(Zend_Date::W3C));

        $date->setMonth($date2);
        $this->assertSame('2019-01-01T04:00:00+05:00', $date->get(Zend_Date::W3C));

        try {
            $date->setMonth('nomonth');
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    /**
     * Test for addMonth
     */
    public function testAddMonth()
    {
        date_default_timezone_set('Europe/Vienna');
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1577833200,$locale);
        $date->setTimeZone(date_default_timezone_get());

        $date->addMonth(1);
        $this->assertSame('2020-02-01T00:00:00+01:00', $date->get(Zend_Date::W3C));
        $date->addMonth(1);
        $this->assertSame('2020-03-01T00:00:00+01:00', $date->get(Zend_Date::W3C));
        $date->addMonth(1);
        $this->assertSame('2020-04-01T00:00:00+02:00', $date->get(Zend_Date::W3C));
        $date->addMonth(1);
        $this->assertSame('2020-05-01T00:00:00+02:00', $date->get(Zend_Date::W3C));
        $date->addMonth(1);
        $this->assertSame('2020-06-01T00:00:00+02:00', $date->get(Zend_Date::W3C));
        $date->addMonth(5);
        $this->assertSame('2020-11-01T00:00:00+01:00', $date->get(Zend_Date::W3C));

        Zend_Date::setOptions(array('fix_dst' => true));
        $date = new Zend_Date('2007-10-01 00:00:00', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-01 00:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));
        $date->addMonth(1);
        $this->assertSame('2007-11-01 00:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));

        $date = new Zend_Date('2007-10-01 23:00:00', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-01 23:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));
        $date->addMonth(1);
        $this->assertSame('2007-11-01 23:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));

        $date = new Zend_Date('2007-03-01 00:00:00', Zend_Date::ISO_8601);
        $this->assertSame('2007-03-01 00:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));
        $date->addMonth(1);
        $this->assertSame('2007-04-01 00:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));

        $date = new Zend_Date('2007-03-01 23:00:00', Zend_Date::ISO_8601);
        $this->assertSame('2007-03-01 23:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));
        $date->addMonth(1);
        $this->assertSame('2007-04-01 23:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));

        Zend_Date::setOptions(array('fix_dst' => false));
        $date = new Zend_Date('2007-10-01 00:00:00', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-01 00:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));
        $date->addMonth(1);
        $this->assertSame('2007-10-31 23:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));

        $date = new Zend_Date('2007-10-01 23:00:00', Zend_Date::ISO_8601);
        $this->assertSame('2007-10-01 23:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));
        $date->addMonth(1);
        $this->assertSame('2007-11-01 22:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));

        $date = new Zend_Date('2007-03-01 00:00:00', Zend_Date::ISO_8601);
        $this->assertSame('2007-03-01 00:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));
        $date->addMonth(1);
        $this->assertSame('2007-04-01 01:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));

        $date = new Zend_Date('2007-03-01 23:00:00', Zend_Date::ISO_8601);
        $this->assertSame('2007-03-01 23:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));
        $date->addMonth(1);
        $this->assertSame('2007-04-02 00:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));

        $date = new Zend_Date('2007-01-31 00:00:00', Zend_Date::ISO_8601);
        $this->assertSame('2007-01-31 00:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));
        $date->addMonth(1);
        $this->assertSame('2007-02-28 00:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));

        $date = new Zend_Date('2007-01-31 00:00:00', Zend_Date::ISO_8601);
        $this->assertSame('2007-01-31 00:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));
        Zend_Date::setOptions(array('extend_month' => true));
        $date->addMonth(1);
        $this->assertSame('2007-03-03 00:00:00', $date->toString('yyyy-MM-dd HH:mm:ss'));

        date_default_timezone_set('America/Chicago');
        $date = new Zend_Date(1577858400,$locale);
        $date->setTimeZone(date_default_timezone_get());
        $this->assertSame('2020-01-01T00:00:00-06:00', $date->get(Zend_Date::ISO_8601));
        $date->addMonth(12);
        $this->assertSame('2021-01-01T00:00:00-06:00', $date->get(Zend_Date::ISO_8601));
    }

    /**
     * Test for subMonth
     */
    public function testSubMonth()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1577833200,$locale);
        $date->setTimeZone(date_default_timezone_get());

        $date->subMonth(1);
        $this->assertSame('2019-12-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->subMonth(12);
        $this->assertSame('2018-12-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * Test for compareMonth
     */
    public function testCompareMonth()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1234567890,$locale);
        $d2   = new Zend_Date(1234567899,$locale);

        $date = new Zend_Date(1234567890,$locale);
        $this->assertSame( 1, $date->compareMonth( 1));
        $this->assertSame( 0, $date->compareMonth( 2));
        $this->assertSame(-1, $date->compareMonth( 3));
        $this->assertSame( 0, $date->compareYear($d2));
    }

    /**
     * Test accessors for _Locale member property of Zend_Date
     */
    public function testLocale()
    {
        $date = new Zend_Date(Zend_Date::now());
        $locale = new Zend_Locale('en_US');
        $date->setLocale($locale);
        $this->assertSame('en_US', $date->getLocale());
    }

    /**
     * test for getWeek
     */
    public function testGetWeek()
    {
        $locale = new Zend_Locale('de_AT');
        $date = new Zend_Date(1168293600, $locale);

        //Tuesday
        $date->addDay(1);
        $this->assertSame('08.01.1970 05:00:00', $date->getWeek()->toString());

        //Wednesday
        $date->addDay(1);
        $this->assertSame('08.01.1970 05:00:00', $date->getWeek()->toString());

        //Thursday
        $date->addDay(1);
        $this->assertSame('08.01.1970 05:00:00', $date->getWeek()->toString());

        //Friday
        $date->addDay(1);
        $this->assertSame('08.01.1970 05:00:00', $date->getWeek()->toString());

        //Friday 05:30 am
        $date->addTime('05:30:00');
        $this->assertSame('08.01.1970 05:00:00', $date->getWeek()->toString());

        //Saturday
        $date->addDay(1);
        $this->assertSame('08.01.1970 05:00:00', $date->getWeek()->toString());

        //Saturday [ar_EG]
        // The right value for AM/PM has to be set in arabic letters
        $this->assertSame('08‏/01‏/1970 5:00:00 ص', $date->getWeek('ar_EG')->toString());
        $date->setTimeZone('UTC');
        $this->assertSame('08‏/01‏/1970 12:00:00 ص', $date->getWeek('ar_EG')->toString());
        $date->setTimeZone('Indian/Maldives');
        $this->assertSame('08‏/01‏/1970 5:00:00 ص', $date->getWeek('ar_EG')->toString());

        //Sunday [start of a new week as defined per ISO 8601]
        $date->addDay(1);
        $this->assertSame('15.01.1970 05:00:00', $date->getWeek()->toString());

        //Monday
        $date->addDay(1);
        $this->assertSame('15.01.1970 05:00:00', $date->getWeek()->toString());

        //Monday 03:45 pm
        $date->addTime('15:45:00');
        $this->assertSame('15.01.1970 05:00:00', $date->getWeek()->toString());
    }

    /**
     * test setting dates to specify weekdays
     */
    public function testDay()
    {
        // all tests and calculations below are in GMT (that is intention for this test)
        $date = new Zend_Date(0, 'de_AT');
        $date->setTimeZone('UTC');
        $dw = $date->getDay();
        $this->assertSame('01.01.1970 00:00:00', $dw->toString());
        for($day = 1; $day < 31; $day++) {
            $date->setDay($day);
            $dw = $date->getDay();
            $weekday = str_pad($day, 2, '0', STR_PAD_LEFT);
            $this->assertSame("$weekday.01.1970 00:00:00", $dw->toString());
        }
    }

    /**
     * @group   ZF-8332
     */
    public function testSetDayOnThirtyFirstGivesThirtyOne()
    {
        $locale = new Zend_Locale('en_US');
        $date = new Zend_Date();
        $date->setYear(2009, $locale)
             ->setMonth(5, $locale)
             ->setDay(31, $locale);
        $this->assertSame('5/31/09', $date->get(Zend_Date::DATE_SHORT, $locale));
    }

    /**
     * test setWeekday
     */
    public function testSetWeekday()
    {
        $date = new Zend_Date('2006-01-01','YYYY-MM-dd', 'en');
        $date->setWeekday(1);
        $this->assertSame('2005-12-26T00:00:00+05:00', $date->getIso());

        $date->set('2006-01-02', 'YYYY-MM-dd');
        $date->setWeekday(1);
        $this->assertSame('2006-01-02T00:00:00+05:00', $date->getIso());
    }

    /**
     * test setLocale/getLocale
     */
    public function testSetLocale()
    {
        $date = new Zend_Date(0, 'de');

        $this->assertSame('de', $date->getLocale());
        $date->setLocale('en');
        $this->assertSame('en', $date->getLocale());
        $date->setLocale('en_XX');
        $this->assertSame('en', $date->getLocale());
        $date->setLocale('de_AT');
        $this->assertSame('de_AT', $date->getLocale());
        $locale = new Zend_Locale('ar');
        $date->setLocale($locale);
        $this->assertSame('ar', $date->getLocale());

        try {
            $date->setLocale('xx_XX');
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    /**
     * test looseBehaviour
     */
    public function testLoose()
    {
        $date = new Zend_Date(0, 'de_DE');

        try {
            $date->set(null, Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->set(10, 'de_DE');
        $this->assertEquals(10, $date->getTimestamp());

        try {
            $date->add(null, Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->add(10, 'de_DE');
        $this->assertEquals(20, $date->getTimestamp());

        try {
            $date->sub(null, Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        $date->sub(10, 'de_DE');
        $this->assertEquals(10, $date->getTimestamp());

        try {
            $date->compare(null, Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->equals(null, Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->isEarlier(null, Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->isLater(null, Zend_Date::YEAR);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setTime(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addTime(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subTime(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareTime(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setDate(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addDate(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subDate(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareDate(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setIso(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addIso(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subIso(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareIso(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setArpa(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addArpa(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subArpa(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareArpa(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setMonth(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addMonth(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subMonth(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareMonth(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setDay(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addDay(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subDay(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareDay(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setWeekday(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addWeekday(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subWeekday(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareWeekday(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setDayOfYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addDayOfYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subDayOfYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareDayOfYear(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setHour(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addHour(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subHour(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareHour(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setMinute(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addMinute(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subMinute(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareMinute(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setSecond(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addSecond(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subSecond(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareSecond(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->setWeek(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->addWeek(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->subWeek(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            $date->compareWeek(null);
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
    }

    public function testTimesync()
    {
        try {
            $server = new Zend_TimeSync('ntp://pool.ntp.org', 'alias');
            $date1 = $server->getDate();
            // need to use the proxy class to simulate time() returning wrong value
            $date2 = new Zend_Date_TestHelper(time());

            $info = $server->getInfo();

            if (($info['offset'] >= 0.5) || ($info['offset'] <= -0.52)) {
                $this->assertFalse($date1->getTimestamp() == $date2->getTimestamp());
            } else {
                $this->assertEquals($date1->getTimestamp(), $date2->getTimestamp());
            }
        } catch (Zend_TimeSync_Exception $e) {
            $this->markTestIncomplete('NTP timeserver not available.');
        }
    }

    public function testUsePhpDateFormat()
    {
        Zend_Date::setOptions(array('format_type' => 'iso'));

        // PHP date() format specifier tests
        $date1 = new Zend_Date('2006-01-02 23:58:59', Zend_Date::ISO_8601, 'en_US');
        $date2 = new Zend_Date('2006-01-02 23:58:59', 'YYYY-MM-dd HH:mm:ss', 'en_US');
        $this->assertSame($date1->getTimestamp(), $date2->getTimestamp());

        date_default_timezone_set('GMT');
        $date = new Zend_Date(0); // 1970-01-01 is a Thursday (should be 4 for 'w' format specifier)
        $this->assertSame(gmdate('w',$date->getTimestamp()), $date->toString(                    'eee'));
        $this->assertSame(gmdate('d',$date->getTimestamp()), $date->toString(                     'dd'));
        $this->assertSame(gmdate('D',$date->getTimestamp()), $date->toString(              'EEE', 'en'));
        $this->assertSame(gmdate('j',$date->getTimestamp()), $date->toString(                      'd'));
        $this->assertSame(gmdate('l',$date->getTimestamp()), $date->toString(             'EEEE', 'en'));
        $this->assertSame(gmdate('N',$date->getTimestamp()), $date->toString(                      'e'));
        $this->assertSame(gmdate('S',$date->getTimestamp()), $date->toString(                     'SS'));
        $this->assertSame(gmdate('z',$date->getTimestamp()), $date->toString(                      'D'));
        $this->assertSame(gmdate('W',$date->getTimestamp()), $date->toString(                     'ww'));
        $this->assertSame(gmdate('F',$date->getTimestamp()), $date->toString(             'MMMM', 'en'));
        $this->assertSame(gmdate('m',$date->getTimestamp()), $date->toString(                     'MM'));
        $this->assertSame(gmdate('M',$date->getTimestamp()), $date->toString(              'MMM', 'en'));
        $this->assertSame(gmdate('n',$date->getTimestamp()), $date->toString(                      'M'));
        $this->assertSame(gmdate('t',$date->getTimestamp()), $date->toString(                    'ddd'));
        $this->assertSame(gmdate('L',$date->getTimestamp()), $date->toString(                      'l'));
        $this->assertSame(gmdate('o',$date->getTimestamp()), $date->toString(                   'YYYY'));
        $this->assertSame(gmdate('Y',$date->getTimestamp()), $date->toString(                   'yyyy'));
        $this->assertSame(gmdate('y',$date->getTimestamp()), $date->toString(                     'yy'));
        $this->assertSame(gmdate('a',$date->getTimestamp()), strtolower($date->toString(    'a', 'en')));
        $this->assertSame(gmdate('A',$date->getTimestamp()), strtoupper($date->toString(    'a', 'en')));
        $this->assertSame(gmdate('B',$date->getTimestamp()), $date->toString(                      'B'));
        $this->assertSame(gmdate('g',$date->getTimestamp()), $date->toString(                      'h'));
        $this->assertSame(gmdate('G',$date->getTimestamp()), $date->toString(                      'H'));
        $this->assertSame(gmdate('h',$date->getTimestamp()), $date->toString(                     'hh'));
        $this->assertSame(gmdate('H',$date->getTimestamp()), $date->toString(                     'HH'));
        $this->assertSame(gmdate('i',$date->getTimestamp()), $date->toString(                     'mm'));
        $this->assertSame(gmdate('s',$date->getTimestamp()), $date->toString(                     'ss'));
        $this->assertSame(  date('e',$date->getTimestamp()), $date->toString(                   'zzzz'));
        $this->assertSame(gmdate('I',$date->getTimestamp()), $date->toString(                      'I'));
        $this->assertSame(gmdate('O',$date->getTimestamp()), $date->toString(                      'Z'));
        $this->assertSame(gmdate('P',$date->getTimestamp()), $date->toString(                   'ZZZZ'));
        $this->assertSame(gmdate('T',$date->getTimestamp()), $date->toString(                      'z'));
        $this->assertSame(gmdate('Z',$date->getTimestamp()), $date->toString(                      'X'));
        $this->assertSame(gmdate('c',$date->getTimestamp()), $date->toString('yyyy-MM-ddTHH:mm:ssZZZZ'));
        $this->assertSame(gmdate('r',$date->getTimestamp()), $date->toString(                      'r'));
        $this->assertSame(gmdate('U',$date->getTimestamp()), $date->toString(                      'U'));

        // PHP date() format specifier tests
        $date1 = new Zend_Date('2006-01-02 23:58:59', Zend_Date::ISO_8601, 'en_US');
        Zend_Date::setOptions(array('format_type' => 'php'));
        $date2 = new Zend_Date('2006-01-02 23:58:59', 'Y-m-d H:i:s', 'en_US');
        $this->assertSame($date1->getTimestamp(), $date2->getTimestamp());

        date_default_timezone_set('GMT');
        $date = new Zend_Date(0); // 1970-01-01 is a Thursday (should be 4 for 'w' format specifier)
        $this->assertSame(gmdate('w',$date->getTimestamp()), $date->toString(      'w'));
        $this->assertSame(gmdate('d',$date->getTimestamp()), $date->toString(      'd'));
        $this->assertSame(gmdate('D',$date->getTimestamp()), $date->toString('D', 'en'));
        $this->assertSame(gmdate('j',$date->getTimestamp()), $date->toString(      'j'));
        $this->assertSame(gmdate('l',$date->getTimestamp()), $date->toString('l', 'en'));
        $this->assertSame(gmdate('N',$date->getTimestamp()), $date->toString(      'N'));
        $this->assertSame(gmdate('S',$date->getTimestamp()), $date->toString(      'S'));
        $this->assertSame(gmdate('z',$date->getTimestamp()), $date->toString(      'z'));
        $this->assertSame(gmdate('W',$date->getTimestamp()), $date->toString(      'W'));
        $this->assertSame(gmdate('F',$date->getTimestamp()), $date->toString('F', 'en'));
        $this->assertSame(gmdate('m',$date->getTimestamp()), $date->toString(      'm'));
        $this->assertSame(gmdate('M',$date->getTimestamp()), $date->toString('M', 'en'));
        $this->assertSame(gmdate('n',$date->getTimestamp()), $date->toString(      'n'));
        $this->assertSame(gmdate('t',$date->getTimestamp()), $date->toString(      't'));
        $this->assertSame(gmdate('L',$date->getTimestamp()), $date->toString(      'L'));
        $this->assertSame(gmdate('o',$date->getTimestamp()), $date->toString(      'o'));
        $this->assertSame(gmdate('Y',$date->getTimestamp()), $date->toString(      'Y'));
        $this->assertSame(gmdate('y',$date->getTimestamp()), $date->toString(      'y'));
        $this->assertSame(gmdate('a',$date->getTimestamp()), strtolower($date->toString('a', 'en')));
        $this->assertSame(gmdate('A',$date->getTimestamp()), strtoupper($date->toString('A', 'en')));
        $this->assertSame(gmdate('B',$date->getTimestamp()), $date->toString(      'B'));
        $this->assertSame(gmdate('g',$date->getTimestamp()), $date->toString(      'g'));
        $this->assertSame(gmdate('G',$date->getTimestamp()), $date->toString(      'G'));
        $this->assertSame(gmdate('h',$date->getTimestamp()), $date->toString(      'h'));
        $this->assertSame(gmdate('H',$date->getTimestamp()), $date->toString(      'H'));
        $this->assertSame(gmdate('i',$date->getTimestamp()), $date->toString(      'i'));
        $this->assertSame(gmdate('s',$date->getTimestamp()), $date->toString(      's'));
        $this->assertSame(  date('e',$date->getTimestamp()), $date->toString(      'e'));
        $this->assertSame(gmdate('I',$date->getTimestamp()), $date->toString(      'I'));
        $this->assertSame(gmdate('O',$date->getTimestamp()), $date->toString(      'O'));
        $this->assertSame(gmdate('P',$date->getTimestamp()), $date->toString(      'P'));
        $this->assertSame(gmdate('T',$date->getTimestamp()), $date->toString(      'T'));
        $this->assertSame(gmdate('Z',$date->getTimestamp()), $date->toString(      'Z'));
        $this->assertSame(gmdate('c',$date->getTimestamp()), $date->toString(      'c'));
        $this->assertSame(gmdate('r',$date->getTimestamp()), $date->toString(      'r'));
        $this->assertSame(gmdate('U',$date->getTimestamp()), $date->toString(      'U'));

        date_default_timezone_set('GMT');
        $date = new Zend_Date(mktime(20,10,0,10,10,2000)); // 1970-01-01 is a Thursday (should be 4 for 'w' format specifier)
        $this->assertSame(gmdate('w',$date->getTimestamp()), $date->toString(      'w'));
        $this->assertSame(gmdate('d',$date->getTimestamp()), $date->toString(      'd'));
        $this->assertSame(gmdate('D',$date->getTimestamp()), $date->toString('D', 'en'));
        $this->assertSame(gmdate('j',$date->getTimestamp()), $date->toString(      'j'));
        $this->assertSame(gmdate('l',$date->getTimestamp()), $date->toString('l', 'en'));
        $this->assertSame(gmdate('N',$date->getTimestamp()), $date->toString(      'N'));
        $this->assertSame(gmdate('S',$date->getTimestamp()), $date->toString(      'S'));
        $this->assertSame(gmdate('z',$date->getTimestamp()), $date->toString(      'z'));
        $this->assertSame(gmdate('W',$date->getTimestamp()), $date->toString(      'W'));
        $this->assertSame(gmdate('F',$date->getTimestamp()), $date->toString('F', 'en'));
        $this->assertSame(gmdate('m',$date->getTimestamp()), $date->toString(      'm'));
        $this->assertSame(gmdate('M',$date->getTimestamp()), $date->toString('M', 'en'));
        $this->assertSame(gmdate('n',$date->getTimestamp()), $date->toString(      'n'));
        $this->assertSame(gmdate('t',$date->getTimestamp()), $date->toString(      't'));
        $this->assertSame(gmdate('L',$date->getTimestamp()), $date->toString(      'L'));
        $this->assertSame(gmdate('o',$date->getTimestamp()), $date->toString(      'o'));
        $this->assertSame(gmdate('Y',$date->getTimestamp()), $date->toString(      'Y'));
        $this->assertSame(gmdate('y',$date->getTimestamp()), $date->toString(      'y'));
        $this->assertSame(gmdate('a',$date->getTimestamp()), strtolower($date->toString('a', 'en')));
        $this->assertSame(gmdate('A',$date->getTimestamp()), strtoupper($date->toString('A', 'en')));
        $this->assertSame(gmdate('B',$date->getTimestamp()), $date->toString(      'B'));
        $this->assertSame(gmdate('g',$date->getTimestamp()), $date->toString(      'g'));
        $this->assertSame(gmdate('G',$date->getTimestamp()), $date->toString(      'G'));
        $this->assertSame(gmdate('h',$date->getTimestamp()), $date->toString(      'h'));
        $this->assertSame(gmdate('H',$date->getTimestamp()), $date->toString(      'H'));
        $this->assertSame(gmdate('i',$date->getTimestamp()), $date->toString(      'i'));
        $this->assertSame(gmdate('s',$date->getTimestamp()), $date->toString(      's'));
        $this->assertSame(  date('e',$date->getTimestamp()), $date->toString(      'e'));
        $this->assertSame(gmdate('I',$date->getTimestamp()), $date->toString(      'I'));
        $this->assertSame(gmdate('O',$date->getTimestamp()), $date->toString(      'O'));
        $this->assertSame(gmdate('P',$date->getTimestamp()), $date->toString(      'P'));
        $this->assertSame(gmdate('T',$date->getTimestamp()), $date->toString(      'T'));
        $this->assertSame(gmdate('Z',$date->getTimestamp()), $date->toString(      'Z'));
        $this->assertSame(gmdate('c',$date->getTimestamp()), $date->toString(      'c'));
        $this->assertSame(gmdate('r',$date->getTimestamp()), $date->toString(      'r'));
        $this->assertSame(gmdate('U',$date->getTimestamp()), $date->toString(      'U'));

        date_default_timezone_set('Indian/Maldives');
        $date = new Zend_Date(0); // 1970-01-01 is a Thursday (should be 4 for 'w' format specifier)
        $this->assertSame(date('w',$date->getTimestamp()), $date->toString(      'w'));
        $this->assertSame(date('d',$date->getTimestamp()), $date->toString(      'd'));
        $this->assertSame(date('D',$date->getTimestamp()), $date->toString('D', 'en'));
        $this->assertSame(date('j',$date->getTimestamp()), $date->toString(      'j'));
        $this->assertSame(date('l',$date->getTimestamp()), $date->toString('l', 'en'));
        $this->assertSame(date('N',$date->getTimestamp()), $date->toString(      'N'));
        $this->assertSame(date('S',$date->getTimestamp()), $date->toString(      'S'));
        $this->assertSame(date('z',$date->getTimestamp()), $date->toString(      'z'));
        $this->assertSame(date('W',$date->getTimestamp()), $date->toString(      'W'));
        $this->assertSame(date('F',$date->getTimestamp()), $date->toString('F', 'en'));
        $this->assertSame(date('m',$date->getTimestamp()), $date->toString(      'm'));
        $this->assertSame(date('M',$date->getTimestamp()), $date->toString('M', 'en'));
        $this->assertSame(date('n',$date->getTimestamp()), $date->toString(      'n'));
        $this->assertSame(date('t',$date->getTimestamp()), $date->toString(      't'));
        $this->assertSame(date('L',$date->getTimestamp()), $date->toString(      'L'));
        $this->assertSame(date('o',$date->getTimestamp()), $date->toString(      'o'));
        $this->assertSame(date('Y',$date->getTimestamp()), $date->toString(      'Y'));
        $this->assertSame(date('y',$date->getTimestamp()), $date->toString(      'y'));
        $this->assertSame(date('a',$date->getTimestamp()), strtolower($date->toString('a', 'en')));
        $this->assertSame(date('A',$date->getTimestamp()), strtoupper($date->toString('A', 'en')));
        $this->assertSame(date('B',$date->getTimestamp()), $date->toString(      'B'));
        $this->assertSame(date('g',$date->getTimestamp()), $date->toString(      'g'));
        $this->assertSame(date('G',$date->getTimestamp()), $date->toString(      'G'));
        $this->assertSame(date('h',$date->getTimestamp()), $date->toString(      'h'));
        $this->assertSame(date('H',$date->getTimestamp()), $date->toString(      'H'));
        $this->assertSame(date('i',$date->getTimestamp()), $date->toString(      'i'));
        $this->assertSame(date('s',$date->getTimestamp()), $date->toString(      's'));
        $this->assertSame(date('e',$date->getTimestamp()), $date->toString(      'e'));
        $this->assertSame(date('I',$date->getTimestamp()), $date->toString(      'I'));
        $this->assertSame(date('O',$date->getTimestamp()), $date->toString(      'O'));
        $this->assertSame(date('P',$date->getTimestamp()), $date->toString(      'P'));
        $this->assertSame(date('T',$date->getTimestamp()), $date->toString(      'T'));
        $this->assertSame(date('Z',$date->getTimestamp()), $date->toString(      'Z'));
        $this->assertSame(date('c',$date->getTimestamp()), $date->toString(      'c'));
        $this->assertSame(date('r',$date->getTimestamp()), $date->toString(      'r'));
        $this->assertSame(date('U',$date->getTimestamp()), $date->toString(      'U'));

        date_default_timezone_set('Indian/Maldives');
        $date = new Zend_Date(mktime(20,10,0,10,10,2000)); // 1970-01-01 is a Thursday (should be 4 for 'w' format specifier)
        $this->assertSame(date('w',$date->getTimestamp()), $date->toString(      'w'));
        $this->assertSame(date('d',$date->getTimestamp()), $date->toString(      'd'));
        $this->assertSame(date('D',$date->getTimestamp()), $date->toString('D', 'en'));
        $this->assertSame(date('j',$date->getTimestamp()), $date->toString(      'j'));
        $this->assertSame(date('l',$date->getTimestamp()), $date->toString('l', 'en'));
        $this->assertSame(date('N',$date->getTimestamp()), $date->toString(      'N'));
        $this->assertSame(date('S',$date->getTimestamp()), $date->toString(      'S'));
        $this->assertSame(date('z',$date->getTimestamp()), $date->toString(      'z'));
        $this->assertSame(date('W',$date->getTimestamp()), $date->toString(      'W'));
        $this->assertSame(date('F',$date->getTimestamp()), $date->toString('F', 'en'));
        $this->assertSame(date('m',$date->getTimestamp()), $date->toString(      'm'));
        $this->assertSame(date('M',$date->getTimestamp()), $date->toString('M', 'en'));
        $this->assertSame(date('n',$date->getTimestamp()), $date->toString(      'n'));
        $this->assertSame(date('t',$date->getTimestamp()), $date->toString(      't'));
        $this->assertSame(date('L',$date->getTimestamp()), $date->toString(      'L'));
        $this->assertSame(date('o',$date->getTimestamp()), $date->toString(      'o'));
        $this->assertSame(date('Y',$date->getTimestamp()), $date->toString(      'Y'));
        $this->assertSame(date('y',$date->getTimestamp()), $date->toString(      'y'));
        $this->assertSame(date('a',$date->getTimestamp()), strtolower($date->toString('a', 'en')));
        $this->assertSame(date('A',$date->getTimestamp()), strtoupper($date->toString('A', 'en')));
        $this->assertSame(date('B',$date->getTimestamp()), $date->toString(      'B'));
        $this->assertSame(date('g',$date->getTimestamp()), $date->toString(      'g'));
        $this->assertSame(date('G',$date->getTimestamp()), $date->toString(      'G'));
        $this->assertSame(date('h',$date->getTimestamp()), $date->toString(      'h'));
        $this->assertSame(date('H',$date->getTimestamp()), $date->toString(      'H'));
        $this->assertSame(date('i',$date->getTimestamp()), $date->toString(      'i'));
        $this->assertSame(date('s',$date->getTimestamp()), $date->toString(      's'));
        $this->assertSame(date('e',$date->getTimestamp()), $date->toString(      'e'));
        $this->assertSame(date('I',$date->getTimestamp()), $date->toString(      'I'));
        $this->assertSame(date('O',$date->getTimestamp()), $date->toString(      'O'));
        $this->assertSame(date('P',$date->getTimestamp()), $date->toString(      'P'));
        $this->assertSame(date('T',$date->getTimestamp()), $date->toString(      'T'));
        $this->assertSame(date('Z',$date->getTimestamp()), $date->toString(      'Z'));
        $this->assertSame(date('c',$date->getTimestamp()), $date->toString(      'c'));
        $this->assertSame(date('r',$date->getTimestamp()), $date->toString(      'r'));
        $this->assertSame(date('U',$date->getTimestamp()), $date->toString(      'U'));
        Zend_Date::setOptions(array('format_type' => 'iso'));
    }

    public function testDaylightsaving()
    {
        $date = new Zend_Date('2007.03.25', Zend_Date::DATES);
        $date->set('16:00:00', Zend_Date::TIMES);
        $this->assertEquals('2007-03-25T16:00:00+05:00', $date->get(Zend_Date::W3C));
        $date->set('01:00:00', Zend_Date::TIMES);
        $this->assertEquals('2007-03-25T01:00:00+05:00', $date->get(Zend_Date::W3C));
    }

    public function testSetOptions()
    {
        $options = Zend_Date::setOptions();
        $this->assertTrue(is_array($options));
        $this->assertEquals('iso', $options['format_type']);

        Zend_Date::setOptions(array('format_type' => 'php'));
        $options = Zend_Date::setOptions();
        $this->assertEquals('php', $options['format_type']);

        try {
            Zend_Date::setOptions(array('format_type' => 'non'));
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            Zend_Date::setOptions(array('unknown' => 'non'));
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }
        try {
            Zend_Date::setOptions(array('fix_dst' => 2));
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        try {
            Zend_Date::setOptions(array('fix_dst' => 2));
            $this->fail();
        } catch (Zend_Date_Exception $e) {
            // success
        }

        require_once 'Zend/Cache.php';
        $cache = Zend_Cache::factory('Core', 'File',
                 array('lifetime' => 120, 'automatic_serialization' => true),
                 array('cache_dir' => dirname(__FILE__) . '/_files/'));
        Zend_Date::setOptions(array('cache' => $cache));
    }

    public function testIsDate()
    {
        $this->assertTrue(Zend_Date::isDate('25.03.2007', 'de_AT'));
        $this->assertTrue(Zend_Date::isDate('2007.03.25', 'YYYY.MM.dd'));
        $this->assertTrue(Zend_Date::isDate('25.Mai.2007', 'dd.MMMM.YYYY', 'de_AT'));
        $this->assertTrue(Zend_Date::isDate('25.Mai.2007 10:00:00', 'dd.MMMM.YYYY', 'de_AT'));
        $this->assertFalse(Zend_Date::isDate('32.Mai.2007 10:00:00', 'dd.MMMM.YYYY', 'de_AT'));
        $this->assertFalse(Zend_Date::isDate('30.Februar.2007 10:00:00', 'dd.MMMM.YYYY', 'de_AT'));
        $this->assertFalse(Zend_Date::isDate('30.Februar.2007 30:00:00', 'dd.MMMM.YYYY HH:mm:ss', 'de_AT'));
        $this->assertFalse(Zend_Date::isDate(3.01));
    }

    public function testToArray()
    {
        $date = new Zend_Date('2006-01-02 23:58:59', Zend_Date::ISO_8601, 'en_US');
        $return = $date->toArray();
        $orig = array('day' => 02, 'month' => 01, 'year' => 2006, 'hour' => 23, 'minute' => 58,
                      'second' => 59, 'timezone' => 'MVT', 'timestamp' => 1136228339, 'weekday' => 1,
                      'dayofyear' => 1, 'week' => '01', 'gmtsecs' => 18000);
        $this->assertEquals($orig, $return);
    }

    public function testFromArray()
    {
        $date = new Zend_Date(array('day' => 04, 'month' => 12, 'year' => 2006, 'hour' => 10,
                                    'minute' => 56, 'second' => 30), 'en_US');
        $this->assertSame('2006-12-04T10:56:30+05:00', $date->getIso());
    }

    public function testTimezoneArray()
    {
        date_default_timezone_set('UTC');

        $date = new Zend_Date(array('year' => 2007, 'month' => 1, 'day' => 1,
                                    'hour' => 20, 'minute' => 45, 'second' => 37, 'en_US'));
        $this->assertSame('2007-01-01T20:45:37+00:00', $date->getIso());

        date_default_timezone_set('CET');
        $date = new Zend_Date(array('year' => 2007, 'month' => 1, 'day' => 1,
                                    'hour' => 1, 'minute' => 45, 'second' => 37, 'en_US'));
        $this->assertSame('2007-01-01T01:45:37+01:00', $date->getIso());

        $date = new Zend_Date(array('year' => 2006, 'month' => 4, 'day' => 18,
                                    'hour' => 12, 'minute' => 3, 'second' => 10, 'de_AT'));
        $this->assertSame('2006-04-18T12:03:10+02:00', $date->getIso());

        $date = new Zend_Date(array('year' => 2009, 'month' => 1, 'day' => 28,
                                    'hour' => 23, 'minute' => 30, 'second' => 00, 'de'));
        $this->assertSame('2009-01-28T23:30:00+01:00', $date->getIso());

        $date = new Zend_Date(array('year' => 2009, 'month' => 8, 'day' => 28,
                                      'hour' => 22, 'minute' => 00, 'second' => 00, 'de'));
        $this->assertSame('2009-08-28T22:00:00+02:00', $date->getIso());
    }

    public function testExtendedDst()
    {
        Zend_Date::setOptions(array('format_type' => 'iso'));
        $date = new Zend_Date();
        $date->setTimezone('UTC');
        $date->set('25-05-2050 12:00:00');
        $this->assertSame('2050-05-25 12:00:00', $date->get('YYYY-MM-dd HH:mm:ss'));
        $date->setTimezone('Europe/Warsaw');
        $this->assertSame('2050-05-25 14:00:00', $date->get('YYYY-MM-dd HH:mm:ss'));

        $date->setTimezone('UTC');
        $date->set('25-05-2020 12:00:00');
        $this->assertSame('2020-05-25 12:00:00', $date->get('YYYY-MM-dd HH:mm:ss'));
        $date->setTimezone('Europe/Warsaw');
        $this->assertSame('2020-05-25 14:00:00', $date->get('YYYY-MM-dd HH:mm:ss'));
    }

    public function testGetFullYear()
    {
        $this->assertSame(1970, Zend_Date::getFullYear(70));
        $this->assertSame(1999, Zend_Date::getFullYear(99));
        $this->assertSame(2000, Zend_Date::getFullYear(0));
        $this->assertSame(2037, Zend_Date::getFullYear(37));
        $this->assertSame(2069, Zend_Date::getFullYear(69));
        $this->assertSame(-4, Zend_Date::getFullYear(-4));
        $this->assertSame(100, Zend_Date::getFullYear(100));
    }

    /**
     * Test for ZF-3677
     */
    public function testZF3677()
    {
        $locale = new Zend_Locale('de_AT');
        require_once 'Zend/Registry.php';
        Zend_Registry::set('Zend_Locale', $locale);

        $date   = new Zend_Date('13',null,$locale);
        $this->assertSame($date->getLocale(), $locale->toString());
    }

    /**
     * Test for ZF-4867
     */
    public function testZF4867()
    {
        date_default_timezone_set('America/New_York');
        $date1  = new Zend_Date('2006-01-01 01:00:00 Europe/Paris', Zend_Date::ISO_8601);
        $this->assertEquals('Europe/Paris', $date1->getTimezone());
    }

    /**
     * Test for ZF-5203
     */
    public function testMultiByteWeekdaysShouldNotBeTruncated()
    {
        $date1  = new Zend_Date('pl');
        $date1->setWeekday(3);
        $this->assertEquals('ś', $date1->get(Zend_Date::WEEKDAY_NARROW));
    }

    /**
     * Test for False Month Addition
     */
    public function testAddingMonthWhenChangingTimezone()
    {
        $date  = new Zend_Date(mktime(22, 59, 59, 1, 10, 2009));
        $this->assertEquals(10, $date->toString('d'));
        $this->assertEquals( 1, $date->toString('M'));
        $date->setTimezone('Europe/Berlin');
        $date->addMonth(1);
        $this->assertEquals(10, $date->toString('d'));
        $this->assertEquals( 2, $date->toString('M'));
    }

    /**
     * Test for False Month Addition
     */
    public function testGmtOffsetValues()
    {
        date_default_timezone_set('Pacific/Auckland');
        $time  = time();
        $date  = new Zend_Date($time);
        $stamp = $date->getGmtOffset();

        $localtime = localtime($time, true);
        $offset = mktime($localtime['tm_hour'],
                         $localtime['tm_min'],
                         $localtime['tm_sec'],
                         $localtime['tm_mon'] + 1,
                         $localtime['tm_mday'],
                         $localtime['tm_year'] + 1900)
              - gmmktime($localtime['tm_hour'],
                         $localtime['tm_min'],
                         $localtime['tm_sec'],
                         $localtime['tm_mon'] + 1,
                         $localtime['tm_mday'],
                         $localtime['tm_year'] + 1900);

        $this->assertEquals($stamp, $offset);

        $date->addMonth(6);
        $stamp = $date->getGmtOffset();


        $localtime = localtime($time, true);
        $offset = mktime($localtime['tm_hour'],
                         $localtime['tm_min'],
                         $localtime['tm_sec'],
                         $localtime['tm_mon'] + 7,
                         $localtime['tm_mday'],
                         $localtime['tm_year'] + 1900)
              - gmmktime($localtime['tm_hour'],
                         $localtime['tm_min'],
                         $localtime['tm_sec'],
                         $localtime['tm_mon'] + 7,
                         $localtime['tm_mday'],
                         $localtime['tm_year'] + 1900);

        $this->assertEquals($stamp, $offset);
    }

    public function testIsDateWithConstants()
    {
        $this->assertTrue(Zend_Date::isDate('2009-02-13T23:31:30+00:00', Zend_Date::W3C, 'de_AT'));

        $date = new Zend_Date();
        $string = $date->toString(Zend_Date::DATES);
        $this->assertTrue(Zend_Date::isDate($string, Zend_Date::DATES));
    }

    /**
     * @ZF-7154
     */
    public function testZF7154()
    {
        $locale = new Zend_Locale('de_AT');

        $date = new Zend_Date(1577833200,$locale);
        $date2 = new Zend_Date(2006, Zend_Date::YEAR);
        $date->setTimeZone(date_default_timezone_get());

        $date->setYear(2000);
        $date->setMonth('Apr.');
        $this->assertSame('2000-04-01T04:00:00+05:00', $date->get(Zend_Date::W3C));

        $date->setYear(2004);
        $date->setMonth('Februar');
        $this->assertSame('2004-02-01T04:00:00+05:00', $date->get(Zend_Date::W3C));
    }

    /**
     * @ZF-7202
     */
    public function testZF7202()
    {
        $date     = new Zend_Date();
        $timezone = $date->getTimezoneFromString('03:58:09 Jul 06, 2009 Indian/Reunion');
        $this->assertSame('Indian/Reunion', $timezone);
    }

    /**
     * @ZF-7589
     */
    public function testSetDateWithArray()
    {
        $date   = new Zend_Date(1234567890);
        $result = $date->setDate(array('year' => 2009, 'month' => 8, 'day' => 14));

        $this->assertSame('2009-08-14T04:31:30+05:00', $result->get(Zend_Date::W3C));
    }

    /**
     * @ZF-7454
     */
    public function testSetWithoutHourAtDSTChange()
    {
        $this->assertTrue(Zend_Date::isDate("23/05/2010", "dd/MM/yyyy", "it_IT"));
        $this->assertTrue(Zend_Date::isDate("24/05/2010", "dd/MM/yyyy", "it_IT"));
    }

    /**
     * @ZF-7456
     */
    public function testSetArrayDateWithoutHour()
    {
        $date = new Zend_Date(array(
            'year'=>2008,
            'month'=>3,
            'day'=>1)
        );
        $this->assertEquals('2008-03-01T00:00:00+05:00', $date->getIso());
    }

    /**
     * @ZF-7745
     *
     */
    public function testSetFirstDayOfLeapYear()
    {
        $date = new Zend_Date(2008, Zend_Date::YEAR);
        $date->setDayOfYear(1);
        $this->assertEquals('2008-01-01T00:00:00+05:00', $date->getIso());

        $date->setDayOfYear(61);
        $this->assertEquals('2008-03-01T00:00:00+05:00', $date->getIso());

        $date->setDayOfYear(62);
        $this->assertEquals('2008-03-02T00:00:00+05:00', $date->getIso());
    }

    /**
     * @ZF-7913
     */
    public function testUsePhpNFormat()
    {
        Zend_Date::setOptions(array('format_type' => 'php'));

        date_default_timezone_set('GMT');
        $date = new Zend_Date(mktime(20,10,0,9,20,2009));
        $this->assertSame(gmdate('w',$date->getTimestamp()), $date->toString(      'w'));
        $this->assertSame(gmdate('d',$date->getTimestamp()), $date->toString(      'd'));
        $this->assertSame(gmdate('D',$date->getTimestamp()), $date->toString('D', 'en'));
        $this->assertSame(gmdate('j',$date->getTimestamp()), $date->toString(      'j'));
        $this->assertSame(gmdate('l',$date->getTimestamp()), $date->toString('l', 'en'));
        $this->assertSame(gmdate('N',$date->getTimestamp()), $date->toString(      'N'));
        $this->assertSame(gmdate('S',$date->getTimestamp()), $date->toString(      'S'));
        $this->assertSame(gmdate('z',$date->getTimestamp()), $date->toString(      'z'));
        $this->assertSame(gmdate('W',$date->getTimestamp()), $date->toString(      'W'));
        $this->assertSame(gmdate('F',$date->getTimestamp()), $date->toString('F', 'en'));
        $this->assertSame(gmdate('m',$date->getTimestamp()), $date->toString(      'm'));
        $this->assertSame(gmdate('M',$date->getTimestamp()), $date->toString('M', 'en'));
        $this->assertSame(gmdate('n',$date->getTimestamp()), $date->toString(      'n'));
        $this->assertSame(gmdate('t',$date->getTimestamp()), $date->toString(      't'));
        $this->assertSame(gmdate('L',$date->getTimestamp()), $date->toString(      'L'));
        $this->assertSame(gmdate('o',$date->getTimestamp()), $date->toString(      'o'));
        $this->assertSame(gmdate('Y',$date->getTimestamp()), $date->toString(      'Y'));
        $this->assertSame(gmdate('y',$date->getTimestamp()), $date->toString(      'y'));
        $this->assertSame(gmdate('a',$date->getTimestamp()), strtolower($date->toString('a', 'en')));
        $this->assertSame(gmdate('A',$date->getTimestamp()), strtoupper($date->toString('A', 'en')));
        $this->assertSame(gmdate('B',$date->getTimestamp()), $date->toString(      'B'));
        $this->assertSame(gmdate('g',$date->getTimestamp()), $date->toString(      'g'));
        $this->assertSame(gmdate('G',$date->getTimestamp()), $date->toString(      'G'));
        $this->assertSame(gmdate('h',$date->getTimestamp()), $date->toString(      'h'));
        $this->assertSame(gmdate('H',$date->getTimestamp()), $date->toString(      'H'));
        $this->assertSame(gmdate('i',$date->getTimestamp()), $date->toString(      'i'));
        $this->assertSame(gmdate('s',$date->getTimestamp()), $date->toString(      's'));
        $this->assertSame(  date('e',$date->getTimestamp()), $date->toString(      'e'));
        $this->assertSame(gmdate('I',$date->getTimestamp()), $date->toString(      'I'));
        $this->assertSame(gmdate('O',$date->getTimestamp()), $date->toString(      'O'));
        $this->assertSame(gmdate('P',$date->getTimestamp()), $date->toString(      'P'));
        $this->assertSame(gmdate('T',$date->getTimestamp()), $date->toString(      'T'));
        $this->assertSame(gmdate('Z',$date->getTimestamp()), $date->toString(      'Z'));
        $this->assertSame(gmdate('c',$date->getTimestamp()), $date->toString(      'c'));
        $this->assertSame(gmdate('r',$date->getTimestamp()), $date->toString(      'r'));
        $this->assertSame(gmdate('U',$date->getTimestamp()), $date->toString(      'U'));

        date_default_timezone_set('Indian/Maldives');
        $date = new Zend_Date(mktime(20,10,0,9,20,2009));
        $this->assertSame(date('w',$date->getTimestamp()), $date->toString(      'w'));
        $this->assertSame(date('d',$date->getTimestamp()), $date->toString(      'd'));
        $this->assertSame(date('D',$date->getTimestamp()), $date->toString('D', 'en'));
        $this->assertSame(date('j',$date->getTimestamp()), $date->toString(      'j'));
        $this->assertSame(date('l',$date->getTimestamp()), $date->toString('l', 'en'));
        $this->assertSame(date('N',$date->getTimestamp()), $date->toString(      'N'));
        $this->assertSame(date('S',$date->getTimestamp()), $date->toString(      'S'));
        $this->assertSame(date('z',$date->getTimestamp()), $date->toString(      'z'));
        $this->assertSame(date('W',$date->getTimestamp()), $date->toString(      'W'));
        $this->assertSame(date('F',$date->getTimestamp()), $date->toString('F', 'en'));
        $this->assertSame(date('m',$date->getTimestamp()), $date->toString(      'm'));
        $this->assertSame(date('M',$date->getTimestamp()), $date->toString('M', 'en'));
        $this->assertSame(date('n',$date->getTimestamp()), $date->toString(      'n'));
        $this->assertSame(date('t',$date->getTimestamp()), $date->toString(      't'));
        $this->assertSame(date('L',$date->getTimestamp()), $date->toString(      'L'));
        $this->assertSame(date('o',$date->getTimestamp()), $date->toString(      'o'));
        $this->assertSame(date('Y',$date->getTimestamp()), $date->toString(      'Y'));
        $this->assertSame(date('y',$date->getTimestamp()), $date->toString(      'y'));
        $this->assertSame(date('a',$date->getTimestamp()), strtolower($date->toString('a', 'en')));
        $this->assertSame(date('A',$date->getTimestamp()), strtoupper($date->toString('A', 'en')));
        $this->assertSame(date('B',$date->getTimestamp()), $date->toString(      'B'));
        $this->assertSame(date('g',$date->getTimestamp()), $date->toString(      'g'));
        $this->assertSame(date('G',$date->getTimestamp()), $date->toString(      'G'));
        $this->assertSame(date('h',$date->getTimestamp()), $date->toString(      'h'));
        $this->assertSame(date('H',$date->getTimestamp()), $date->toString(      'H'));
        $this->assertSame(date('i',$date->getTimestamp()), $date->toString(      'i'));
        $this->assertSame(date('s',$date->getTimestamp()), $date->toString(      's'));
        $this->assertSame(date('e',$date->getTimestamp()), $date->toString(      'e'));
        $this->assertSame(date('I',$date->getTimestamp()), $date->toString(      'I'));
        $this->assertSame(date('O',$date->getTimestamp()), $date->toString(      'O'));
        $this->assertSame(date('P',$date->getTimestamp()), $date->toString(      'P'));
        $this->assertSame(date('T',$date->getTimestamp()), $date->toString(      'T'));
        $this->assertSame(date('Z',$date->getTimestamp()), $date->toString(      'Z'));
        $this->assertSame(date('c',$date->getTimestamp()), $date->toString(      'c'));
        $this->assertSame(date('r',$date->getTimestamp()), $date->toString(      'r'));
        $this->assertSame(date('U',$date->getTimestamp()), $date->toString(      'U'));
        Zend_Date::setOptions(array('format_type' => 'iso'));
    }

    /**
     * @ZF-7912
     */
    public function testPhpFormatWithIsEmpty()
    {
        Zend_Date::setOptions(array('format_type' => 'php'));
        $date1 = new Zend_Date();
        $date2 = clone $date1;
        $date2->add(1, 'd');

        $this->assertTrue($date1->isEarlier($date2, 'd.M.y'));
        $this->assertFalse($date2->isEarlier($date1, 'd.M.y'));
        $this->assertFalse($date1->isLater($date2, 'd.M.y'));
        $this->assertTrue($date2->isLater($date1, 'd.M.y'));
    }

    public function testPhpFormatWithToString()
    {
        Zend_Date::setOptions(array('format_type' => 'php'));
        $date = new Zend_Date('10.10.2009 10:10:10');
        $this->assertEquals('10.10.2009 10:10:10', $date->toString("d.m.Y H:i:s"));
        $date->setTime("23:59:59");
        $this->assertEquals('10.10.2009 23:59:59', $date->toString("d.m.Y H:i:s"));
    }

    /**
     * @ZF-8650
     */
    public function testFractionalPrecision()
    {
        $date = new Zend_Date();
        $date->set('012345', Zend_Date::MILLISECOND);

        $this->assertEquals(3, $date->getFractionalPrecision());
        $this->assertEquals('345', $date->toString('S'));

        $date->setFractionalPrecision(6);
        $this->assertEquals(6, $date->getFractionalPrecision());
        $this->assertEquals('345000', $date->toString('S'));

        $date->add(200, Zend_Date::MILLISECOND);
        $this->assertEquals(6, $date->getFractionalPrecision());
        $this->assertEquals('345200', $date->toString('S'));
    }

    /**
     * @ZF-9085
     */
    public function testGettingMonthWhenUsingGNU()
    {
        Zend_Date::setOptions(array('format_type' => 'php'));
        $date = new Zend_Date(array('day' => 1, 'month' => 4, 'year' => 2008));
        $date2  = $date->getMonth();
        $result = $date2->toArray();
        $this->assertEquals(1970, $result['year']);
    }

    /**
     * @ZF-9891
     */
    public function testComparingDatesWithoutOption()
    {
        $date  = new Zend_Date(strtotime('Sat, 07 Mar 2009 08:03:50 +0000'));
        $date2 = new Zend_Date();
        $date2->set('Sat, 07 Mar 2009 08:03:50 +0000', Zend_Date::RFC_2822);

        $this->assertTrue($date2->equals($date));
    }

    /**
     * @ZF-10150
     */
    public function testChineseFullDates()
    {
        $date = new Zend_Date(array('year' => 2008, 'month' => 10, 'day' => 12));
        $this->assertEquals('2008年10月12日', $date->get(Zend_Date::DATE_LONG, 'zh'));
    }
    /**
     * @group ZF-10492
     */
    public function test_farFutureDate()
    {
        $t = '2041-08-01 00:00:00';
        $date = new Zend_Date($t, 'yyyy-MM-dd HH:mm:ss');
        $this->assertEquals($t, $date->toString('yyyy-MM-dd HH:mm:ss'));
    }

    /**
     * @group ZF-11846
     */
    public function testGetTimezoneFromStringForTimezoneOffsetsGreaterThan12()
    {
        $date = new Zend_Date();
        $this->assertEquals('Etc/GMT-13', $date->getTimezoneFromString('18:00:00+1300'));
        $this->assertEquals('Etc/GMT-14', $date->getTimezoneFromString('18:00:00+1400'));
    }

    /**
     * @group ZF-11992
     */
    public function testDateShouldMatchOnFirstDayOfYear()
    {
        $date = new Zend_Date('01.01.2012');
        $out  = $date->toString('Y-MM-dd');
        $this->assertEquals('2012-01-01', $out);
    }

    /**
     * @group GH-2
     */
    public function testGetTimezoneFromStringForTimezonesWithUnderscore()
    {
        $date = new Zend_Date();

        $this->assertEquals(
            'America/Los_Angeles',
            $date->getTimezoneFromString('America/Los_Angeles')
        );

        $this->assertEquals(
            'America/New_York',
            $date->getTimezoneFromString('America/New_York')
        );
    }

    /**
     * @group GH-561
     */
    public function testGetYearAndMonthWithoutDot()
    {
        $date = new Zend_Date('2014.12.29');

        $this->assertEquals('29.12.2014', $date->get(Zend_Date::DATE_MEDIUM));
        $this->assertEquals('2014.12', $date->get('Y.M'));
        $this->assertEquals('201412', $date->get('YM'));
    }
}

class Zend_Date_TestHelper extends Zend_Date
{
    public function _setTime($timestamp)
    {
        $this->_getTime($timestamp);
    }

    protected function _getTime($timestamp = null)
    {
        static $_timestamp = null;
        if ($timestamp !== null) {
            $_timestamp = $timestamp;
        }
        if ($_timestamp !== null) {
            return $_timestamp;
        }
        return time();
    }

    public function mktime($hour, $minute, $second, $month, $day, $year, $dst= -1, $gmt = false)
    {
        return parent::mktime($hour, $minute, $second, $month, $day, $year, $dst, $gmt);
    }
}
