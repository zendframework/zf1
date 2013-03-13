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
 * @category   ZendX
 * @package    ZendX_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Db/Table/Row/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class ZendX_Db_Table_Row_FirebirdTest extends Zend_Db_Table_Row_TestCommon
{

    public function testTableRowSetReadOnly()
    {
        $table = $this->_table['bugs'];
        $bug_status = $this->_db->foldCase('bug_status');

        $rowset = $table->find(1);
        $row1 = $rowset->current();

        $row1->setReadOnly(true);
        $this->assertTrue($row1->isReadOnly());

        $data = array(
            'bug_id'          => $this->_db->nextSequenceId('zfbugs_seq'),
            'bug_description' => 'New Description',
            'bug_status'      => 'INVALID'
        );

        $row2 = $table->createRow($data);
        $row2->setReadOnly(true);
        try {
            $row2->save();
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals('This row has been marked read-only', $e->getMessage());
        }

        $row2->setReadOnly(false);
        $row2->save();

        $row2->$bug_status = 'VALID';
        $row2->setReadOnly(true);

        try {
            $row2->save();
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals('This row has been marked read-only', $e->getMessage());
        }

        $row2->setReadOnly(false);
        $row2->save();
    }

    public function testTableRowSaveInsert()
    {
        $table = $this->_table['bugs'];
        $data = array(
            'bug_description' => 'New Description',
            'bug_status'      => 'INVALID'
        );
        try {
            $row3 = $table->createRow($data);
            $this->assertNull($row3->bug_id);
            $row3->bug_id = $this->_db->nextSequenceId('zfbugs_seq');
            $row3->save();
            $this->assertEquals(5, $row3->bug_id);
            $this->assertEquals($data['bug_description'], $row3->bug_description);
            $this->assertEquals($data['bug_status'], $row3->bug_status);
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    public function getDriver()
    {
        return 'Firebird';
    }

}
