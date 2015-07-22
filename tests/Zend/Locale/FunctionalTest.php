<?php

class Zend_Locale_FunctionalTest extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
        Zend_Locale::disableCache(true);
    }

    function tearDown()
    {
        Zend_Locale::disableCache(false);
    }

    function testlocale_fr_FR()
    {
        $locale = 'fr_FR';
        $myDate = $this->dateShortFormatInLocale($locale);

        $this->assertEquals('05/04/2015', $myDate);
        $this->_testDateFormatParsing($myDate, $locale);

        $currency = new Zend_Currency($locale);
        $this->assertSame('1 234,56 €', $currency->toCurrency(1234.56));

        $date = $this->dateInLocale($locale);
        $this->_testDaysAndMonthTranslations($date, 'dimanche', 'dim', 'd',
            'avril', 'avr.');
    }

    function dateShortFormatInLocale($locale)
    {
        $date = $this->dateInLocale($locale);
        return $date->get(Zend_Date::DATE_SHORT);
    }

    function dateInLocale($locale)
    {
        return new Zend_Date(gmmktime(0, 0, 0, 4, 5, 2015), null, $locale);
    }

    private function _testDateFormatParsing($otherDate, $locale)
    {
        $date = new Zend_Date($otherDate, null, $locale);
        $this->assertEquals($date->get(Zend_Date::DATE_SHORT), $otherDate,
            'format parsing');
    }


    private function _testDaysAndMonthTranslations($date, $weekday, $weekdayShort,
        $weekDayNarrow, $monthName, $monthNameShort)
    {
        $this->assertEquals($weekday, $date->get(Zend_Date::WEEKDAY));
        $this->assertEquals($weekdayShort,
            $date->get(Zend_Date::WEEKDAY_SHORT));
        $this->assertEquals($weekDayNarrow,
            $date->get(Zend_Date::WEEKDAY_NARROW));
        $this->assertEquals($monthName,
            $date->get(Zend_Date::MONTH_NAME));
        $this->assertEquals($monthNameShort,
            $date->get(Zend_Date::MONTH_NAME_SHORT));
    }
}
