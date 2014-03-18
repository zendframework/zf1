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
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Mail_Storage_Mbox
 */
require_once 'Zend/Mail/Storage/Mbox.php';

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';


/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mail
 */
class Zend_Mail_MboxTest extends PHPUnit_Framework_TestCase
{
    protected $_mboxOriginalFile;
    protected $_mboxFile;
    protected $_tmpdir;

    public function setUp()
    {
        if ($this->_tmpdir == null) {
            if (TESTS_ZEND_MAIL_TEMPDIR != null) {
                $this->_tmpdir = TESTS_ZEND_MAIL_TEMPDIR;
            } else {
                $this->_tmpdir = dirname(__FILE__) . '/_files/test.tmp/';
            }
            if (!file_exists($this->_tmpdir)) {
                mkdir($this->_tmpdir);
            }
            $count = 0;
            $dh = opendir($this->_tmpdir);
            while (readdir($dh) !== false) {
                ++$count;
            }
            closedir($dh);
            if ($count != 2) {
                $this->markTestSkipped('Are you sure your tmp dir is a valid empty dir?');
                return;
            }
        }

        $this->_mboxOriginalFile = dirname(__FILE__) . '/_files/test.mbox/INBOX';
        $this->_mboxFile = $this->_tmpdir . 'INBOX';

        copy($this->_mboxOriginalFile, $this->_mboxFile);
    }

    public function tearDown()
    {
        unlink($this->_mboxFile);
    }

    public function testLoadOk()
    {
        try {
            $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));
        } catch (Exception $e) {
            $this->fail('exception raised while loading mbox file');
        }
    }

    public function testLoadConfig()
    {
        try {
            $mail = new Zend_Mail_Storage_Mbox(new Zend_Config(array('filename' => $this->_mboxFile)));
        } catch (Exception $e) {
            $this->fail('exception raised while loading mbox folder');
        }
    }

    public function testNoParams()
    {
        try {
            $mail = new Zend_Mail_Storage_Mbox(array());
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised with empty params');
    }

    public function testLoadFailure()
    {
        try {
            $mail = new Zend_Mail_Storage_Mbox(array('filename' => 'ThisFileDoesNotExist'));
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while loading unknown file');
    }

    public function testLoadInvalid()
    {
        try {
            $mail = new Zend_Mail_Storage_Mbox(array('filename' => __FILE__));
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception while loading invalid file');
    }

    public function testClose()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        try {
            $mail->close();
        } catch (Exception $e) {
            $this->fail('exception raised while closing mbox file');
        }
    }

    public function testHasTop()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        $this->assertTrue($mail->hasTop);
    }

    public function testHasCreate()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        $this->assertFalse($mail->hasCreate);
    }

    public function testNoop()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        try {
            $mail->noop();
        } catch (Exception $e) {
            $this->fail('exception raised while doing nothing (noop)');
        }
    }

    public function testCount()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        $count = $mail->countMessages();
        $this->assertEquals(7, $count);
    }

    public function testSize()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));
        $shouldSizes = array(1 => 397, 89, 694, 452, 497, 101, 139);


        $sizes = $mail->getSize();
        $this->assertEquals($shouldSizes, $sizes);
    }

    public function testSingleSize()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        $size = $mail->getSize(2);
        $this->assertEquals(89, $size);
    }

    public function testFetchHeader()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

/*
    public function testFetchTopBody()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        $content = $mail->getHeader(3, 1)->getContent();
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }
*/

    public function testFetchMessageHeader()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testFetchMessageBody()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        $content = $mail->getMessage(3)->getContent();
        list($content, ) = explode("\n", $content, 2);
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }

    public function testFailedRemove()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        try {
            $mail->removeMessage(1);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while deleting message (mbox is read-only)');
    }

    public function testCapa()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));
        $capa = $mail->getCapabilities();
        $this->assertTrue(isset($capa['uniqueid']));
    }

    public function testValid()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        $this->assertFalse($mail->valid());
        $mail->rewind();
        $this->assertTrue($mail->valid());
    }


    public function testOutOfBounds()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        try {
            $mail->seek(INF);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while seeking to not invalid id');
    }

    public function testSleepWake()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        $count = $mail->countMessages();
        $content = $mail->getMessage(1)->getContent();

        $serialzed = serialize($mail);
        $mail = null;
        unlink($this->_mboxFile);
        // otherwise this test is to fast for a mtime change
        sleep(2);
        copy($this->_mboxOriginalFile, $this->_mboxFile);
        $mail = unserialize($serialzed);

        $this->assertEquals($mail->countMessages(), $count);
        $this->assertEquals($mail->getMessage(1)->getContent(), $content);
    }

    public function testSleepWakeRemoved()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        $count = $mail->countMessages();
        $content = $mail->getMessage(1)->getContent();

        $serialzed = serialize($mail);
        $mail = null;

        $stat = stat($this->_mboxFile);
        chmod($this->_mboxFile, 0);
        clearstatcache();
        $statcheck = stat($this->_mboxFile);
        if ($statcheck['mode'] % (8 * 8 * 8) !== 0) {
            chmod($this->_mboxFile, $stat['mode']);
            $this->markTestSkipped('cannot remove read rights, which makes this test useless (maybe you are using Windows?)');
            return;
        }



        $check = false;
        try {
            $mail = unserialize($serialzed);
        } catch (Exception $e) {
            $check = true;
            // test ok
        }

        chmod($this->_mboxFile, $stat['mode']);

        if (!$check) {
            if (function_exists('posix_getuid') && posix_getuid() === 0) {
                $this->markTestSkipped('seems like you are root and we therefore cannot test the error handling');
            }
            $this->fail('no exception while waking with non readable file');
         }
    }

    public function testUniqueId()
    {
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));

        $this->assertFalse($mail->hasUniqueId);
        $this->assertEquals(1, $mail->getNumberByUniqueId($mail->getUniqueId(1)));

        $ids = $mail->getUniqueId();
        foreach ($ids as $num => $id) {
            $this->assertEquals($num, $id);

            if ($mail->getNumberByUniqueId($id) != $num) {
                    $this->fail('reverse lookup failed');
            }
        }
    }

    public function testShortMbox()
    {
        $fh = fopen($this->_mboxFile, 'w');
        fputs($fh, "From \r\nSubject: test\r\nFrom \r\nSubject: test2\r\n");
        fclose($fh);
        $mail = new Zend_Mail_Storage_Mbox(array('filename' => $this->_mboxFile));
        $this->assertEquals($mail->countMessages(), 2);
        $this->assertEquals($mail->getMessage(1)->subject, 'test');
        $this->assertEquals($mail->getMessage(1)->getContent(), '');
        $this->assertEquals($mail->getMessage(2)->subject, 'test2');
        $this->assertEquals($mail->getMessage(2)->getContent(), '');
    }

}
