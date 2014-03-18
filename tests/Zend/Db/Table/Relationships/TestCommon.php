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
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Db_Table_TestSetup
 */
require_once 'Zend/Db/Table/TestSetup.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';




/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Table
 * @group      Zend_Db_Table_Relationships
 */
abstract class Zend_Db_Table_Relationships_TestCommon extends Zend_Db_Table_TestSetup
{

    public function testTableRelationshipFindParentRow()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id', true);
        $account_name = $this->_db->foldCase('account_name');

        $table = $this->_table['bugs'];

        $childRows = $table->fetchAll("$bug_id = 1");
        $this->assertTrue($childRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertTrue($childRow1 instanceof Zend_Db_Table_Row_Abstract,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $parentRow = $childRow1->findParentRow('My_ZendDbTable_TableAccounts');
        $this->assertTrue($parentRow instanceof Zend_Db_Table_Row_Abstract,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($parentRow));

        $this->assertEquals('goofy', $parentRow->$account_name);
    }

    public function testTableRelationshipFindParentRowSelect()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id', true);
        $account_name = $this->_db->foldCase('account_name');
        $account_name_column = $this->_db->quoteIdentifier('account_name', true);

        $table = $this->_table['bugs'];
        $select = $table->select()->where($account_name_column . ' = ?', 'goofy');

        $childRows = $table->fetchAll("$bug_id = 1");
        $this->assertTrue($childRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertTrue($childRow1 instanceof Zend_Db_Table_Row_Abstract,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $parentRow = $childRow1->findParentRow('My_ZendDbTable_TableAccounts', null, $select);
        $this->assertTrue($parentRow instanceof Zend_Db_Table_Row_Abstract,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($parentRow));

        $this->assertEquals('goofy', $parentRow->$account_name);
    }

    public function testTableRelationshipMagicFindParentRow()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id', true);
        $account_name = $this->_db->foldCase('account_name');

        $table = $this->_table['bugs'];

        $childRows = $table->fetchAll("$bug_id = 1");
        $this->assertTrue($childRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertTrue($childRow1 instanceof Zend_Db_Table_Row_Abstract,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $parentRow = $childRow1->findParentMy_ZendDbTable_TableAccounts();
        $this->assertTrue($parentRow instanceof Zend_Db_Table_Row_Abstract,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($parentRow));

        $this->assertEquals('goofy', $parentRow->$account_name);
    }

    public function testTableRelationshipMagicFindParentRowSelect()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id', true);
        $account_name = $this->_db->foldCase('account_name');
        $account_name_column = $this->_db->quoteIdentifier('account_name', true);

        $table = $this->_table['bugs'];
        $select = $table->select()->where($account_name_column . ' = ?', 'goofy');

        $childRows = $table->fetchAll("$bug_id = 1");
        $this->assertTrue($childRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertTrue($childRow1 instanceof Zend_Db_Table_Row_Abstract,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $parentRow = $childRow1->findParentMy_ZendDbTable_TableAccounts($select);
        $this->assertTrue($parentRow instanceof Zend_Db_Table_Row_Abstract,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($parentRow));

        $this->assertEquals('goofy', $parentRow->$account_name);
    }

    public function testTableRelationshipMagicException()
    {
        $table = $this->_table['bugs'];

        $parentRows = $table->find(1);
        $parentRow1 = $parentRows->current();

        // Completely bogus method
        try {
            $result = $parentRow1->nonExistantMethod();
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertTrue($e instanceof Zend_Db_Table_Row_Exception,
                'Expecting object of type Zend_Db_Table_Row_Exception got '.get_class($e));
            $this->assertEquals("Unrecognized method 'nonExistantMethod()'", $e->getMessage());
        }
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTableRelationshipFindParentRowErrorOnBadString()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id', true);

        $table = $this->_table['bugs'];

        $childRows = $table->fetchAll("$bug_id = 1");
        $childRow1 = $childRows->current();

        $parentRow = $childRow1->findParentRow('nonexistant_class');
    }

    /**
     * @expectedException Zend_Db_Table_Exception
     */
    public function testTableRelationshipFindParentRowExceptionOnBadClass()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id', true);

        $table = $this->_table['bugs'];

        $childRows = $table->fetchAll("$bug_id = 1");
        $childRow1 = $childRows->current();

        $parentRow = $childRow1->findParentRow(new stdClass());
    }

    public function testTableRelationshipFindManyToManyRowset()
    {
        $table = $this->_table['bugs'];

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findManyToManyRowset('My_ZendDbTable_TableProducts', 'My_ZendDbTable_TableBugsProducts');
        $this->assertTrue($destRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(3, $destRows->count());
    }

    public function testTableRelationshipFindManyToManyRowsetSelect()
    {
        $product_name = $this->_db->foldCase('product_name');
        $bug_id = $this->_db->foldCase('bug_id');
        $bug_id_column = $this->_db->quoteIdentifier('bug_id', true);

        $table = $this->_table['bugs'];
        $select = $table->select()->where($bug_id_column . ' = ?', 1)
                                  ->limit(2)
                                  ->order($product_name . ' ASC');

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findManyToManyRowset('My_ZendDbTable_TableProducts', 'My_ZendDbTable_TableBugsProducts',
                                                      null, null, $select);
        $this->assertTrue($destRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(2, $destRows->count());

        $childRow = $destRows->current();
        $this->assertEquals('Linux', $childRow->$product_name);
    }

    public function testTableRelationshipMagicFindManyToManyRowset()
    {
        $table = $this->_table['bugs'];

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findMy_ZendDbTable_TableProductsViaMy_ZendDbTable_TableBugsProducts();
        $this->assertTrue($destRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(3, $destRows->count());
    }

    public function testTableRelationshipMagicFindManyToManyRowsetSelect()
    {
        $product_name = $this->_db->foldCase('product_name');
        $bug_id = $this->_db->foldCase('bug_id');
        $bug_id_column = $this->_db->quoteIdentifier('bug_id', true);

        $table = $this->_table['bugs'];
        $select = $table->select()->where($bug_id_column . ' = ?', 1)
                                  ->limit(2)
                                  ->order($product_name . ' ASC');

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findMy_ZendDbTable_TableProductsViaMy_ZendDbTable_TableBugsProducts($select);
        $this->assertTrue($destRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(2, $destRows->count());

        $childRow = $destRows->current();
        $this->assertEquals('Linux', $childRow->$product_name);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTableRelationshipFindManyToManyRowsetErrorOnBadClassNameAsString()
    {
        $table = $this->_table['bugs'];

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        // Use nonexistant class for destination table
        $destRows = $originRow1->findManyToManyRowset('nonexistant_class', 'My_ZendDbTable_TableBugsProducts');

    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTableRelationshipFindManyToManyRowsetErrorOnBadClassNameAsStringForIntersection()
    {
        $table = $this->_table['bugs'];

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        // Use nonexistant class for intersection table
        $destRows = $originRow1->findManyToManyRowset('My_ZendDbTable_TableProducts', 'nonexistant_class');
    }

    /**
     * @expectedException Zend_Db_Table_Exception
     */
    public function testTableRelationshipFindManyToManyRowsetExceptionOnBadClassAsString()
    {

        $table = $this->_table['bugs'];

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        // Use stdClass instead of table class for destination table
        $destRows = $originRow1->findManyToManyRowset(new stdClass(), 'My_ZendDbTable_TableBugsProducts');

    }


    /**
     * @expectedException Zend_Db_Table_Exception
     */
    public function testTableRelationshipFindManyToManyRowsetExceptionOnBadClassAsStringForIntersection()
    {
        $table = $this->_table['bugs'];

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        // Use stdClass instead of table class for intersection table
        $destRows = $originRow1->findManyToManyRowset('My_ZendDbTable_TableProducts', new stdClass());

    }

    public function testTableRelationshipFindDependentRowset()
    {
        $table = $this->_table['bugs'];
        $bug_id = $this->_db->foldCase('bug_id');
        $product_id = $this->_db->foldCase('product_id');

        $parentRows = $table->find(1);
        $this->assertTrue($parentRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($parentRows));
        $parentRow1 = $parentRows->current();

        $childRows = $parentRow1->findDependentRowset('My_ZendDbTable_TableBugsProducts');
        $this->assertTrue($childRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $this->assertEquals(3, $childRows->count());

        $childRow1 = $childRows->current();
        $this->assertTrue($childRow1 instanceof Zend_Db_Table_Row_Abstract,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $this->assertEquals(1, $childRow1->$bug_id);
        $this->assertEquals(1, $childRow1->$product_id);
    }

    public function testTableRelationshipFindDependentRowsetSelect()
    {
        $table = $this->_table['bugs'];
        $bug_id = $this->_db->foldCase('bug_id');
        $product_id = $this->_db->foldCase('product_id');

        $select = $table->select()->limit(2)
                                  ->order($product_id . ' DESC');

        $parentRows = $table->find(1);
        $this->assertTrue($parentRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($parentRows));
        $parentRow1 = $parentRows->current();

        $childRows = $parentRow1->findDependentRowset('My_ZendDbTable_TableBugsProducts', null, $select);
        $this->assertTrue($childRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertTrue($childRow1 instanceof Zend_Db_Table_Row_Abstract,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $this->assertEquals(1, $childRow1->$bug_id);
        $this->assertEquals(3, $childRow1->$product_id);
    }

    public function testTableRelationshipMagicFindDependentRowset()
    {
        $table = $this->_table['bugs'];
        $bug_id = $this->_db->foldCase('bug_id');
        $product_id = $this->_db->foldCase('product_id');

        $parentRows = $table->find(1);
        $parentRow1 = $parentRows->current();

        $childRows = $parentRow1->findMy_ZendDbTable_TableBugsProducts();
        $this->assertTrue($childRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $this->assertEquals(3, $childRows->count());

        $childRow1 = $childRows->current();
        $this->assertTrue($childRow1 instanceof Zend_Db_Table_Row_Abstract,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $this->assertEquals(1, $childRow1->$bug_id);
        $this->assertEquals(1, $childRow1->$product_id);
    }

    public function testTableRelationshipMagicFindDependentRowsetSelect()
    {
        $table = $this->_table['bugs'];
        $bug_id = $this->_db->foldCase('bug_id');
        $product_id = $this->_db->foldCase('product_id');
        $select = $table->select()->limit(2)
                                  ->order($product_id . ' DESC');

        $parentRows = $table->find(1);
        $parentRow1 = $parentRows->current();

        $childRows = $parentRow1->findMy_ZendDbTable_TableBugsProducts($select);
        $this->assertTrue($childRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $this->assertEquals(2, $childRows->count());

        $childRow1 = $childRows->current();
        $this->assertTrue($childRow1 instanceof Zend_Db_Table_Row_Abstract,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $this->assertEquals(1, $childRow1->$bug_id);
        $this->assertEquals(3, $childRow1->$product_id);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testTableRelationshipFindDependentRowsetPhpError()
    {
        $table = $this->_table['bugs'];

        $parentRows = $table->find(1);
        $parentRow1 = $parentRows->current();

        $childRows = $parentRow1->findDependentRowset('nonexistant_class');
    }

    /**
     * Ensures that basic cascading update functionality succeeds using strings for single columns
     *
     * @return void
     */
    public function testTableRelationshipCascadingUpdateUsageBasicString()
    {
        $bug = $this->_getTable('My_ZendDbTable_TableBugsCustom')
                ->find(1)
                ->current();
        $bug_id = $this->_db->foldCase('bug_id');

        $this->assertEquals(
            3,
            count($bugProducts = $bug->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find three dependent rows'
            );

        $bug->$bug_id = 333;

        $bug->save();

        $this->assertEquals(
            3,
            count($bugProducts = $bug->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find three dependent rows'
            );

        foreach ($bugProducts as $bugProduct) {
            $this->assertEquals(333, $bugProduct->$bug_id);
        }

        $bug->$bug_id = 1;

        $bug->save();

        $this->assertEquals(
            3,
            count($bugProducts = $bug->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find three dependent rows'
            );

        foreach ($bugProducts as $bugProduct) {
            $this->assertEquals(1, $bugProduct->$bug_id);
        }
    }

    /**
     * Ensures that basic cascading update functionality succeeds using arrays for single columns
     *
     * @return void
     */
    public function testTableRelationshipCascadingUpdateUsageBasicArray()
    {
        $account1 = $this->_getTable('My_ZendDbTable_TableAccountsCustom')
                    ->find('mmouse')
                    ->current();
        $account_name = $this->_db->foldCase('account_name');
        $reported_by = $this->_db->foldCase('reported_by');

        $this->assertEquals(
            1,
            count($account1->findDependentRowset('My_ZendDbTable_TableBugsCustom')),
            'Expecting to find one dependent row'
            );

        $account1->$account_name = 'daisy';

        $account1->save();

        $this->assertEquals(
            1,
            count($account1Bugs = $account1->findDependentRowset('My_ZendDbTable_TableBugsCustom')),
            'Expecting to find one dependent row'
            );

        foreach ($account1Bugs as $account1Bug) {
            $this->assertEquals('daisy', $account1Bug->$reported_by);
        }

        $account1->$account_name = 'mmouse';

        $account1->save();

        $this->assertEquals(
            1,
            count($account1Bugs = $account1->findDependentRowset('My_ZendDbTable_TableBugsCustom')),
            'Expecting to find one dependent row'
            );

        foreach ($account1Bugs as $account1Bug) {
            $this->assertEquals('mmouse', $account1Bug->$reported_by);
        }
    }

    /**
     * Ensures that cascading update functionality is not run when onUpdate != self::CASCADE
     *
     * @return void
     */
    public function testTableRelationshipCascadingUpdateUsageInvalidNoop()
    {
        $product1 = $this->_getTable('My_ZendDbTable_TableProductsCustom')
                    ->find(1)
                    ->current();

        $this->assertEquals(
            1,
            count($product1->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find one dependent row'
            );

        $product_id = $this->_db->foldCase('product_id');
        $product1->$product_id = 333;

        $product1->save();

        $this->assertEquals(
            0,
            count($product1BugsProducts = $product1->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find one dependent row'
            );

        $product1->$product_id = 1;

        $product1->save();

        $this->assertEquals(
            1,
            count($product1BugsProducts = $product1->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find one dependent row'
            );

        foreach ($product1BugsProducts as $product1BugsProduct) {
            $this->assertEquals(1, $product1BugsProduct->$product_id);
        }
    }

    /**
     * Ensures that basic cascading delete functionality succeeds using strings for single columns
     *
     * @return void
     */
    public function testTableRelationshipCascadingDeleteUsageBasicString()
    {
        $bug1 = $this->_getTable('My_ZendDbTable_TableBugsCustom')
                ->find(1)
                ->current();

        $this->assertEquals(
            3,
            count($bug1->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find three dependent rows'
            );

        $bug1->delete();

        $bug_id = $this->_db->quoteIdentifier('bug_id', true);

        $this->assertEquals(
            0,
            count($this->_getTable('My_ZendDbTable_TableBugsProductsCustom')->fetchAll("$bug_id = 1")),
            'Expecting cascading delete to have reduced dependent rows to zero'
            );
    }

    /**
     * Ensures that basic cascading delete functionality succeeds using arrays for single columns
     *
     * @return void
     */
    public function testTableRelationshipCascadingDeleteUsageBasicArray()
    {
        $reported_by = $this->_db->quoteIdentifier('reported_by', true);

        $account1 = $this->_getTable('My_ZendDbTable_TableAccountsCustom')
                    ->find('mmouse')
                    ->current();

        $this->assertEquals(
            1,
            count($account1->findDependentRowset('My_ZendDbTable_TableBugsCustom')),
            'Expecting to find one dependent row'
            );

        $account1->delete();

        $tableBugsCustom = $this->_getTable('My_ZendDbTable_TableBugsCustom');

        $this->assertEquals(
            0,
            count(
                $tableBugsCustom->fetchAll(
                    $tableBugsCustom->getAdapter()
                                    ->quoteInto("$reported_by = ?", 'mmouse')
                    )
                ),
            'Expecting cascading delete to have reduced dependent rows to zero'
            );
    }

    /**
     * Ensures that cascading delete functionality is not run when onDelete != self::CASCADE
     *
     * @return void
     */
    public function testTableRelationshipCascadingDeleteUsageInvalidNoop()
    {
        $product1 = $this->_getTable('My_ZendDbTable_TableProductsCustom')
                    ->find(1)
                    ->current();

        $this->assertEquals(
            1,
            count($product1->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find one dependent row'
            );

        $product1->delete();

        $product_id = $this->_db->quoteIdentifier('product_id', true);

        $this->assertEquals(
            1,
            count($this->_getTable('My_ZendDbTable_TableBugsProductsCustom')->fetchAll("$product_id = 1")),
            'Expecting to find one dependent row'
            );
    }

    public function testTableRelationshipGetReference()
    {
        $table = $this->_table['bugs'];

        $map = $table->getReference('My_ZendDbTable_TableAccounts', 'Reporter');

        $this->assertThat($map, $this->arrayHasKey('columns'));
        $this->assertThat($map, $this->arrayHasKey('refTableClass'));
        $this->assertThat($map, $this->arrayHasKey('refColumns'));
    }

    public function testTableRelationshipGetReferenceException()
    {
        $table = $this->_table['bugs'];

        try {
            $table->getReference('My_ZendDbTable_TableAccounts', 'Nonexistent');
            $this->fail('Expected to catch Zend_Db_Table_Exception for nonexistent reference rule');
        } catch (Zend_Exception $e) {
            $this->assertTrue($e instanceof Zend_Db_Table_Exception,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
        }

        try {
            $table->getReference('Nonexistent', 'Reporter');
            $this->fail('Expected to catch Zend_Db_Table_Exception for nonexistent rule tableClass');
        } catch (Zend_Exception $e) {
            $this->assertTrue($e instanceof Zend_Db_Table_Exception,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
        }

        try {
            $table->getReference('Nonexistent');
            $this->fail('Expected to catch Zend_Db_Table_Exception for nonexistent rule tableClass');
        } catch (Zend_Exception $e) {
            $this->assertTrue($e instanceof Zend_Db_Table_Exception,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
        }
    }

    /**
     * Ensures that findParentRow() returns an instance of a custom row class when passed an instance
     * of the table class having $_rowClass overridden.
     *
     * @return void
     */
    public function testTableRelationshipFindParentRowCustomInstance()
    {
        $this->_useMyIncludePath();

        $myRowClass = 'My_ZendDbTable_Row_TestMyRow';

        Zend_Loader::loadClass($myRowClass);

        $bug1Reporter = $this->_table['bugs']
                        ->find(1)
                        ->current()
                        ->findParentRow($this->_table['accounts']->setRowClass($myRowClass));

        $this->assertTrue($bug1Reporter instanceof $myRowClass,
            "Expecting object of type $myRowClass, got ".get_class($bug1Reporter));
    }

    /**
     * Ensures that findParentRow() returns an instance of a custom row class when passed a string class
     * name, where the class has $_rowClass overridden.
     *
     * @return void
     */
    public function testTableRelationshipFindParentRowCustomClass()
    {
        $this->_useMyIncludePath();

        $myRowClass = 'My_ZendDbTable_Row_TestMyRow';

        Zend_Loader::loadClass($myRowClass);

        Zend_Loader::loadClass('My_ZendDbTable_TableAccountsCustom');

        $bug1Reporter = $this->_getTable('My_ZendDbTable_TableBugsCustom')
                        ->find(1)
                        ->current()
                        ->findParentRow(new My_ZendDbTable_TableAccountsCustom(array('db' => $this->_db)));

        $this->assertTrue($bug1Reporter instanceof $myRowClass,
            "Expecting object of type $myRowClass, got ".get_class($bug1Reporter));
    }

    /**
     * Ensures that findDependentRowset() returns instances of custom row and rowset classes when
     * passed an instance of the table class.
     *
     * @return void
     */
    public function testTableRelationshipFindDependentRowsetCustomInstance()
    {
        $this->_useMyIncludePath();

        $myRowsetClass = 'My_ZendDbTable_Rowset_TestMyRowset';
        $myRowClass    = 'My_ZendDbTable_Row_TestMyRow';

        Zend_Loader::loadClass($myRowsetClass);

        $account_name = $this->_db->quoteIdentifier('account_name', true);

        $bugs = $this->_table['accounts']
                ->fetchRow($this->_db->quoteInto("$account_name = ?", 'mmouse'))
                ->findDependentRowset(
                    $this->_table['bugs']
                        ->setRowsetClass($myRowsetClass)
                        ->setRowClass($myRowClass),
                    'Engineer'
                    );

        $this->assertTrue($bugs instanceof $myRowsetClass,
            "Expecting object of type $myRowsetClass, got ".get_class($bugs));

        $this->assertEquals(3, count($bugs));

        foreach ($bugs as $bug) {
            $this->assertTrue($bug instanceof $myRowClass,
                "Expecting object of type $myRowClass, got ".get_class($bug));
        }
    }

    /**
     * Ensures that findDependentRowset() returns instances of custom row and rowset classes when
     * passed the named class.
     *
     * @return void
     */
    public function testTableRelationshipFindDependentRowsetCustomClass()
    {
        $this->_useMyIncludePath();

        $myRowsetClass = 'My_ZendDbTable_Rowset_TestMyRowset';
        $myRowClass    = 'My_ZendDbTable_Row_TestMyRow';

        Zend_Loader::loadClass($myRowsetClass);

        $account_name = $this->_db->quoteIdentifier('account_name', true);

        $bugs = $this->_getTable('My_ZendDbTable_TableAccountsCustom')
                ->fetchRow($this->_db->quoteInto("$account_name = ?", 'mmouse'))
                ->findDependentRowset('My_ZendDbTable_TableBugsCustom', 'Engineer');

        $this->assertTrue($bugs instanceof $myRowsetClass,
            "Expecting object of type $myRowsetClass, got ".get_class($bugs));

        $this->assertEquals(3, count($bugs));

        foreach ($bugs as $bug) {
            $this->assertTrue($bug instanceof $myRowClass,
                "Expecting object of type $myRowClass, got ".get_class($bug));
        }
    }

    /**
     * Ensures that findManyToManyRowset() returns instances of custom row and rowset class when
     * passed an instance of the table class.
     *
     * @return void
     */
    public function testTableRelationshipFindManyToManyRowsetCustomInstance()
    {
        $this->_useMyIncludePath();

        $myRowsetClass = 'My_ZendDbTable_Rowset_TestMyRowset';
        $myRowClass    = 'My_ZendDbTable_Row_TestMyRow';

        Zend_Loader::loadClass($myRowsetClass);

        $bug1Products = $this->_table['bugs']
                        ->find(1)
                        ->current()
                        ->findManyToManyRowset(
                            $this->_table['products']
                                ->setRowsetClass($myRowsetClass)
                                ->setRowClass($myRowClass),
                            'My_ZendDbTable_TableBugsProducts'
                            );

        $this->assertTrue($bug1Products instanceof $myRowsetClass,
            "Expecting object of type $myRowsetClass, got ".get_class($bug1Products));

        $this->assertEquals(3, count($bug1Products));

        foreach ($bug1Products as $bug1Product) {
            $this->assertTrue($bug1Product instanceof $myRowClass,
                "Expecting object of type $myRowClass, got ".get_class($bug1Product));
        }
    }

    /**
     * Ensures that findManyToManyRowset() returns instances of custom row and rowset classes when
     * passed the named class.
     *
     * @return void
     */
    public function testTableRelationshipFindManyToManyRowsetCustomClass()
    {
        $this->_useMyIncludePath();

        $myRowsetClass = 'My_ZendDbTable_Rowset_TestMyRowset';
        $myRowClass    = 'My_ZendDbTable_Row_TestMyRow';

        Zend_Loader::loadClass($myRowsetClass);

        $bug1Products = $this->_getTable('My_ZendDbTable_TableBugsCustom')
                        ->find(1)
                        ->current()
                        ->findManyToManyRowset(
                            'My_ZendDbTable_TableProductsCustom',
                            'My_ZendDbTable_TableBugsProductsCustom'
                            );

        $this->assertTrue($bug1Products instanceof $myRowsetClass,
            "Expecting object of type $myRowsetClass, got ".get_class($bug1Products));

        $this->assertEquals(3, count($bug1Products));

        foreach ($bug1Products as $bug1Product) {
            $this->assertTrue($bug1Product instanceof $myRowClass,
                "Expecting object of type $myRowClass, got ".get_class($bug1Product));
        }
    }

    /**
     * Ensures that rows returned by findParentRow() are updatable.
     *
     * @return void
     */
    public function testTableRelationshipFindParentRowIsUpdateable()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id', true);
        $account_name = $this->_db->foldCase('account_name');

        $table = $this->_table['bugs'];

        $childRows = $table->fetchAll("$bug_id = 1");
        $this->assertTrue($childRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertTrue($childRow1 instanceof Zend_Db_Table_Row_Abstract,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $parentRow = $childRow1->findParentRow('My_ZendDbTable_TableAccounts');
        $this->assertTrue($parentRow instanceof Zend_Db_Table_Row_Abstract,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($parentRow));

        $this->assertEquals('goofy', $parentRow->$account_name);

        $parentRow->$account_name = 'clarabell';
        try {
            $parentRow->save();
        } catch (Zend_Exception $e) {
            $this->fail('Failed with unexpected '.get_class($e).': '.$e->getMessage());
        }

        $accounts = $this->_db->quoteIdentifier('zfaccounts', true);
        $account_name = $this->_db->quoteIdentifier('account_name', true);
        $accounts_list = $this->_db->fetchCol("SELECT $account_name from $accounts ORDER BY $account_name");
        // if the save() did an UPDATE instead of an INSERT, then goofy should
        // be missing, and clarabell should be present
        $this->assertEquals(array('clarabell', 'dduck', 'mmouse'), $accounts_list);
    }

    /**
     * Ensures that rows returned by findDependentRowset() are updatable.
     *
     * @return void
     */
    public function testTableRelationshipFindDependentRowsetIsUpdateable()
    {
        $table = $this->_table['accounts'];
        $bug_id_column = $this->_db->foldCase('bug_id');
        $bug_description = $this->_db->foldCase('bug_description');

        $parentRows = $table->find('mmouse');
        $this->assertTrue($parentRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($parentRows));
        $parentRow1 = $parentRows->current();

        $childRows = $parentRow1->findDependentRowset('My_ZendDbTable_TableBugs');
        $this->assertTrue($childRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $this->assertEquals(1, $childRows->count());

        $childRow1 = $childRows->current();
        $this->assertTrue($childRow1 instanceof Zend_Db_Table_Row_Abstract,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $childRow1->$bug_description = 'Updated description';
        $bug_id = $childRow1->$bug_id_column;
        try {
            $childRow1->save();
        } catch (Zend_Exception $e) {
            $this->fail('Failed with unexpected '.get_class($e).': '.$e->getMessage());
        }

        // find the row we just updated and make sure it has the new value.
        $bugs_table = $this->_table['bugs'];
        $bugs_rows = $bugs_table->find($bug_id);
        $this->assertEquals(1, $bugs_rows->count());
        $bug1 = $bugs_rows->current();
        $this->assertEquals($bug_id, $bug1->$bug_id_column);
        $this->assertEquals('Updated description', $bug1->$bug_description);
    }

    /**
     * Ensures that rows returned by findManyToManyRowset() are updatable.
     *
     * @return void
     */
    public function testTableRelationshipFindManyToManyRowsetIsUpdateable()
    {
        $table = $this->_table['bugs'];
        $product_id_column = $this->_db->foldCase('product_id');
        $product_name = $this->_db->foldCase('product_name');

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findManyToManyRowset('My_ZendDbTable_TableProducts', 'My_ZendDbTable_TableBugsProducts');
        $this->assertTrue($destRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(3, $destRows->count());

        $row1 = $destRows->current();
        $product_id = $row1->$product_id_column;
        $row1->$product_name = 'AmigaOS';
        try {
            $row1->save();
        } catch (Zend_Exception $e) {
            $this->fail('Failed with unexpected '.get_class($e).': '.$e->getMessage());
        }

        // find the row we just updated and make sure it has the new value.
        $products_table = $this->_table['products'];
        $product_rows = $products_table->find($product_id);
        $this->assertEquals(1, $product_rows->count());
        $product_row = $product_rows->current();
        $this->assertEquals($product_id, $product_row->$product_id_column);
        $this->assertEquals('AmigaOS', $product_row->$product_name);
    }

    public function testTableRelationshipOmitRefColumns()
    {
        $refMap = array(
            'Reporter' => array(
                'columns'       => array('reported_by'),
                'refTableClass' => 'My_ZendDbTable_TableAccounts'
            )
        );
        $table = $this->_getTable('My_ZendDbTable_TableSpecial',
            array(
                'name'          => 'zfbugs',
                'referenceMap'  => $refMap
            )
        );

        $bug1 = $table->find(1)->current();
        $reporter = $bug1->findParentRow('My_ZendDbTable_TableAccounts');
        $this->assertEquals(array('account_name' => 'goofy'), $reporter->toArray());
    }

    /**
     * Test that findParentRow() works even if the column names are
     * not the same.
     */
    public function testTableRelationshipFindParentRowWithDissimilarColumns()
    {
        $bug_id = $this->_db->foldCase('bug_id');
        $product_id = $this->_db->foldCase('product_id');

        $intersectionTable = $this->_getBugsProductsWithDissimilarColumns();
        $intRow = $intersectionTable->find(2, 3)->current();

        $bugRow = $intRow->findParentRow('My_ZendDbTable_TableBugs');
        $this->assertEquals(2, $bugRow->$bug_id);

        $productRow = $intRow->findParentRow('My_ZendDbTable_TableProducts');
        $this->assertEquals(3, $productRow->$product_id);
    }

    /**
     * Test that findDependentRowset() works even if the column names are
     * not the same.
     */
    public function testTableRelationshipFindDependentRowsetWithDissimilarColumns()
    {
        $intersectionTable = $this->_getBugsProductsWithDissimilarColumns();
        $bugsTable = $this->_getTable('My_ZendDbTable_TableBugs');
        $bugRow = $bugsTable->find(2)->current();

        $intRows = $bugRow->findDependentRowset($intersectionTable);
        $this->assertEquals(array(2, 3), array_values($intRows->current()->toArray()));
    }

    /**
     * Test that findManyToManyRowset() works even if the column names are
     * not the same.
     */
    public function testTableRelationshipFindManyToManyRowsetWithDissimilarColumns()
    {
        $product_id = $this->_db->foldCase('product_id');

        $intersectionTable = $this->_getBugsProductsWithDissimilarColumns();
        $bugsTable = $this->_getTable('My_ZendDbTable_TableBugs');
        $bugRow = $bugsTable->find(2)->current();

        $productRows = $bugRow->findManyToManyRowset('My_ZendDbTable_TableProducts', $intersectionTable);
        $this->assertEquals(3, $productRows->current()->$product_id);
    }

    /**
     * Test that findManyToManyRowset() works even if the column types are
     * not the same.
     */
    public function testTableRelationshipFindManyToManyRowsetWithDissimilarTypes()
    {
        $table = $this->_table['products'];

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findManyToManyRowset('My_ZendDbTable_TableBugs', 'My_ZendDbTable_TableBugsProducts');
        $this->assertTrue($destRows instanceof Zend_Db_Table_Rowset_Abstract,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(1, $destRows->count());
    }

    /**
     * @group ZF-3486
     */
    public function testTableRelationshipCanFindParentViaConcreteInstantiation()
    {
        Zend_Db_Table::setDefaultAdapter($this->_db);

        $definition = $this->_getTableDefinition();

        $bugsTable = new Zend_Db_Table('Bugs', $definition);
        $rowset = $bugsTable->find(1);
        $row = $rowset->current();
        $parent = $row->findParentRow('Accounts', 'Engineer');
        $this->assertEquals('mmouse', $parent->account_name);

        Zend_Db_Table::setDefaultAdapter();
    }

    /**
     * @group ZF-3486
     */
    public function testTableRelationshipCanFindDependentRowsetViaConcreteInstantiation()
    {
        Zend_Db_Table::setDefaultAdapter($this->_db);

        $definition = $this->_getTableDefinition();

        $productsTable = new Zend_Db_Table('Products', $definition);
        $productsRowset = $productsTable->find(1);
        $productRow = $productsRowset->current();

        $this->assertEquals('Windows', $productRow->product_name);

        $this->assertEquals(1, count($productRow->findDependentRowset('BugsProducts', 'Product')));

        $bugsProductRow = $productRow->findDependentRowset('BugsProducts', 'Product')->current();
        $this->assertEquals(1, $bugsProductRow->product_id);
        $this->assertEquals(1, $bugsProductRow->bug_id);

        Zend_Db_Table::setDefaultAdapter();
    }

    /**
     * @group ZF-3486
     */
    public function testTableRelationshipCanFindManyToManyRowsetViaConcreteInstantiation()
    {
        Zend_Db_Table::setDefaultAdapter($this->_db);

        $definition = $this->_getTableDefinition();

        $bugsTable = new Zend_Db_Table('Bugs', $definition);
        $bugsRowset = $bugsTable->find(1);
        $bugRow = $bugsRowset->current();

        $m2mRowset = $bugRow->findManyToManyRowset('Products', 'BugsProducts');

        $this->assertEquals(3, $m2mRowset->count());
    }

    /**
     * Utility Methods Below
     *
     */

    /**
     * _getTableDefinition()
     *
     * @return Zend_Db_Table_Definition
     */
    protected function _getTableDefinition()
    {
        $definition = array(
            'Bugs' => array(
                'name' => 'zfbugs',
                'referenceMap' => array(
                    'Reporter' => array(
                        'columns'           => 'reported_by',
                        'refTableClass'     => 'Accounts',
                        'refColumns'        => 'account_name'
                        ),
                    'Engineer' => array(
                        'columns'           => 'assigned_to',
                        'refTableClass'     => 'Accounts',
                        'refColumns'        => 'account_name'
                        ),
                    'Verifier' => array(
                        'columns'           => 'verified_by',
                        'refTableClass'     => 'Accounts',
                        'refColumns'        => 'account_name'
                        )
                    )
                ),
            'Accounts' => array(
                'name' => 'zfaccounts'
                ),
            'BugsProducts' => array(
                'name' => 'zfbugs_products',
                'referenceMap' => array(
                    'Bug' => array(
                        'columns'           => 'bug_id', // Deliberate non-array value
                        'refTableClass'     => 'Bugs',
                        'refColumns'        => 'bug_id'
                        ),
                    'Product' => array(
                        'columns'           => 'product_id',
                        'refTableClass'     => 'Products',
                        'refColumns'        => 'product_id',
                        'onDelete'          => Zend_Db_Table::CASCADE,
                        'onUpdate'          => Zend_Db_Table::CASCADE
                        )
                    )
                ),
            'Products' => array(
                'name' => 'zfproducts'
                )
            );

        return new Zend_Db_Table_Definition($definition);
    }

    /**
     * Create database table based on BUGS_PRODUCTS bug with alternative
     * spellings of column names.  Then create a Table class for this
     * physical table and return it.
     */
    protected function _getBugsProductsWithDissimilarColumns()
    {
        $altCols = array(
            'boog_id'      => 'INTEGER NOT NULL',
            'produck_id'   => 'INTEGER NOT NULL',
            'PRIMARY KEY'  => 'boog_id,produck_id'
        );
        $this->_util->createTable('AltBugsProducts', $altCols);
        $altBugsProducts = $this->_db->quoteIdentifier($this->_db->foldCase('zfalt_bugs_products'), true);
        $bugsProducts = $this->_db->quoteIdentifier($this->_db->foldCase('zfbugs_products'), true);
        $this->_db->query("INSERT INTO $altBugsProducts SELECT * FROM $bugsProducts");

        $refMap    = array(
            'Boog' => array(
                'columns'           => array('boog_id'),
                'refTableClass'     => 'My_ZendDbTable_TableBugs',
                'refColumns'        => array('bug_id')
            ),
            'Produck' => array(
                'columns'           => array('produck_id'),
                'refTableClass'     => 'My_ZendDbTable_TableProducts',
                'refColumns'        => array('product_id')
            )
        );
        $options = array('name' => 'zfalt_bugs_products', 'referenceMap' => $refMap);
        $table = $this->_getTable('My_ZendDbTable_TableSpecial', $options);
        return $table;
    }

    /**
     * Ensure that the related table returned from the ManyToManyRowset only contains
     * the proper columns for the table.
     *
     * @group ZF-3709
     */
    public function testTableRelationshipReturnsOnlyTheColumnsInTargetTable()
    {
        $table = $this->_table['bugs'];
        $relatedTable = $this->_table['products'];
        $relatedTableExpectedColumns = $relatedTable->info(Zend_Db_Table::COLS);

        $row = $table->fetchRow('bug_id = 1');

        $relatedRows = $row->findManyToManyRowset('My_ZendDbTable_TableProducts', 'My_ZendDbTable_TableBugsProducts');

        foreach ($relatedRows as $relatedRow) {
            $actualColumns = array_keys($relatedRow->toArray());
            $this->assertEquals($relatedTableExpectedColumns, $actualColumns);
        }
    }

}
