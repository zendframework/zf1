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

    function localeFormats()
    {
        return [
['fr_FR', '05/04/2015', '1 234,56 €', 'dimanche', 'dim', 'd', 'avril', 'avr.'],
['de_DE', '05.04.2015', '1.234,56 €', 'Sonntag', 'Son', 'S', 'April', 'Apr.'],
['el_GR', '05/04/2015', '1.234,56 €', 'Κυριακή', 'Κυρ', 'Κ', 'Απριλίου', 'Απρ'],
['pl_PL', '05-04-2015', '1 234,56 PLN', 'niedziela', 'nie', 'n', 'kwietnia', 'kwi'],
['en_GB', '05/04/2015', '£1,234.56', 'Sunday', 'Sun', 'S', 'April', 'Apr'],
['en_IE', '05/04/2015', '€1,234.56', 'Sunday', 'Sun', 'S', 'April', 'Apr'],
['ro_RO', '05.04.2015', '1.234,56 €', 'duminică', 'dum', 'D', 'aprilie', 'apr.'],
['it_IT', '05/04/2015', '€ 1.234,56', 'domenica', 'dom', 'd', 'aprile', 'apr'],
['ru_RU', '05/04/2015', '1 234,56 руб', 'воскресенье', 'вос', 'в', 'апреля', 'апр.'],
// ['tr_TR', '05.04.2015', '1.234,56 TL', 'Pazar', 'Paz', 'P', 'Nisan', 'Nis'],
// ['uk_UA', '05/04/2015', '1 234,56 грн', 'неділя', 'нед', 'Н', 'квітня', 'квіт.'],
// ['sr_RS', '5.4.2015.', 'RSD 1.234,56', 'nedelja', 'ned', 'n', 'april', 'apr'],
// ['hu_HU', '2015.04.05.', '1 234 Ft', 'vasárnap', 'vas', 'V', 'április', 'ápr.'],
// ['cs_CZ', '05/04/2015', '1 234 Kč', 'neděle', 'ned', 'n', 'dubna', 'dub'],
// ['bg_BG', '05/04/2015', '1 234,56 €', 'неделя', 'нед', 'н', 'април', 'апр.'],
// ['he_IL', '05/04/2015', '1,234.56 ₪', 'יום ראשון', 'יום', 'י', 'אפריל', 'אפר׳'],
// ['sq_AL', '2015-04-05', '€1.234,56', 'e diel', 'e d', 'D', 'prill', 'Pri'],
// ['es_ES', '05/04/2015', '1.234,56 €', 'domingo', 'dom', 'd', 'abril', 'abr.'],
// ['pt_PT', '05/04/2015', '1 234,56 €', 'domingo', 'dom', 'd', 'Abril', 'Abr'],
// ['en_US', '04/05/2015', '$1,234.56', 'Sunday', 'Sun', 'S', 'April', 'Apr'],
// ['en_NZ', '05/04/2015', 'NZ$1,234.56', 'Sunday', 'Sun', 'S', 'April', 'Apr'],
// ['en_ZA', '05/04/2015', 'R1 234,56', 'Sunday', 'Sun', 'S', 'April', 'Apr'],
// ['en_TP', '05/04/2015', '€1,234.56', 'Sunday', 'Sun', 'S', 'April', 'Apr'],
// ['pt_BR', '05/04/2015', 'R$1.234,56', 'domingo', 'dom', 'd', 'abril', 'abr'],
// ['da_DK', '05/04/2015', '1.234,56 DKK', 'søndag', 'søn', 's', 'april', 'apr.'],
// ['fi_FI', '05/04/2015', '1 234,56 €', 'sunnuntaina', 'sun', 's', 'huhtikuuta', 'huhtikuuta'],
// ['nb_NO', '05/04/2015', 'NOK 1 234,56', 'søndag', 'søn', 's', 'april', 'apr.'],
// ['sv_SE', '05/04/2015', '1 234,56 SEK', 'söndag', 'sön', 's', 'april', 'apr'],
// ['ar_AE', '05/04/2015', 'AED 1.234,56', 'Sunday', 'Sun', 'S', 'April', 'Apr'],
// ['en_IN', '05/04/2015', 'INR 1,234.56', 'Sunday', 'Sun', 'S', 'April', 'Apr'],
// ['en_HK', '05/04/2015', 'HKD1,234.56', '星期日', '星期日', '周', '四月', '4月'],
// ['en_SG', '05/04/2015', 'SGD1,234.56', 'Sunday', 'Sun', 'S', 'April', 'Apr'],
// ['en_PH', '05/04/2015', 'PHP1,234.56', 'Linggo', 'Lin', 'L', 'Abril', 'Abr'],
// ['ko_KR', '05/04/2015', 'KRW1,234', '일요일', '일요일', '일', '4월', '4월'],
// ['ja_JP', '05/04/2015', 'JPY1,234', '日曜日', '日曜日', '日', '4月', '4月'],
// ['zh_CN', '05/04/2015', 'CNY1,234.56', '星期日', '星期日', '周', '四月', '4月'],
// ['id_ID', '05/04/2015', 'Rp1.234', 'Minggu', 'Min', 'M', 'April', 'Apr'],
// ['zh_TW', '05/04/2015', 'NT$1,234.56', '星期日', '星期日', '週', '4月', '4月'],
// ['es_MX', '05/04/2015', '1,234.56 $', 'domingo', 'dom', 'd', 'abril', 'abr.'],
// ['es_AR', '05/04/2015', '1.234,56 $', 'domingo', 'dom', 'd', 'abril', 'abr.'],
// ['es_CL', '05/04/2015', '$1.234,56', 'domingo', 'dom', 'd', 'abril', 'abr.'],
// ['es_CO', '5/04/2015', '1.234,56 $', 'domingo', 'dom', 'd', 'abril', 'abr.'],
// ['es_CR', '05/04/2015', '1.234,56 ₡', 'domingo', 'dom', 'd', 'abril', 'abr.'],
// ['es_VE', '05/04/2015', 'Bs1.234,56', 'domingo', 'dom', 'd', 'abril', 'abr.'],
// ['es_BO', '05/04/2015', '1.234,56 $b', 'domingo', 'dom', 'd', 'abril', 'abr.'],
// ['es_EC', '05/04/2015', '$1.234,56', 'domingo', 'dom', 'd', 'abril', 'abr.'],
// ['es_SV', '05/04/2015', '1,234.56 $', 'domingo', 'dom', 'd', 'abril', 'abr.'],
// ['es_GT', '5/04/2015', '1,234.56 Q', 'domingo', 'dom', 'd', 'abril', 'abr.'],
// ['es_HN', '05/04/2015', '1,234.56 L', 'domingo', 'dom', 'd', 'abril', 'abr.'],
// ['es_NI', '05/04/2015', '1,234.56 C$', 'domingo', 'dom', 'd', 'abril', 'abr.'],
// ['es_PA', '05/04/2015', '1,234.56 $', 'domingo', 'dom', 'd', 'abril', 'abr.'],
// ['en_AU', '05/04/2015', '$1,234.56', 'Sunday', 'Sun', 'S', 'April', 'Apr'],
// ['en_CA', '05/04/2015', '$1,234.56', 'Sunday', 'Sun', 'S', 'April', 'Apr'],
// ['nl_NL', '05/04/2015', '€ 1.234,56', 'zondag', 'zon', 'z', 'april', 'apr.'],
// ['et_EE', '4/05/2015', '1 234,56 €', 'pühapäev', 'püh', 'P', 'aprill', 'apr'],
// ['ms_MY', '4/5/2015', 'RM1,234.56', 'Ahad', 'Aha', 'A', 'April', 'Apr'],
// ['ar_QA', '05-Apr-2015', 'ر.ق. 1.234', 'Sunday', 'Sun', 'S', 'April', 'Apr'],
// ['vi_VN', '05/04/2015', '1.234,560 đ', 'Chủ Nhật', 'Chủ', 'C', 'tháng 4', 'thg 4'],
// ['lt_LT', '05/04/2015', '€ 1.234,56', 'sekmadienis', 'sek', 's', 'balandis', 'bal.'],
// ['lv_LV', '05/04/2015', '€ 1.234,56', 'svētdiena', 'svē', 'S', 'aprīlis', 'apr.'],
// ['be_BY', '05/04/2015', '1.234,56 €', '','','','',''],
// ['sl_SI', '05/04/2015', '€ 1.234,56', '','','','','']
        ];
    }

    /**
     * @dataProvider localeFormats
     */
    function testlocale($locale, $shortDate, $amountText, $weekday,
        $weekdayShort, $weekDayNarrow, $monthName, $monthNameShort)
    {
        $myDate = $this->dateShortFormatInLocale($locale);

        $this->assertEquals($shortDate, $myDate);
        $this->_testDateFormatParsing($myDate, $locale);

        $currency = new Zend_Currency($locale);
        $this->assertSame($amountText, $currency->toCurrency(1234.56));

        $date = $this->dateInLocale($locale);
        $this->_testDaysAndMonthTranslations($date, $weekday, $weekdayShort,
            $weekDayNarrow, $monthName, $monthNameShort);

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
