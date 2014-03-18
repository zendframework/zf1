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
 * @package    Zend_Gdata_Gapps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once 'Zend/Gdata/Gapps/EmailListRecipientEntry.php';
require_once 'Zend/Gdata/Gapps.php';

/**
 * @category   Zend
 * @package    Zend_Gdata_Gapps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Gapps
 */
class Zend_Gdata_Gapps_EmailListRecipientEntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/Gdata/Gapps/_files/EmailListRecipientEntryDataSample1.xml',
                true);
        $this->entry = new Zend_Gdata_Gapps_EmailListRecipientEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($emailListRecipientEntry) {
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/emailList/2.0/us-sales/recipient/SusanJones%40example.com',
            $emailListRecipientEntry->id->text);
        $this->assertEquals('1970-01-01T00:00:00.000Z', $emailListRecipientEntry->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $emailListRecipientEntry->category[0]->scheme);
        $this->assertEquals('http://schemas.google.com/apps/2006#emailList.recipient', $emailListRecipientEntry->category[0]->term);
        $this->assertEquals('text', $emailListRecipientEntry->title->type);
        $this->assertEquals('SusanJones', $emailListRecipientEntry->title->text);;
        $this->assertEquals('self', $emailListRecipientEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $emailListRecipientEntry->getLink('self')->type);
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/emailList/2.0/us-sales/recipient/SusanJones%40example.com', $emailListRecipientEntry->getLink('self')->href);
        $this->assertEquals('edit', $emailListRecipientEntry->getLink('edit')->rel);
        $this->assertEquals('application/atom+xml', $emailListRecipientEntry->getLink('edit')->type);
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/emailList/2.0/us-sales/recipient/SusanJones%40example.com', $emailListRecipientEntry->getLink('edit')->href);
        $this->assertEquals('SusanJones@example.com', $emailListRecipientEntry->who->email);
    }

    public function testEmptyEntryShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionElements() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionAttributes() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testEmptyEmailListRecipientEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newEmailListRecipientEntry = new Zend_Gdata_Gapps_EmailListRecipientEntry();
        $newEmailListRecipientEntry->transferFromXML($entryXml);
        $newEmailListRecipientEntryXml = $newEmailListRecipientEntry->saveXML();
        $this->assertTrue($entryXml == $newEmailListRecipientEntryXml);
    }

    public function testSamplePropertiesAreCorrect () {
        $this->entry->transferFromXML($this->entryText);
        $this->verifyAllSamplePropertiesAreCorrect($this->entry);
    }

    public function testConvertEmailListRecipientEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newEmailListRecipientEntry = new Zend_Gdata_Gapps_EmailListRecipientEntry();
        $newEmailListRecipientEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newEmailListRecipientEntry);
        $newEmailListRecipientEntryXml = $newEmailListRecipientEntry->saveXML();
        $this->assertEquals($entryXml, $newEmailListRecipientEntryXml);
    }

}
