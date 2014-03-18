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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Filter_Compress_ZipTest::main');
}

/**
 * @see Zend_Filter_Compress_Zip
 */
require_once 'Zend/Filter/Compress/Zip.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_Compress_ZipTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Filter_Compress_ZipTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (!extension_loaded('zip')) {
            $this->markTestSkipped('This adapter needs the zip extension');
        }

        $files = array(
            dirname(__FILE__) . '/../_files/compressed.zip',
            dirname(__FILE__) . '/../_files/zipextracted.txt',
            dirname(__FILE__) . '/../_files/zip.tmp',
            dirname(__FILE__) . '/../_files/_compress/Compress/First/Second/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress/Compress/First/Second',
            dirname(__FILE__) . '/../_files/_compress/Compress/First/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress/Compress/First',
            dirname(__FILE__) . '/../_files/_compress/Compress/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress/Compress',
            dirname(__FILE__) . '/../_files/_compress/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress'
        );

        foreach($files as $file) {
            if (file_exists($file)) {
                if (is_dir($file)) {
                    rmdir($file);
                } else {
                    unlink($file);
                }
            }
        }

        if (!file_exists(dirname(__FILE__) . '/../_files/Compress/First/Second')) {
            mkdir(dirname(__FILE__) . '/../_files/Compress/First/Second', 0777, true);
            file_put_contents(dirname(__FILE__) . '/../_files/Compress/First/Second/zipextracted.txt', 'compress me');
            file_put_contents(dirname(__FILE__) . '/../_files/Compress/First/zipextracted.txt', 'compress me');
            file_put_contents(dirname(__FILE__) . '/../_files/Compress/zipextracted.txt', 'compress me');
        }
    }

    public function tearDown()
    {
        $files = array(
            dirname(__FILE__) . '/../_files/compressed.zip',
            dirname(__FILE__) . '/../_files/zipextracted.txt',
            dirname(__FILE__) . '/../_files/zip.tmp',
            dirname(__FILE__) . '/../_files/_compress/Compress/First/Second/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress/Compress/First/Second',
            dirname(__FILE__) . '/../_files/_compress/Compress/First/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress/Compress/First',
            dirname(__FILE__) . '/../_files/_compress/Compress/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress/Compress',
            dirname(__FILE__) . '/../_files/_compress/zipextracted.txt',
            dirname(__FILE__) . '/../_files/_compress'
        );

        foreach($files as $file) {
            if (file_exists($file)) {
                if (is_dir($file)) {
                    rmdir($file);
                } else {
                    unlink($file);
                }
            }
        }

        if (!file_exists(dirname(__FILE__) . '/../_files/Compress/First/Second')) {
            mkdir(dirname(__FILE__) . '/../_files/Compress/First/Second', 0777, true);
            file_put_contents(dirname(__FILE__) . '/../_files/Compress/First/Second/zipextracted.txt', 'compress me');
            file_put_contents(dirname(__FILE__) . '/../_files/Compress/First/zipextracted.txt', 'compress me');
            file_put_contents(dirname(__FILE__) . '/../_files/Compress/zipextracted.txt', 'compress me');
        }
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testBasicUsage()
    {
        $filter  = new Zend_Filter_Compress_Zip(
            array(
                'archive' => dirname(__FILE__) . '/../_files/compressed.zip',
                'target'  => dirname(__FILE__) . '/../_files/zipextracted.txt'
            )
        );

        $content = $filter->compress('compress me');
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $content = $filter->decompress($content);
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents(dirname(__FILE__) . '/../_files/zipextracted.txt');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Setting Options
     *
     * @return void
     */
    public function testZipGetSetOptions()
    {
        $filter = new Zend_Filter_Compress_Zip();
        $this->assertEquals(array('archive' => null, 'target' => null), $filter->getOptions());

        $this->assertEquals(null, $filter->getOptions('archive'));

        $this->assertNull($filter->getOptions('nooption'));
        $filter->setOptions(array('nooption' => 'foo'));
        $this->assertNull($filter->getOptions('nooption'));

        $filter->setOptions(array('archive' => 'temp.txt'));
        $this->assertEquals('temp.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Archive
     *
     * @return void
     */
    public function testZipGetSetArchive()
    {
        $filter = new Zend_Filter_Compress_Zip();
        $this->assertEquals(null, $filter->getArchive());
        $filter->setArchive('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getArchive());
        $this->assertEquals('Testfile.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Target
     *
     * @return void
     */
    public function testZipGetSetTarget()
    {
        $filter = new Zend_Filter_Compress_Zip();
        $this->assertNull($filter->getTarget());
        $filter->setTarget('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getTarget());
        $this->assertEquals('Testfile.txt', $filter->getOptions('target'));

        try {
            $filter->setTarget('/unknown/path/to/file.txt');
            $this->fails('Exception expected');
        } catch(Zend_Filter_Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }
    }

    /**
     * Compress to Archive
     *
     * @return void
     */
    public function testZipCompressFile()
    {
        $filter  = new Zend_Filter_Compress_Zip(
            array(
                'archive' => dirname(__FILE__) . '/../_files/compressed.zip',
                'target'  => dirname(__FILE__) . '/../_files/zipextracted.txt'
            )
        );
        file_put_contents(dirname(__FILE__) . '/../_files/zipextracted.txt', 'compress me');

        $content = $filter->compress(dirname(__FILE__) . '/../_files/zipextracted.txt');
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $content = $filter->decompress($content);
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'
                            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents(dirname(__FILE__) . '/../_files/zipextracted.txt');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testCompressNonExistingTargetFile()
    {
        $filter  = new Zend_Filter_Compress_Zip(
            array(
                'archive' => dirname(__FILE__) . '/../_files/compressed.zip',
                'target'  => dirname(__FILE__) . '/../_files'
            )
        );

        $content = $filter->compress('compress me');
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $content = $filter->decompress($content);
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents(dirname(__FILE__) . '/../_files/zip.tmp');
        $this->assertEquals('compress me', $content);
    }

    /**
     * Compress directory to Archive
     *
     * @return void
     */
    public function testZipCompressDirectory()
    {
        $filter  = new Zend_Filter_Compress_Zip(
            array(
                'archive' => dirname(__FILE__) . '/../_files/compressed.zip',
                'target'  => dirname(__FILE__) . '/../_files/_compress'
            )
        );
        $content = $filter->compress(dirname(__FILE__) . '/../_files/Compress');
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        mkdir(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . '_compress');
        $content = $filter->decompress($content);
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'
                            . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . '_compress'
                            . DIRECTORY_SEPARATOR, $content);

        $base = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
              . DIRECTORY_SEPARATOR . '_compress' . DIRECTORY_SEPARATOR . 'Compress' . DIRECTORY_SEPARATOR;
        $this->assertTrue(file_exists($base));
        $this->assertTrue(file_exists($base . 'zipextracted.txt'));
        $this->assertTrue(file_exists($base . 'First' . DIRECTORY_SEPARATOR . 'zipextracted.txt'));
        $this->assertTrue(file_exists($base . 'First' . DIRECTORY_SEPARATOR .
                          'Second' . DIRECTORY_SEPARATOR . 'zipextracted.txt'));
        $content = file_get_contents(dirname(__FILE__) . '/../_files/Compress/zipextracted.txt');
        $this->assertEquals('compress me', $content);
    }

    /**
     * testing toString
     *
     * @return void
     */
    public function testZipToString()
    {
        $filter = new Zend_Filter_Compress_Zip();
        $this->assertEquals('Zip', $filter->toString());
    }

    /**
     * @group
     * @expectedException Zend_Filter_Exception
     */
    public function testDecompressWillThrowExceptionWhenDecompressingWithNoTarget()
    {
        $filter  = new Zend_Filter_Compress_Zip(
            array(
                'archive' => dirname(__FILE__) . '/../_files/compressed.zip',
                'target'  => dirname(__FILE__) . '/../_files/_compress'
            )
        );

        $content = $filter->compress('compress me');
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
                            . DIRECTORY_SEPARATOR . 'compressed.zip', $content);

        $filter  = new Zend_Filter_Compress_Zip(
            array(
                'archive' => dirname(__FILE__) . '/../_files/compressed.zip'
            )
        );
        $content = $filter->decompress($content);
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR, $content);
        $content = file_get_contents(dirname(__FILE__) . '/../_files/zip.tmp');
        $this->assertEquals('compress me', $content);
    }

    /**
     * @group RS
     * @expectedException Zend_Filter_Exception
     */
    public function testDecompressWillThrowExceptionWhenDetectingUpwardDirectoryTraversal()
    {
        if (version_compare(PHP_VERSION, '5.2.8', '>=')) {
            $this->markTestSkipped('This test is to run on PHP less than 5.2.8');
            return;
        }

        $filter  = new Zend_Filter_Compress_Zip(
            array(
                'archive' => dirname(__FILE__) . '/../_files/compressed.zip',
                'target'  => dirname(__FILE__) . '/../_files/evil.zip'
                )
            );

        $filter->decompress(dirname(__FILE__) . '/../_files/evil.zip');
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Filter_Compress_ZipTest::main') {
    Zend_Filter_Compress_ZipTest::main();
}
