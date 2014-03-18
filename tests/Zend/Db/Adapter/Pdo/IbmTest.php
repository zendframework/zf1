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
 * @see Zend_Db_Adapter_Db2Test
 */
require_once 'Zend/Db/Adapter/Db2Test.php';


/**
 * @see Zend_Db_Adapter_Pdo_Ibm
 */
require_once 'Zend/Db/Adapter/Pdo/Ibm.php';


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Adapter
 */
class Zend_Db_Adapter_Pdo_IbmTest extends Zend_Db_Adapter_Db2Test
{
    public function getDriver()
    {
        return 'Pdo_Ibm';
    }

    public function testAdapterTransactionCommit()
    {
        $server = $this->_util->getServer();

        if ($server == 'IDS') {
            $this->markTestIncomplete('IDS needs special consideration for transactions');
        } else {
            parent::testAdapterTransactionCommit();
        }
    }

    public function testAdapterTransactionRollback()
    {
        $server = $this->_util->getServer();

        if ($server == 'IDS') {
            $this->markTestIncomplete('IDS needs special consideration for transactions');
        } else {
            parent::testAdapterTransactionCommit();
        }
    }

    public function testAdapterLimitInvalidArgumentException()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $sql = $this->_db->limit("SELECT * FROM $products", 0);

        $stmt = $this->_db->query($sql);
        $result = $stmt->fetchAll();

        $this->assertEquals(0, count($result), 'Expecting to see 0 rows returned');

        try {
            $sql = $this->_db->limit("SELECT * FROM $products", 1, -1);
            $this->fail('Expected to catch Zend_Db_Adapter_Exception');
        } catch (Zend_Exception $e) {
            $this->assertTrue($e instanceof Zend_Db_Adapter_Exception,
                'Expecting object of type Zend_Db_Adapter_Exception, got '.get_class($e));
        }
    }

    /**
     * Used by _testAdapterOptionCaseFoldingNatural()
     * DB2 returns identifiers in uppercase naturally,
     * while IDS does not
     */
    protected function _testAdapterOptionCaseFoldingNaturalIdentifier()
    {
        $server = $this->_util->getServer();

        if ($server == 'DB2') {
            return 'CASE_FOLDED_IDENTIFIER';
        }
        return 'case_folded_identifier';
    }
}
