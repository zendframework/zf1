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

require_once 'Zend/Db/Table/Relationships/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class ZendX_Db_Table_Relationships_FirebirdTest extends Zend_Db_Table_Relationships_TestCommon
{

    public function testTableRelationshipFindParentRowSelect()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id', true);
        $account_name = $this->_db->foldCase('account_name');

        $table = $this->_table['bugs'];
        $select = $table->select()->where('"account_name" = ?', 'goofy');

        $childRows = $table->fetchAll("$bug_id = 1");
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $parentRow = $childRow1->findParentRow('Zend_Db_Table_TableAccounts', null, $select);
        $this->assertType('Zend_Db_Table_Row_Abstract', $parentRow,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($parentRow));

        $this->assertEquals('goofy', $parentRow->$account_name);
    }

    public function testTableRelationshipMagicFindParentRowSelect()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id', true);
        $account_name = $this->_db->foldCase('account_name');

        $table = $this->_table['bugs'];
        $select = $table->select()->where('"account_name" = ?', 'goofy');

        $childRows = $table->fetchAll("$bug_id = 1");
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $parentRow = $childRow1->findParentZend_Db_Table_TableAccounts($select);
        $this->assertType('Zend_Db_Table_Row_Abstract', $parentRow,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($parentRow));

        $this->assertEquals('goofy', $parentRow->$account_name);
    }
	
    public function testTableRelationshipFindManyToManyRowsetSelect()
    {
        $product_name = $this->_db->foldCase('product_name');
        $bug_id = $this->_db->foldCase('"bug_id"');

        $table = $this->_table['bugs'];
        $select = $table->select()->where($bug_id . ' = ?', 1)
                                  ->limit(2)
                                  ->order($product_name . ' ASC');

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findManyToManyRowset('Zend_Db_Table_TableProducts', 'Zend_Db_Table_TableBugsProducts', 
                                                      null, null, $select);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $destRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(2, $destRows->count());

        $childRow = $destRows->current();
        $this->assertEquals('Linux', $childRow->$product_name);
    }

    public function testTableRelationshipMagicFindManyToManyRowsetSelect()
    {
        $product_name = $this->_db->foldCase('product_name');
        $bug_id = $this->_db->foldCase('"bug_id"');

        $table = $this->_table['bugs'];
        $select = $table->select()->where($bug_id . ' = ?', 1)
                                  ->limit(2)
                                  ->order($product_name . ' ASC');

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findZend_Db_Table_TableProductsViaZend_Db_Table_TableBugsProducts($select);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $destRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(2, $destRows->count());

        $childRow = $destRows->current();
        $this->assertEquals('Linux', $childRow->$product_name);
    }	


    public function getDriver()
    {
        return 'Firebird';
    }

}
