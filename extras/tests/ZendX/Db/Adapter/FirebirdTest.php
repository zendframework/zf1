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

/**
 * @see Zend_Db_Adapter_TestCommon
 */
require_once 'Zend/Db/Adapter/TestCommon.php';

/**
 * @see Zend_Db_Adapter_Firebird
 */
require_once 'ZendX/Db/Adapter/Firebird.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class ZendX_Db_Adapter_FirebirdTest extends Zend_Db_Adapter_TestCommon
{

    protected $_numericDataTypes = array(
        Zend_Db::INT_TYPE    => Zend_Db::INT_TYPE,
        Zend_Db::BIGINT_TYPE => Zend_Db::BIGINT_TYPE,
        Zend_Db::FLOAT_TYPE  => Zend_Db::FLOAT_TYPE,
        'INT'                => Zend_Db::INT_TYPE,
        'INTEGER'            => Zend_Db::INT_TYPE,
        'SMALLINT'           => Zend_Db::INT_TYPE,
        'BIGINT'             => Zend_Db::BIGINT_TYPE,
        'INT64'              => Zend_Db::BIGINT_TYPE,
        'DECIMAL'            => Zend_Db::FLOAT_TYPE,
        'DOUBLE'             => Zend_Db::FLOAT_TYPE,
        'DOUBLE PRECISION'   => Zend_Db::FLOAT_TYPE,
        'NUMERIC'            => Zend_Db::FLOAT_TYPE,
        'FLOAT'              => Zend_Db::FLOAT_TYPE
    );


    public function testAdapterDescribeTablePrimaryAuto()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support auto-increment');
    }

    public function testAdapterInsert()
    {
        $row = array (
            'product_id'   => $this->_db->nextSequenceId('zfproducts_seq'),
            'product_name' => 'Solaris',
        );
        $rowsAffected = $this->_db->insert('zfproducts', $row);
        $this->assertEquals(1, $rowsAffected);
        $lastInsertId = $this->_db->lastInsertId('zfproducts', null); // implies 'zfproducts_seq'
        $lastSequenceId = $this->_db->lastSequenceId('zfproducts_seq');
        $this->assertEquals('4', (string) $lastInsertId, 'Expected new id to be 4');
        $this->assertEquals('4', (string) $lastSequenceId, 'Expected new id to be 4');
    }

    /**
     * test that quote() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteSingleQuote()
    {
        $string = "St John's Wort";
        $value = $this->_db->quote($string);
        $this->assertEquals("'St John''s Wort'", $value);
    }

    /**
     * test that quoteTableAs() accepts a string and an alias,
     * and returns each as delimited identifiers.
     * Oracle does not want the 'AS' in between.
     */
    public function testAdapterQuoteTableAs()
    {
        $string = "foo";
        $alias = "bar";
        $value = $this->_db->quoteTableAs($string, $alias);
        $this->assertEquals('"foo" "bar"', $value);
    }

    /**
     * test that quote() escapes a double-quote
     * character in a string.
     */
    public function testAdapterQuoteDoubleQuote()
    {
        $value = $this->_db->quote('St John"s Wort');
        $this->assertEquals("'St John\"s Wort'", $value);
    }

    /**
     * Test that quote() takes an array and returns
     * an imploded string of comma-separated, quoted elements.
     */
    public function testAdapterQuoteArray()
    {
        $array = array("it's", 'all', 'right!');
        $value = $this->_db->quote($array);
        $this->assertEquals("'it''s', 'all', 'right!'", $value);
    }

    /**
     * test that quoteInto() escapes a single-quote
     * character in a string.
     */
    public function testAdapterQuoteIntoSingleQuote()
    {
        $value = $this->_db->quoteInto('id = ?', 'St John\'s Wort');
        $this->assertEquals("id = 'St John''s Wort'", $value);
    }

	/**
     * test that quoteInto() escapes a double-quote
     * character in a string.
     */
    public function testAdapterQuoteIntoDoubleQuote()
    {
        $value = $this->_db->quoteInto('id=?', 'St John"s Wort');
        $this->assertEquals("id='St John\"s Wort'", $value);
    }

    public function testZF2059()
    {
		$this->markTestSkipped($this->getDriver() . ' not affected by ZF-2059');
    }


	// Deffers from others RDBMS, Firebird always is in a transaction, and for Zend_Db the default
	// transaction Isolation is snapshot, changes made by other transaction is only visible with new
    // transaction, commiting retaining or rollback retaining
    public function testAdapterTransactionCommit()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        // use our default connection as the Connection1
        $dbConnection1 = $this->_db;

        // create a second connection to the same database
        $dbConnection2 = Zend_Db::factory($this->getDriver(), $this->_util->getParams());
        $dbConnection2->getConnection();

        // notice the number of rows in connection 2
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(4, $count, 'Expecting to see 4 rows in bugs table (step 1)');

        // start an explicit transaction in connection 1
        $dbConnection1->beginTransaction();

        // delete a row in connection 1
        $rowsAffected = $dbConnection1->delete(
            'zfbugs',
            "$bug_id = 1"
        );
        $this->assertEquals(1, $rowsAffected);

        // we should still see all rows in connection 2
        // because the DELETE has not been committed yet
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(4, $count, 'Expecting to still see 4 rows in bugs table (step 2); perhaps Adapter is still in autocommit mode?');

        // commit the DELETE
        $dbConnection1->commit();

        // now we should see one fewer rows in connection 2
		
		$dbConnection2->commit();
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(3, $count, 'Expecting to see 3 rows in bugs table after DELETE (step 3)');

        // delete another row in connection 1
        $rowsAffected = $dbConnection1->delete(
            'zfbugs',
            "$bug_id = 2"
        );
        $this->assertEquals(1, $rowsAffected);

        // we should see results immediately, because
        // the db connection returns to auto-commit mode
        $count = $dbConnection2->fetchOne("SELECT COUNT(*) FROM $bugs");
        $this->assertEquals(2, $count);
    }

    /**
     * Used by _testAdapterOptionCaseFoldingNatural()
     * DB2, Oracle and Firebird return identifiers in uppercase naturally,
     * so those test suites will override this method.
     */
    protected function _testAdapterOptionCaseFoldingNaturalIdentifier()
    {
        return 'CASE_FOLDED_IDENTIFIER';
    }

    public function testAdapterOptionCaseFoldingLower()
    {
        $this->markTestSkipped($this->getDriver() . ' always return UPPERCASE Natural Identifiers');
    }

    public function getDriver()
    {
        return 'Firebird';
    }

}
