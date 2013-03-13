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

require_once 'Zend/Db/Table/Select/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class ZendX_Db_Table_Select_FirebirdTest extends Zend_Db_Table_Select_TestCommon
{

    public function testSelectWhereWithTypeFloat()
    {
        $this->markTestIncomplete($this->getDriver() . ' (setlocale bugging subsequent tests)');
    }

    public function testSelectFromSelectObject ()
    {
        $select = $this->_selectFromSelectObject();
        $query = $select->assemble();
        $cmp = 'SELECT ' . $this->_db->quoteIdentifier('t') . '.* FROM (SELECT '
                         . $this->_db->quoteIdentifier('subqueryTable') . '.* FROM '
                         . $this->_db->quoteIdentifier('subqueryTable') . ') '
                         . $this->_db->quoteIdentifier('t');
        $this->assertEquals($query, $cmp);
    }

    /**
     * Test the UNION statement for a Zend_Db_Select object.
     */
    protected function _selectUnionString()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $bug_status = $this->_db->quoteIdentifier('bug_status');
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');
        $id = $this->_db->quoteIdentifier('id');
        $name = $this->_db->quoteIdentifier('name');
        $sql1 = "SELECT $bug_id AS $id, $bug_status AS $name FROM $bugs";
        $sql2 = "SELECT $product_id AS $id, $product_name AS $name FROM $products";

        $select = $this->_db->select()
            ->union(array($sql1, $sql2))
            ->order(new Zend_Db_Expr('1'));
        return $select;
    }

    protected function _selectColumnWithColonQuotedParameter()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("'as''as:xX'" . ' = ?', $this->_db->quote("as'as:x"));
        return $select;
    }

    protected function _selectOrderByAutoExpr()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->order("UPPER($products.$product_id)");
        return $select;
    }

    protected function _selectGroupByAutoExpr()
    {
        $thecount = $this->_db->quoteIdentifier('thecount');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id'=>"UPPER($bugs_products.$bug_id)", new Zend_Db_Expr("COUNT(*) AS $thecount")))
            ->group("UPPER($bugs_products.$bug_id)")
            ->order("UPPER($bugs_products.$bug_id)");
        return $select;
    }

    public function testSelectFromQualified()
    {
        $this->markTestSkipped($this->getDriver() . ' does not report its schema as we expect.');
    }

    public function testSelectJoinQualified()
    {
        $this->markTestSkipped($this->getDriver() . ' does not report its schema as we expect.');
    }

    public function testSelectJoin()
    {
        $select = $this->_selectJoin();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(6, count($result));
        $this->assertEquals(4, count($result[0]));
    }

    public function testSelectJoinInner()
    {
        $select = $this->_selectJoinInner();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(6, count($result));
        $this->assertEquals(4, count($result[0]));
    }

    public function testSelectJoinRight()
    {
        $select = $this->_selectJoinRight();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(7, count($result));
        $this->assertEquals(10, count($result[0]));
        $this->assertEquals(3, $result[3]['product_id']);
        $this->assertNull($result[6]['product_id']);
    }

    public function testSelectJoinLeft()
    {
        $select = $this->_selectJoinLeft();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(7, count($result));
        $this->assertEquals(10, count($result[0]));
        $this->assertEquals(3, $result[3]['product_id']);
        $this->assertNull($result[6]['product_id']);
    }

    public function testSelectJoinCross()
    {
        $select = $this->_selectJoinCross();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(18, count($result));
        $this->assertEquals(4, count($result[0]));
    }

    public function testSelectJoinWithCorrelationName()
    {
        $select = $this->_selectJoinWithCorrelationName();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(4, count($result[0]));
    }

    public function getDriver()
    {
        return 'Firebird';
    }

}
