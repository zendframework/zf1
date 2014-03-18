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
 * Zend_Mail_Storage_Pop3
 */
require_once 'Zend/Mail/Storage/Pop3.php';

/**
 * Zend_Mail_Protocol_Pop3
 */
require_once 'Zend/Mail/Protocol/Pop3.php';

/**
 * Zend_Config
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
class Zend_Mail_Pop3Test extends PHPUnit_Framework_TestCase
{
    protected $_params;

    public function setUp()
    {
        $this->_params = array('host'     => TESTS_ZEND_MAIL_POP3_HOST,
                               'user'     => TESTS_ZEND_MAIL_POP3_USER,
                               'password' => TESTS_ZEND_MAIL_POP3_PASSWORD);

        if (defined('TESTS_ZEND_MAIL_SERVER_TESTDIR') && TESTS_ZEND_MAIL_SERVER_TESTDIR) {
            if (!file_exists(TESTS_ZEND_MAIL_SERVER_TESTDIR . DIRECTORY_SEPARATOR . 'inbox')
             && !file_exists(TESTS_ZEND_MAIL_SERVER_TESTDIR . DIRECTORY_SEPARATOR . 'INBOX')) {
                $this->markTestSkipped('There is no file name "inbox" or "INBOX" in '
                                       . TESTS_ZEND_MAIL_SERVER_TESTDIR . '. I won\'t use it for testing. '
                                       . 'This is you safety net. If you think it is the right directory just '
                                       . 'create an empty file named INBOX or remove/deactived this message.');
            }

            $this->_cleanDir(TESTS_ZEND_MAIL_SERVER_TESTDIR);
            $this->_copyDir(dirname(__FILE__) . '/_files/test.' . TESTS_ZEND_MAIL_SERVER_FORMAT,
                            TESTS_ZEND_MAIL_SERVER_TESTDIR);
        }
    }

    protected function _cleanDir($dir)
    {
        $dh = opendir($dir);
        while (($entry = readdir($dh)) !== false) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            $fullname = $dir . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($fullname)) {
                $this->_cleanDir($fullname);
                rmdir($fullname);
            } else {
                unlink($fullname);
            }
        }
        closedir($dh);
    }

    protected function _copyDir($dir, $dest)
    {
        $dh = opendir($dir);
        while (($entry = readdir($dh)) !== false) {
            if ($entry == '.' || $entry == '..' || $entry == '.svn') {
                continue;
            }
            $fullname = $dir  . DIRECTORY_SEPARATOR . $entry;
            $destname = $dest . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($fullname)) {
                mkdir($destname);
                $this->_copyDir($fullname, $destname);
            } else {
                copy($fullname, $destname);
            }
        }
        closedir($dh);
    }

    public function testConnectOk()
    {
        try {
            $mail = new Zend_Mail_Storage_Pop3($this->_params);
        } catch (Exception $e) {
            $this->fail('exception raised while loading connection to pop3 server');
        }
    }

    public function testConnectConfig()
    {
        try {
            $mail = new Zend_Mail_Storage_Pop3(new Zend_Config($this->_params));
        } catch (Exception $e) {
            $this->fail('exception raised while loading connection to pop3 server');
        }
    }


    public function testConnectFailure()
    {
        $this->_params['host'] = 'example.example';
        try {
            $mail = new Zend_Mail_Storage_Pop3($this->_params);
        } catch (Exception $e) {
            return; // test ok
        }

        // I can only hope noone installs a POP3 server there
        $this->fail('no exception raised while connecting to example.example');
    }

    public function testNoParams()
    {
        try {
            $mail = new Zend_Mail_Storage_Pop3(array());
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised with empty params');
    }

    public function testConnectSSL()
    {
        if (!TESTS_ZEND_MAIL_POP3_SSL) {
            return;
        }

        $this->_params['ssl'] = 'SSL';
        try {
            $mail = new Zend_Mail_Storage_Pop3($this->_params);
        } catch (Exception $e) {
            $this->fail('exception raised while loading connection to pop3 server with SSL');
        }
    }

    public function testConnectTLS()
    {
        if (!TESTS_ZEND_MAIL_POP3_TLS) {
            return;
        }

        $this->_params['ssl'] = 'TLS';
        try {
            $mail = new Zend_Mail_Storage_Pop3($this->_params);
        } catch (Exception $e) {
            $this->fail('exception raised while loading connection to pop3 server with TLS');
        }
    }

    public function testInvalidService()
    {
        $this->_params['port'] = TESTS_ZEND_MAIL_POP3_INVALID_PORT;

        try {
            $mail = new Zend_Mail_Storage_Pop3($this->_params);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception while connection to invalid port');
    }

    public function testWrongService()
    {
        $this->_params['port'] = TESTS_ZEND_MAIL_POP3_WRONG_PORT;

        try {
            $mail = new Zend_Mail_Storage_Pop3($this->_params);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception while connection to wrong port');
    }

    public function testClose()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);

        try {
            $mail->close();
        } catch (Exception $e) {
            $this->fail('exception raised while closing pop3 connection');
        }
    }

    public function testHasTop()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);

        $this->assertTrue($mail->hasTop);
    }

    public function testHasCreate()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);

        $this->assertFalse($mail->hasCreate);
    }

    public function testNoop()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);

        try {
            $mail->noop();
        } catch (Exception $e) {
            $this->fail('exception raised while doing nothing (noop)');
        }
    }

    public function testCount()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);

        $count = $mail->countMessages();
        $this->assertEquals(7, $count);
    }

    public function testSize()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);
        $shouldSizes = array(1 => 397, 89, 694, 452, 497, 101, 139);


        $sizes = $mail->getSize();
        $this->assertEquals($shouldSizes, $sizes);
    }

    public function testSingleSize()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);

        $size = $mail->getSize(2);
        $this->assertEquals(89, $size);
    }

    public function testFetchHeader()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

/*
    public function testFetchTopBody()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);

        $content = $mail->getHeader(3, 1)->getContent();
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }
*/

    public function testFetchMessageHeader()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testFetchMessageBody()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);

        $content = $mail->getMessage(3)->getContent();
        list($content, ) = explode("\n", $content, 2);
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }

