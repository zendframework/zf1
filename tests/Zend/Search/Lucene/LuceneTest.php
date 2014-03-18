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

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Search_Lucene_LuceneTest::main');
}

/**
 * Zend_Search_Lucene
 */
require_once 'Zend/Search/Lucene.php';

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class Zend_Search_Lucene_LuceneTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    private function _clearDirectory($dirName)
    {
        if (!file_exists($dirName) || !is_dir($dirName))  {
            return;
        }

        // remove files from temporary direcytory
        $dir = opendir($dirName);
        while (($file = readdir($dir)) !== false) {
            if (!is_dir($dirName . '/' . $file)) {
                @unlink($dirName . '/' . $file);
            }
        }
        closedir($dir);
    }

    public function setUp()
    {
        $this->_clearDirectory(dirname(__FILE__) . '/_index/_files');
    }

    public function testCreate()
    {
        $index = Zend_Search_Lucene::create(dirname(__FILE__) . '/_index/_files');
        $this->assertTrue($index instanceof Zend_Search_Lucene_Interface);
        unset($index);

        $this->_clearDirectory(dirname(__FILE__) . '/_index/_files');
    }

    public function testOpen()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $this->assertTrue($index instanceof Zend_Search_Lucene_Interface);
    }

    public function testOpenNonCompound()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_nonCompoundIndexFiles');

        $this->assertTrue($index instanceof Zend_Search_Lucene_Interface);
    }

    public function testDefaultSearchField()
    {
        $currentDefaultSearchField = Zend_Search_Lucene::getDefaultSearchField();
        $this->assertEquals($currentDefaultSearchField, null);

        Zend_Search_Lucene::setDefaultSearchField('anotherField');
        $this->assertEquals(Zend_Search_Lucene::getDefaultSearchField(), 'anotherField');

        Zend_Search_Lucene::setDefaultSearchField($currentDefaultSearchField);
    }

    public function testCount()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $this->assertEquals($index->count(), 10);
    }

    public function testMaxDoc()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $this->assertEquals($index->maxDoc(), 10);
    }

    public function testNumDocs()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $this->assertEquals($index->numDocs(), 9);
    }

    public function testIsDeleted()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $this->assertFalse($index->isDeleted(3));
        $this->assertTrue($index->isDeleted(6));
    }

    public function testMaxBufferedDocs()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $currentMaxBufferedDocs = $index->getMaxBufferedDocs();

        $index->setMaxBufferedDocs(234);
        $this->assertEquals($index->getMaxBufferedDocs(), 234);

        $index->setMaxBufferedDocs($currentMaxBufferedDocs);
    }

    public function testMaxMergeDocs()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $currentMaxMergeDocs = $index->getMaxMergeDocs();

        $index->setMaxMergeDocs(34);
        $this->assertEquals($index->getMaxMergeDocs(), 34);

        $index->setMaxMergeDocs($currentMaxMergeDocs);
    }

    public function testMergeFactor()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $currentMergeFactor = $index->getMergeFactor();

        $index->setMergeFactor(113);
        $this->assertEquals($index->getMergeFactor(), 113);

        $index->setMergeFactor($currentMergeFactor);
    }

    public function testFind()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $hits = $index->find('submitting');
        $this->assertEquals(count($hits), 3);
    }

    public function testGetFieldNames()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $this->assertTrue(array_values($index->getFieldNames()) == array('path', 'modified', 'contents'));
    }

    public function testGetDocument()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $doc = $index->getDocument(3);

        $this->assertTrue($doc instanceof Zend_Search_Lucene_Document);
        $this->assertEquals($doc->path, 'IndexSource/about-pear.html');
    }

    public function testHasTerm()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $this->assertTrue($index->hasTerm(new Zend_Search_Lucene_Index_Term('packages', 'contents')));
        $this->assertFalse($index->hasTerm(new Zend_Search_Lucene_Index_Term('nonusedword', 'contents')));
    }

    public function testTermDocs()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $this->assertTrue(array_values( $index->termDocs(new Zend_Search_Lucene_Index_Term('packages', 'contents')) ) ==
                          array(0, 2, 6, 7, 8));
    }

    public function testTermPositions()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $this->assertTrue($index->termPositions(new Zend_Search_Lucene_Index_Term('packages', 'contents')) ==
                          array(0 => array(174),
                                2 => array(40, 742),
                                6 => array(6, 156, 163),
                                7 => array(194),
                                8 => array(55, 190, 405)));
    }

    public function testDocFreq()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $this->assertEquals($index->docFreq(new Zend_Search_Lucene_Index_Term('packages', 'contents')), 5);
    }

    public function testGetSimilarity()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $this->assertTrue($index->getSimilarity() instanceof Zend_Search_Lucene_Search_Similarity);
    }

    public function testNorm()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $this->assertTrue(abs($index->norm(3, 'contents') - 0.054688) < 0.000001);
    }

    public function testHasDeletions()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $this->assertTrue($index->hasDeletions());
    }

    public function testDelete()
    {
        // Copy index sample into _files directory
        $sampleIndexDir = dirname(__FILE__) . '/_indexSample/_files';
        $tempIndexDir = dirname(__FILE__) . '/_files';
        if (!is_dir($tempIndexDir)) {
            mkdir($tempIndexDir);
        }

        $this->_clearDirectory($tempIndexDir);

        $indexDir = opendir($sampleIndexDir);
        while (($file = readdir($indexDir)) !== false) {
            if (!is_dir($sampleIndexDir . '/' . $file)) {
                copy($sampleIndexDir . '/' . $file, $tempIndexDir . '/' . $file);
            }
        }
        closedir($indexDir);


        $index = Zend_Search_Lucene::open($tempIndexDir);

        $this->assertFalse($index->isDeleted(2));
        $index->delete(2);
        $this->assertTrue($index->isDeleted(2));

        $index->commit();

        unset($index);

        $index1 = Zend_Search_Lucene::open($tempIndexDir);
        $this->assertTrue($index1->isDeleted(2));
        unset($index1);

        $this->_clearDirectory($tempIndexDir);
    }

    public function testAddDocument()
    {
        $index = Zend_Search_Lucene::create(dirname(__FILE__) . '/_index/_files');

        $indexSourceDir = dirname(__FILE__) . '/_indexSource/_files';
        $dir = opendir($indexSourceDir);
        while (($file = readdir($dir)) !== false) {
            if (is_dir($indexSourceDir . '/' . $file)) {
                continue;
            }
            if (strcasecmp(substr($file, strlen($file)-5), '.html') != 0) {
                continue;
            }

            // Create new Document from a file
            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::Text('path', 'IndexSource/' . $file));
            $doc->addField(Zend_Search_Lucene_Field::Keyword( 'modified', filemtime($indexSourceDir . '/' . $file) ));

            $f = fopen($indexSourceDir . '/' . $file,'rb');
            $byteCount = filesize($indexSourceDir . '/' . $file);

            $data = '';
            while ( $byteCount > 0 && ($nextBlock = fread($f, $byteCount)) != false ) {
                $data .= $nextBlock;
                $byteCount -= strlen($nextBlock);
            }
            fclose($f);

            $doc->addField(Zend_Search_Lucene_Field::Text('contents', $data, 'ISO-8859-1'));

            // Add document to the index
            $index->addDocument($doc);
        }
        closedir($dir);

        unset($index);

        $index1 = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index/_files');
        $this->assertTrue($index1 instanceof Zend_Search_Lucene_Interface);
        unset($index1);

        $this->_clearDirectory(dirname(__FILE__) . '/_index/_files');
    }

    public function testOptimize()
    {
        $index = Zend_Search_Lucene::create(dirname(__FILE__) . '/_index/_files');

        $index->setMaxBufferedDocs(2);

        $indexSourceDir = dirname(__FILE__) . '/_indexSource/_files';
        $dir = opendir($indexSourceDir);
        while (($file = readdir($dir)) !== false) {
            if (is_dir($indexSourceDir . '/' . $file)) {
                continue;
            }
            if (strcasecmp(substr($file, strlen($file)-5), '.html') != 0) {
                continue;
            }

            // Create new Document from a file
            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::Keyword('path', 'IndexSource/' . $file));
            $doc->addField(Zend_Search_Lucene_Field::Keyword( 'modified', filemtime($indexSourceDir . '/' . $file) ));

            $f = fopen($indexSourceDir . '/' . $file,'rb');
            $byteCount = filesize($indexSourceDir . '/' . $file);

            $data = '';
            while ( $byteCount > 0 && ($nextBlock = fread($f, $byteCount)) != false ) {
                $data .= $nextBlock;
                $byteCount -= strlen($nextBlock);
            }
            fclose($f);

            $doc->addField(Zend_Search_Lucene_Field::Text('contents', $data, 'ISO-8859-1'));

            // Add document to the index
            $index->addDocument($doc);
        }
        closedir($dir);
        unset($index);

        $index1 = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index/_files');
        $this->assertTrue($index1 instanceof Zend_Search_Lucene_Interface);
        $pathTerm = new Zend_Search_Lucene_Index_Term('IndexSource/contributing.html', 'path');
        $contributingDocs = $index1->termDocs($pathTerm);
        foreach ($contributingDocs as $id) {
            $index1->delete($id);
        }
        $index1->optimize();
        unset($index1);

        $index2 = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index/_files');
        $this->assertTrue($index2 instanceof Zend_Search_Lucene_Interface);

        $hits = $index2->find('submitting');
        $this->assertEquals(count($hits), 3);
        unset($index2);

        $this->_clearDirectory(dirname(__FILE__) . '/_index/_files');
    }

    public function testTerms()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $this->assertEquals(count($index->terms()), 607);
    }

    public function testTermsStreamInterface()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $terms = array();

        $index->resetTermsStream();
        while ($index->currentTerm() !== null) {
            $terms[] = $index->currentTerm();
            $index->nextTerm();
        }

        $this->assertEquals(count($terms), 607);
    }

    public function testTermsStreamInterfaceSkipTo()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $terms = array();

        $index->resetTermsStream();
        $index->skipTo(new Zend_Search_Lucene_Index_Term('one', 'contents'));

        while ($index->currentTerm() !== null) {
            $terms[] = $index->currentTerm();
            $index->nextTerm();
        }

        $this->assertEquals(count($terms), 244);
    }

    public function testTermsStreamInterfaceSkipToTermsRetrieving()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_indexSample/_files');

        $terms = array();

        $index->resetTermsStream();
        $index->skipTo(new Zend_Search_Lucene_Index_Term('one', 'contents'));

        $terms[] = $index->currentTerm();
        $terms[] = $index->nextTerm();
        $terms[] = $index->nextTerm();

        $index->closeTermsStream();

        $this->assertTrue($terms ==
                          array(new Zend_Search_Lucene_Index_Term('one', 'contents'),
                                new Zend_Search_Lucene_Index_Term('only', 'contents'),
                                new Zend_Search_Lucene_Index_Term('open', 'contents'),
                               ));
    }

    public function testTermsStreamInterfaceSkipToTermsRetrievingZeroTermsCase()
    {
        $index = Zend_Search_Lucene::create(dirname(__FILE__) . '/_index/_files');

        // Zero terms
        $doc = new Zend_Search_Lucene_Document();
        $doc->addField(Zend_Search_Lucene_Field::Text('contents', ''));
        $index->addDocument($doc);

        unset($index);


        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index/_files');

        $index->resetTermsStream();
        $index->skipTo(new Zend_Search_Lucene_Index_Term('term', 'contents'));

        $this->assertTrue($index->currentTerm() === null);

        $index->closeTermsStream();
        unset($index);

        $this->_clearDirectory(dirname(__FILE__) . '/_index/_files');
    }

    public function testTermsStreamInterfaceSkipToTermsRetrievingOneTermsCase()
    {
        $index = Zend_Search_Lucene::create(dirname(__FILE__) . '/_index/_files');

        // Zero terms
        $doc = new Zend_Search_Lucene_Document();
        $doc->addField(Zend_Search_Lucene_Field::Text('contents', 'someterm'));
        $index->addDocument($doc);

        unset($index);


        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index/_files');

        $index->resetTermsStream();
        $index->skipTo(new Zend_Search_Lucene_Index_Term('term', 'contents'));

        $this->assertTrue($index->currentTerm() === null);

        $index->closeTermsStream();
        unset($index);

        $this->_clearDirectory(dirname(__FILE__) . '/_index/_files');
    }

    public function testTermsStreamInterfaceSkipToTermsRetrievingTwoTermsCase()
    {
        $index = Zend_Search_Lucene::create(dirname(__FILE__) . '/_index/_files');

        // Zero terms
        $doc = new Zend_Search_Lucene_Document();
        $doc->addField(Zend_Search_Lucene_Field::Text('contents', 'someterm word'));
        $index->addDocument($doc);

        unset($index);


        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index/_files');

        $index->resetTermsStream();
        $index->skipTo(new Zend_Search_Lucene_Index_Term('term', 'contents'));

        $this->assertTrue($index->currentTerm() == new Zend_Search_Lucene_Index_Term('word', 'contents'));

        $index->closeTermsStream();
        unset($index);

        $this->_clearDirectory(dirname(__FILE__) . '/_index/_files');
    }

    /**
     * @group ZF-7518
     */
    public function testTermsStreamInterfaceSkipToMatchedTerm()
    {
        $index = Zend_Search_Lucene::create(dirname(__FILE__) . '/_index/_files');

        $doc = new Zend_Search_Lucene_Document();
        $doc->addField(Zend_Search_Lucene_Field::Keyword('test', 'f'));
        $index->addDocument($doc);

        unset($index);


        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index/_files');

        $hits = $index->find('test:[a TO t]');
        $this->assertEquals(1, count($hits));
        $this->assertEquals(0, reset($hits)->id);

        $hits = $index->find('test:f');
        $this->assertEquals(1, count($hits));
        $this->assertEquals(0, reset($hits)->id);

        $hits = $index->find('test:g');
        $this->assertEquals(0, count($hits));

        unset($index);

        $this->_clearDirectory(dirname(__FILE__) . '/_index/_files');
    }

    /**
     * @group ZF-9680
     */
    public function testIsDeletedWithoutExplicitCommit()
    {
        //$this->_clearDirectory(dirname(__FILE__) . '/_index/_files');

        $index = Zend_Search_Lucene::create(dirname(__FILE__) . '/_index/_files');

        $document = new Zend_Search_Lucene_Document;
        $document->addField(Zend_Search_Lucene_Field::Keyword('_id', 'myId'));
        $document->addField(Zend_Search_Lucene_Field::Keyword('bla', 'blubb'));
        $index->addDocument($document);

        $this->assertFalse($index->isDeleted(0));
        unset($index);

        $this->_clearDirectory(dirname(__FILE__) . '/_index/_files');
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Search_Lucene_LuceneTest::main') {
    Zend_Search_Lucene_LuceneTest::main();
}
