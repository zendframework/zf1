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
 * @version    $Id $
 */

require_once 'Zend/Db/Table/Row/TestCommon.php';




/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Table
 * @group      Zend_Db_Table_Row
 */
class Zend_Db_Table_Row_Pdo_MssqlTest extends Zend_Db_Table_Row_TestCommon
{

    public function testTableRowSaveInsert()
    {
        $this->markTestIncomplete($this->getDriver() . ': DEFAULT or NULL are not allowed as explicit identity values.');
    }

    public function testTableRowSetPrimaryKey()
    {
        $this->markTestSkipped($this->getDriver() . ' cannot update identity columns.');
    }

    public function testTableRowSaveInsertSequence()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support sequences');
    }

    public function getDriver()
    {
        return 'Pdo_Mssql';
    }

}