/*
    public function testFailedRemove()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);

        try {
            $mail->removeMessage(1);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while deleting message (mbox is read-only)');
    }
*/

    public function testWithInstanceConstruction()
    {
        $protocol = new Zend_Mail_Protocol_Pop3($this->_params['host']);
        $mail = new Zend_Mail_Storage_Pop3($protocol);
        try {
            // because we did no login this has to throw an exception
            $mail->getMessage(1);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while fetching with wrong transport');
    }

    public function testRequestAfterClose()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);
        $mail->close();
        try {
            $mail->getMessage(1);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while requesting after closing connection');
    }

    public function testServerCapa()
    {
        $mail = new Zend_Mail_Protocol_Pop3($this->_params['host']);
        $this->assertTrue(is_array($mail->capa()));
    }

    public function testServerUidl()
    {
        $mail = new Zend_Mail_Protocol_Pop3($this->_params['host']);
        $mail->login($this->_params['user'], $this->_params['password']);

        $uids = $mail->uniqueid();
        $this->assertEquals(count($uids), 7);

        $this->assertEquals($uids[1], $mail->uniqueid(1));
    }

    public function testRawHeader()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);

        $this->assertTrue(strpos($mail->getRawHeader(1), "\r\nSubject: Simple Message\r\n") > 0);
    }

    public function testUniqueId()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);

        $this->assertTrue($mail->hasUniqueId);
        $this->assertEquals(1, $mail->getNumberByUniqueId($mail->getUniqueId(1)));

        $ids = $mail->getUniqueId();
        foreach ($ids as $num => $id) {
            foreach ($ids as $inner_num => $inner_id) {
                if ($num == $inner_num) {
                    continue;
                }
                if ($id == $inner_id) {
                    $this->fail('not all ids are unique');
                }
            }

            if ($mail->getNumberByUniqueId($id) != $num) {
                    $this->fail('reverse lookup failed');
            }
        }
    }

    public function testWrongUniqueId()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);
        try {
            $mail->getNumberByUniqueId('this_is_an_invalid_id');
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception while getting number for invalid id');
    }

    public function testReadAfterClose()
    {
        $protocol = new Zend_Mail_Protocol_Pop3($this->_params['host']);
        $protocol->logout();

        try {
            $protocol->readResponse();
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception while reading from closed socket');
    }

    public function testRemove()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);
        $count = $mail->countMessages();

        $mail->removeMessage(1);
        $this->assertEquals($mail->countMessages(), --$count);

        unset($mail[2]);
        $this->assertEquals($mail->countMessages(), --$count);
    }

    public function testDotMessage()
    {
        $mail = new Zend_Mail_Storage_Pop3($this->_params);
        $content = '';
        $content .= "Before the dot\r\n";
        $content .= ".\r\n";
        $content .= "is after the dot\r\n";
        $this->assertEquals($mail->getMessage(7)->getContent(), $content);
    }
}
