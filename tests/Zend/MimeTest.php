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
 * @package    Zend_Mime
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/** Zend_Mail */
require_once 'Zend/Mail.php';

/** Zend_Mime */
require_once 'Zend/Mime.php';

/**
 * @category   Zend
 * @package    Zend_Mime
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mime
 */
class Zend_MimeTest extends PHPUnit_Framework_TestCase
{
    public function testBoundary()
    {
        // check boundary for uniqueness
        $m1 = new Zend_Mime();
        $m2 = new Zend_Mime();
        $this->assertNotEquals($m1->boundary(), $m2->boundary());

        // check instantiating with arbitrary boundary string
        $myBoundary = 'mySpecificBoundary';
        $m3 = new Zend_Mime($myBoundary);
        $this->assertEquals($m3->boundary(), $myBoundary);

    }

    public function testIsPrintable_notPrintable()
    {
        $this->assertFalse(Zend_Mime::isPrintable('Test with special chars: �����'));
    }

    public function testIsPrintable_isPrintable()
    {
        $this->assertTrue(Zend_Mime::isPrintable('Test without special chars'));
    }

    public function testQP()
    {
        $text = "This is a cool Test Text with special chars: ����\n"
              . "and with multiple lines���� some of the Lines are long, long"
              . ", long, long, long, long, long, long, long, long, long, long"
              . ", long, long, long, long, long, long, long, long, long, long"
              . ", long, long, long, long, long, long, long, long, long, long"
              . ", long, long, long, long and with ����";

        $qp = Zend_Mime::encodeQuotedPrintable($text);
        $this->assertEquals(quoted_printable_decode($qp), $text);
    }

    /**
     * @group ZF-10074
     */
    public function testEncodeQuotedPrintableWhenTextHasZeroAtTheEnd()
    {
        $raw = str_repeat('x',72) . '0';
        $quoted = Zend_Mime::encodeQuotedPrintable($raw, 72);
        $expected = quoted_printable_decode($quoted);        
        $this->assertEquals($expected, $raw);
    }

    public function testBase64()
    {
        $content = str_repeat("\x88\xAA\xAF\xBF\x29\x88\xAA\xAF\xBF\x29\x88\xAA\xAF", 4);
        $encoded = Zend_Mime::encodeBase64($content);
        $this->assertEquals($content, base64_decode($encoded));
    }

    public function testZf1058WhitespaceAtEndOfBodyCausesInfiniteLoop()
    {
        $mail = new Zend_Mail();
        $mail->setSubject('my subject');
        $mail->setBodyText("my body\r\n\r\n...after two newlines\r\n ");
        $mail->setFrom('test@email.com');
        $mail->addTo('test@email.com');

        // test with generic transport
        require_once 'Mail/MailTest.php';
        $mock = new Zend_Mail_Transport_Sendmail_Mock();
        $mail->send($mock);
        $body = quoted_printable_decode($mock->body);
        $this->assertContains("my body\r\n\r\n...after two newlines", $body, $body);
    }

    /**
     * @group ZF-1688
     * @dataProvider dataTestEncodeMailHeaderQuotedPrintable
     */
    public function testEncodeMailHeaderQuotedPrintable($str, $charset, $result)
    {
        $this->assertEquals($result, Zend_Mime::encodeQuotedPrintableHeader($str, $charset));
    }

    public static function dataTestEncodeMailHeaderQuotedPrintable()
    {
        return array(
            array("äöü", "UTF-8", "=?UTF-8?Q?=C3=A4=C3=B6=C3=BC?="),
            array("äöü ", "UTF-8", "=?UTF-8?Q?=C3=A4=C3=B6=C3=BC?="),
            array("Gimme more €", "UTF-8", "=?UTF-8?Q?Gimme=20more=20=E2=82=AC?="),
            array("Alle meine Entchen schwimmen in dem See, schwimmen in dem See, Köpfchen in das Wasser, Schwänzchen in die Höh!", "UTF-8", "=?UTF-8?Q?Alle=20meine=20Entchen=20schwimmen=20in=20dem=20See=2C=20?=
 =?UTF-8?Q?schwimmen=20in=20dem=20See=2C=20K=C3=B6pfchen=20in=20das=20?=
 =?UTF-8?Q?Wasser=2C=20Schw=C3=A4nzchen=20in=20die=20H=C3=B6h!?="),
            array("ääääääääääääääääääääääääääääääääää", "UTF-8", "=?UTF-8?Q?=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4=C3=A4?="),
        );
    }

    /**
     * @group ZF-1688
     * @dataProvider dataTestEncodeMailHeaderBase64
     */
    public function testEncodeMailHeaderBase64($str, $charset, $result)
    {
        $this->assertEquals($result, Zend_Mime::encodeBase64Header($str, $charset));
    }

    public static function dataTestEncodeMailHeaderBase64()
    {
        return array(
            array("äöü", "UTF-8", "=?UTF-8?B?w6TDtsO8?="),
            array("Alle meine Entchen schwimmen in dem See, schwimmen in dem See, Köpfchen in das Wasser, Schwänzchen in die Höh!", "UTF-8", "=?UTF-8?B?QWxsZSBtZWluZSBFbnRjaGVuIHNjaHdpbW1lbiBpbiBkZW0gU2VlLCBzY2h3?=
 =?UTF-8?B?aW1tZW4gaW4gZGVtIFNlZSwgS8O2cGZjaGVuIGluIGRhcyBXYXNzZXIsIFNj?=
 =?UTF-8?B?aHfDpG56Y2hlbiBpbiBkaWUgSMO2aCE=?="),
        );
    }

    /**
     * @group ZF-1688
     */
    public function testLineLengthInQuotedPrintableHeaderEncoding()
    {
        $subject = "Alle meine Entchen schwimmen in dem See, schwimmen in dem See, Köpfchen in das Wasser, Schwänzchen in die Höh!";
        $encoded = Zend_Mime::encodeQuotedPrintableHeader($subject, "UTF-8", 100);
        foreach(explode(Zend_Mime::LINEEND, $encoded) AS $line ) {
            if(strlen($line) > 100) {
                $this->fail("Line '".$line."' is ".strlen($line)." chars long, only 100 allowed.");
            }
        }
        $encoded = Zend_Mime::encodeQuotedPrintableHeader($subject, "UTF-8", 40);
        foreach(explode(Zend_Mime::LINEEND, $encoded) AS $line ) {
            if(strlen($line) > 40) {
                $this->fail("Line '".$line."' is ".strlen($line)." chars long, only 40 allowed.");
            }
        }
    }
}
