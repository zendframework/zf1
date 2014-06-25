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
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
/**
 * @see Zend_Pdf
 */
require_once 'Zend/Pdf.php';
/**
 * @see Zend_Pdf_Exception
 */
require_once 'Zend/Pdf/Exception.php';

/**
 * @category   Zend
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Pdf
 */
class Zend_PdfTest extends PHPUnit_Framework_TestCase
{

    public function testGetJavasriptNull()
    {
        // getting JavaScript without setting it returns NULL
        $pdf = new Zend_Pdf();
        $this->assertNull($pdf->getJavaScript());
    }

    public function testSetAndGetJavasriptArray()
    {
        // getting JavaScript after setting it returns array
        $pdf = new Zend_Pdf();
        $pdf->setJavaScript('print();');
        $this->assertTrue(is_array($pdf->getJavaScript()));
    }

    public function testSetJavaScriptString()
    {
        // setting string value is possible
        $pdf = new Zend_Pdf();
        $javaScriptString = 'print();';
        $pdf->setJavaScript($javaScriptString);
        $javaScript = $pdf->getJavaScript();
        $this->assertEquals($javaScriptString, $javaScript[0]);
    }

    public function testSetJavaScriptArray()
    {
        // setting string value is possible
        $pdf = new Zend_Pdf();
        $javaScriptArray = array('print();', 'alert();');
        $pdf->setJavaScript($javaScriptArray);
        $this->assertEquals($javaScriptArray, $pdf->getJavaScript());
    }

    public function testResetJavaScript()
    {
        // reset removes the added JavaScript
        $pdf = new Zend_Pdf();
        $pdf->setJavaScript('print();');
        $pdf->resetJavaScript();
        $this->assertNull($pdf->getJavaScript());
    }

    public function testAddJavaScript()
    {
        // adding JavaScript appends previously added JavaScript
        $pdf = new Zend_Pdf();
        $javaScriptArray = array('print();', 'alert();');
        $pdf->addJavaScript($javaScriptArray[0]);
        $pdf->addJavaScript($javaScriptArray[1]);
        $this->assertEquals($javaScriptArray, $pdf->getJavaScript());
    }

    public function testSetJavaScriptEmptyString()
    {
        // setting empty JavaScript string throws exception
        $pdf = new Zend_Pdf();
        try {
            $pdf->setJavaScript('');
            $this->fail('Expected exception when trying to set empty string.');
        } catch (Zend_Pdf_Exception $e) {
            $this->assertContains('JavaScript must be a non empty string or array of strings', $e->getMessage());
        }
    }

    public function testSetJavaScriptEmptyArray()
    {
        // setting empty JavaScript string throws exception
        $pdf = new Zend_Pdf();
        try {
            $pdf->setJavaScript(array());
            $this->fail('Expected exception when trying to set empty array.');
        } catch (Zend_Pdf_Exception $e) {
            $this->assertContains('JavaScript must be a non empty string or array of strings', $e->getMessage());
        }
    }

    public function testSetAndSaveLoadAndGetJavaScript()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'PdfUnitFile');
        $javaScript = array('print();', 'alert();');

        $pdf = new Zend_Pdf();
        $pdf->setJavaScript($javaScript);
        $pdf->save($tempFile);
        unset($pdf);

        $pdf = Zend_Pdf::load($tempFile);
        unlink($tempFile);

        $this->assertEquals($javaScript, $pdf->getJavaScript());
    }

    public function testSetAndSaveLoadAndResetAndSaveLoadAndGetJavaScript()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'PdfUnitFile');
        $javaScript = array('print();', 'alert();');

        $pdf = new Zend_Pdf();
        $pdf->setJavaScript($javaScript);
        $pdf->save($tempFile);
        unset($pdf);

        $pdf = Zend_Pdf::load($tempFile);
        unlink($tempFile);

        $pdf->resetJavaScript();
        $pdf->save($tempFile);
        unset($pdf);

        $pdf = Zend_Pdf::load($tempFile);
        unlink($tempFile);

        $this->assertNull($pdf->getJavaScript());
    }

}
