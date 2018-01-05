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
 * @see Zend_Db_Select_TestCommon
 */
require_once 'Zend/Db/Select/TestCommon.php';





/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Select
 */
class Zend_Db_Select_Pdo_MysqlTest extends Zend_Db_Select_TestCommon
{

    public function getDriver()
    {
        return 'Pdo_Mysql';
    }

    public function testSelectWithForceIndex()
    {
        $select = $this->_db->select();
        $select->from(array ('p' => 'product'))
            ->forceIndex('IX_this_index_does_not_exist');

        $expected = 'SELECT `p`.* FROM `product` AS `p` FORCE INDEX(IX_this_index_does_not_exist)';
        $this->assertEquals($expected, $select->assemble(),
            'Select with force index failed');
    }

    public function testSelectWithUseIndex()
    {
        $select = $this->_db->select();
        $select->from(array ('p' => 'product'))
            ->useIndex('IX_this_index_does_not_exist');

        $expected = 'SELECT `p`.* FROM `product` AS `p` USE INDEX(IX_this_index_does_not_exist)';
        $this->assertEquals($expected, $select->assemble(),
            'Select with use index failed');
    }

    public function testSelectWithIgnoreIndex()
    {
        $select = $this->_db->select();
        $select->from(array ('p' => 'product'))
            ->ignoreIndex('IX_this_index_does_not_exist');

        $expected = 'SELECT `p`.* FROM `product` AS `p` IGNORE INDEX(IX_this_index_does_not_exist)';
        $this->assertEquals($expected, $select->assemble(),
            'Select with ignore index failed');
    }

    public function testSelectWithJoinAndForceIndex()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->forceIndex('IX_this_index_does_not_exist')
            ->join('zfbugs_products', "$products.$product_id = $bugs_products.$product_id", array());

        $expected = 'SELECT `zfproducts`.* FROM `zfproducts` FORCE INDEX(IX_this_index_does_not_exist)' .
            "\n" . ' INNER JOIN `zfbugs_products` ON `zfproducts`.`product_id` = `zfbugs_products`.`product_id`';
        $this->assertEquals($expected, $select->assemble(),
            'Select with join and force index failed');
    }
}
