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
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Search_Lucene
 */
require_once 'Zend/Search/Lucene/Analysis/Analyzer.php';

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class Zend_Search_Lucene_AnalysisTest extends PHPUnit_Framework_TestCase
{
    public function testAnalyzer()
    {
        $currentAnalyzer = Zend_Search_Lucene_Analysis_Analyzer::getDefault();
        $this->assertTrue($currentAnalyzer instanceof Zend_Search_Lucene_Analysis_Analyzer);

        /** Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num */
        require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common/Utf8Num.php';

        $newAnalyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num();
        Zend_Search_Lucene_Analysis_Analyzer::setDefault($newAnalyzer);
        $this->assertTrue(Zend_Search_Lucene_Analysis_Analyzer::getDefault() === $newAnalyzer);

        // Set analyzer to the default value (used in other tests)
        Zend_Search_Lucene_Analysis_Analyzer::setDefault($currentAnalyzer);
    }

    public function testText()
    {
        /** Zend_Search_Lucene_Analysis_Analyzer_Common_Text */
        require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common/Text.php';

        $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Text();

        $tokenList = $analyzer->tokenize('Word1 Word2 anotherWord');

        $this->assertEquals(count($tokenList), 3);

        $this->assertEquals($tokenList[0]->getTermText(),         'Word');
        $this->assertEquals($tokenList[0]->getStartOffset(),       0);
        $this->assertEquals($tokenList[0]->getEndOffset(),         4);
        $this->assertEquals($tokenList[0]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[1]->getTermText(),         'Word');
        $this->assertEquals($tokenList[1]->getStartOffset(),       6);
        $this->assertEquals($tokenList[1]->getEndOffset(),         10);
        $this->assertEquals($tokenList[1]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[2]->getTermText(),         'anotherWord');
        $this->assertEquals($tokenList[2]->getStartOffset(),       12);
        $this->assertEquals($tokenList[2]->getEndOffset(),         23);
        $this->assertEquals($tokenList[2]->getPositionIncrement(), 1);
    }

    public function testTextCaseInsensitive()
    {
        /** Zend_Search_Lucene_Analysis_Analyzer_Common_Text_CaseInsensitive */
        require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common/Text/CaseInsensitive.php';

        $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Text_CaseInsensitive();

        $tokenList = $analyzer->tokenize('Word1 Word2 anotherWord');

        $this->assertEquals(count($tokenList), 3);

        $this->assertEquals($tokenList[0]->getTermText(),         'word');
        $this->assertEquals($tokenList[0]->getStartOffset(),       0);
        $this->assertEquals($tokenList[0]->getEndOffset(),         4);
        $this->assertEquals($tokenList[0]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[1]->getTermText(),         'word');
        $this->assertEquals($tokenList[1]->getStartOffset(),       6);
        $this->assertEquals($tokenList[1]->getEndOffset(),         10);
        $this->assertEquals($tokenList[1]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[2]->getTermText(),         'anotherword');
        $this->assertEquals($tokenList[2]->getStartOffset(),       12);
        $this->assertEquals($tokenList[2]->getEndOffset(),         23);
        $this->assertEquals($tokenList[2]->getPositionIncrement(), 1);
    }

    public function testTextNum()
    {
        /** Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum */
        require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common/TextNum.php';

        $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum();

        $tokenList = $analyzer->tokenize('Word1 Word2 anotherWord');

        $this->assertEquals(count($tokenList), 3);

        $this->assertEquals($tokenList[0]->getTermText(),         'Word1');
        $this->assertEquals($tokenList[0]->getStartOffset(),       0);
        $this->assertEquals($tokenList[0]->getEndOffset(),         5);
        $this->assertEquals($tokenList[0]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[1]->getTermText(),         'Word2');
        $this->assertEquals($tokenList[1]->getStartOffset(),       6);
        $this->assertEquals($tokenList[1]->getEndOffset(),         11);
        $this->assertEquals($tokenList[1]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[2]->getTermText(),         'anotherWord');
        $this->assertEquals($tokenList[2]->getStartOffset(),       12);
        $this->assertEquals($tokenList[2]->getEndOffset(),         23);
        $this->assertEquals($tokenList[2]->getPositionIncrement(), 1);
    }

    public function testTextNumCaseInsensitive()
    {
        /** Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive */
        require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common/TextNum/CaseInsensitive.php';

        $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive();

        $tokenList = $analyzer->tokenize('Word1 Word2 anotherWord');

        $this->assertEquals(count($tokenList), 3);

        $this->assertEquals($tokenList[0]->getTermText(),         'word1');
        $this->assertEquals($tokenList[0]->getStartOffset(),       0);
        $this->assertEquals($tokenList[0]->getEndOffset(),         5);
        $this->assertEquals($tokenList[0]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[1]->getTermText(),         'word2');
        $this->assertEquals($tokenList[1]->getStartOffset(),       6);
        $this->assertEquals($tokenList[1]->getEndOffset(),         11);
        $this->assertEquals($tokenList[1]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[2]->getTermText(),         'anotherword');
        $this->assertEquals($tokenList[2]->getStartOffset(),       12);
        $this->assertEquals($tokenList[2]->getEndOffset(),         23);
        $this->assertEquals($tokenList[2]->getPositionIncrement(), 1);
    }

    public function testUtf8()
    {
        if (@preg_match('/\pL/u', 'a') != 1) {
            // PCRE unicode support is turned off
            return;
        }

        /** Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8 */
        require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common/Utf8.php';

        $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8();

        // UTF-8 text with a cyrillic symbols
        $tokenList = $analyzer->tokenize('Слово1 Слово2 ДругоеСлово', 'UTF-8');

        $this->assertEquals(count($tokenList), 3);

        $this->assertEquals($tokenList[0]->getTermText(),         'Слово');
        $this->assertEquals($tokenList[0]->getStartOffset(),       0);
        $this->assertEquals($tokenList[0]->getEndOffset(),         5);
        $this->assertEquals($tokenList[0]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[1]->getTermText(),         'Слово');
        $this->assertEquals($tokenList[1]->getStartOffset(),       7);
        $this->assertEquals($tokenList[1]->getEndOffset(),         12);
        $this->assertEquals($tokenList[1]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[2]->getTermText(),         'ДругоеСлово');
        $this->assertEquals($tokenList[2]->getStartOffset(),       14);
        $this->assertEquals($tokenList[2]->getEndOffset(),         25);
        $this->assertEquals($tokenList[2]->getPositionIncrement(), 1);
    }

    public function testUtf8Num()
    {
        if (@preg_match('/\pL/u', 'a') != 1) {
            // PCRE unicode support is turned off
            return;
        }

        /** Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num */
        require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common/Utf8Num.php';

        $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num();

        // UTF-8 text with a cyrillic symbols
        $tokenList = $analyzer->tokenize('Слово1 Слово2 ДругоеСлово', 'UTF-8');

        $this->assertEquals(count($tokenList), 3);

        $this->assertEquals($tokenList[0]->getTermText(),         'Слово1');
        $this->assertEquals($tokenList[0]->getStartOffset(),       0);
        $this->assertEquals($tokenList[0]->getEndOffset(),         6);
        $this->assertEquals($tokenList[0]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[1]->getTermText(),         'Слово2');
        $this->assertEquals($tokenList[1]->getStartOffset(),       7);
        $this->assertEquals($tokenList[1]->getEndOffset(),         13);
        $this->assertEquals($tokenList[1]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[2]->getTermText(),         'ДругоеСлово');
        $this->assertEquals($tokenList[2]->getStartOffset(),       14);
        $this->assertEquals($tokenList[2]->getEndOffset(),         25);
        $this->assertEquals($tokenList[2]->getPositionIncrement(), 1);
    }

    public function testUtf8CaseInsensitive()
    {
        if (@preg_match('/\pL/u', 'a') != 1) {
            // PCRE unicode support is turned off
            return;
        }
        if (!function_exists('mb_strtolower')) {
            // mbstring extension is disabled
            return;
        }

        /** Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive */
        require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common/Utf8/CaseInsensitive.php';

        $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive();

        // UTF-8 text with a cyrillic symbols
        $tokenList = $analyzer->tokenize('Слово1 Слово2 ДругоеСлово', 'UTF-8');

        $this->assertEquals(count($tokenList), 3);

        $this->assertEquals($tokenList[0]->getTermText(),         'слово');
        $this->assertEquals($tokenList[0]->getStartOffset(),       0);
        $this->assertEquals($tokenList[0]->getEndOffset(),         5);
        $this->assertEquals($tokenList[0]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[1]->getTermText(),         'слово');
        $this->assertEquals($tokenList[1]->getStartOffset(),       7);
        $this->assertEquals($tokenList[1]->getEndOffset(),         12);
        $this->assertEquals($tokenList[1]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[2]->getTermText(),         'другоеслово');
        $this->assertEquals($tokenList[2]->getStartOffset(),       14);
        $this->assertEquals($tokenList[2]->getEndOffset(),         25);
        $this->assertEquals($tokenList[2]->getPositionIncrement(), 1);
    }

    public function testUtf8NumCaseInsensitive()
    {
        if (@preg_match('/\pL/u', 'a') != 1) {
            // PCRE unicode support is turned off
            return;
        }
        if (!function_exists('mb_strtolower')) {
            // mbstring extension is disabled
            return;
        }

        /** Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive */
        require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common/Utf8Num/CaseInsensitive.php';
        $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive();

        // UTF-8 text with a cyrillic symbols
        $tokenList = $analyzer->tokenize('Слово1 Слово2 ДругоеСлово', 'UTF-8');

        $this->assertEquals(count($tokenList), 3);

        $this->assertEquals($tokenList[0]->getTermText(),         'слово1');
        $this->assertEquals($tokenList[0]->getStartOffset(),       0);
        $this->assertEquals($tokenList[0]->getEndOffset(),         6);
        $this->assertEquals($tokenList[0]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[1]->getTermText(),         'слово2');
        $this->assertEquals($tokenList[1]->getStartOffset(),       7);
        $this->assertEquals($tokenList[1]->getEndOffset(),         13);
        $this->assertEquals($tokenList[1]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[2]->getTermText(),         'другоеслово');
        $this->assertEquals($tokenList[2]->getStartOffset(),       14);
        $this->assertEquals($tokenList[2]->getEndOffset(),         25);
        $this->assertEquals($tokenList[2]->getPositionIncrement(), 1);
    }

    public function testEncoding()
    {
        if (PHP_OS == 'AIX') {
            $this->markTestSkipped('Test not available on AIX');
        }

        /** Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8 */
        require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common/Utf8.php';

        $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8();

        // UTF-8 text with a cyrillic symbols
        $tokenList = $analyzer->tokenize(iconv('UTF-8', 'Windows-1251', 'Слово1 Слово2 ДругоеСлово'), 'Windows-1251');

        $this->assertEquals(count($tokenList), 3);

        $this->assertEquals($tokenList[0]->getTermText(),         'Слово');
        $this->assertEquals($tokenList[0]->getStartOffset(),       0);
        $this->assertEquals($tokenList[0]->getEndOffset(),         5);
        $this->assertEquals($tokenList[0]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[1]->getTermText(),         'Слово');
        $this->assertEquals($tokenList[1]->getStartOffset(),       7);
        $this->assertEquals($tokenList[1]->getEndOffset(),         12);
        $this->assertEquals($tokenList[1]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[2]->getTermText(),         'ДругоеСлово');
        $this->assertEquals($tokenList[2]->getStartOffset(),       14);
        $this->assertEquals($tokenList[2]->getEndOffset(),         25);
        $this->assertEquals($tokenList[2]->getPositionIncrement(), 1);
    }

    public function testStopWords()
    {
        /** Zend_Search_Lucene_Analysis_Analyzer_Common_Text_CaseInsensitive */
        require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common/Text/CaseInsensitive.php';

        /** Zend_Search_Lucene_Analysis_TokenFilter_StopWords */
        require_once 'Zend/Search/Lucene/Analysis/TokenFilter/StopWords.php';

        $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Text_CaseInsensitive();
        $stopWordsFilter = new Zend_Search_Lucene_Analysis_TokenFilter_StopWords(array('word', 'and', 'or'));

        $analyzer->addFilter($stopWordsFilter);

        $tokenList = $analyzer->tokenize('Word1 Word2 anotherWord');

        $this->assertEquals(count($tokenList), 1);

        $this->assertEquals($tokenList[0]->getTermText(),         'anotherword');
        $this->assertEquals($tokenList[0]->getStartOffset(),       12);
        $this->assertEquals($tokenList[0]->getEndOffset(),         23);
        $this->assertEquals($tokenList[0]->getPositionIncrement(), 1);
    }

    public function testShortWords()
    {
        /** Zend_Search_Lucene_Analysis_Analyzer_Common_Text_CaseInsensitive */
        require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common/Text/CaseInsensitive.php';

        /** Zend_Search_Lucene_Analysis_TokenFilter_ShortWords */
        require_once 'Zend/Search/Lucene/Analysis/TokenFilter/ShortWords.php';

        $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Text_CaseInsensitive();
        $stopWordsFilter = new Zend_Search_Lucene_Analysis_TokenFilter_ShortWords(4 /* Minimal length */);

        $analyzer->addFilter($stopWordsFilter);

        $tokenList = $analyzer->tokenize('Word1 and anotherWord');

        $this->assertEquals(count($tokenList), 2);

        $this->assertEquals($tokenList[0]->getTermText(),         'word');
        $this->assertEquals($tokenList[0]->getStartOffset(),       0);
        $this->assertEquals($tokenList[0]->getEndOffset(),         4);
        $this->assertEquals($tokenList[0]->getPositionIncrement(), 1);

        $this->assertEquals($tokenList[1]->getTermText(),         'anotherword');
        $this->assertEquals($tokenList[1]->getStartOffset(),       10);
        $this->assertEquals($tokenList[1]->getEndOffset(),         21);
        $this->assertEquals($tokenList[1]->getPositionIncrement(), 1);
    }
}
