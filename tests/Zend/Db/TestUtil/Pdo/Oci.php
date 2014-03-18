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
 * @see Zend_Db_TestUtil_Pdo_Common
 */
require_once 'Zend/Db/TestUtil/Pdo/Common.php';




/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_TestUtil_Pdo_Oci extends Zend_Db_TestUtil_Pdo_Common
{

    public function setUp(Zend_Db_Adapter_Abstract $db)
    {
        $this->_db = $db;
        $this->createSequence('zfbugs_seq');
        $this->createSequence('zfproducts_seq');
        parent::setUp($db);
    }

    public function getParams(array $constants = array())
    {
        $constants = array (
            'host'     => 'TESTS_ZEND_DB_ADAPTER_ORACLE_HOSTNAME',
            'username' => 'TESTS_ZEND_DB_ADAPTER_ORACLE_USERNAME',
            'password' => 'TESTS_ZEND_DB_ADAPTER_ORACLE_PASSWORD',
            'dbname'   => 'TESTS_ZEND_DB_ADAPTER_ORACLE_SID'
        );
        return parent::getParams($constants);
    }

    public function getSqlType($type)
    {
        if (preg_match('/VARCHAR(.*)/', $type, $matches)) {
            return 'VARCHAR2' . $matches[1];
        }
        if ($type == 'IDENTITY') {
            return 'NUMBER(11) PRIMARY KEY';
        }
        if ($type == 'INTEGER') {
            return 'NUMBER(11)';
        }
        if ($type == 'DATETIME') {
            return 'TIMESTAMP';
        }
        return $type;
    }

    protected function _getSqlCreateTable($tableName)
    {
        $tableList = $this->_db->fetchCol('SELECT UPPER(TABLE_NAME) FROM ALL_TABLES '
            . $this->_db->quoteInto(' WHERE UPPER(TABLE_NAME) = UPPER(?)', $tableName)
        );
        if (in_array(strtoupper($tableName), $tableList)) {
            return null;
        }
        return 'CREATE TABLE ' . $this->_db->quoteIdentifier($tableName, true);
    }

    protected function _getSqlDropTable($tableName)
    {
        $tableList = $this->_db->fetchCol('SELECT UPPER(TABLE_NAME) FROM ALL_TABLES '
            . $this->_db->quoteInto(' WHERE UPPER(TABLE_NAME) = UPPER(?)', $tableName)
        );
        if (in_array(strtoupper($tableName), $tableList)) {
            return 'DROP TABLE ' . $this->_db->quoteIdentifier($tableName, true);
        }
        return null;
    }

    protected function _getSqlCreateSequence($sequenceName)
    {
        $seqList = $this->_db->fetchCol('SELECT UPPER(SEQUENCE_NAME) FROM ALL_SEQUENCES '
            . $this->_db->quoteInto(' WHERE UPPER(SEQUENCE_NAME) = UPPER(?)', $sequenceName)
        );
        if (in_array(strtoupper($sequenceName), $seqList)) {
            return null;
        }
        return 'CREATE SEQUENCE ' . $this->_db->quoteIdentifier($sequenceName, true);
    }

    protected function _getSqlDropSequence($sequenceName)
    {
        $seqList = $this->_db->fetchCol('SELECT UPPER(SEQUENCE_NAME) FROM ALL_SEQUENCES '
            . $this->_db->quoteInto(' WHERE UPPER(SEQUENCE_NAME) = UPPER(?)', $sequenceName)
        );
        if (in_array(strtoupper($sequenceName), $seqList)) {
            return 'DROP SEQUENCE ' . $this->_db->quoteIdentifier($sequenceName, true);
        }
        return null;
    }

    protected function _getDataBugs()
    {
        $data = parent::_getDataBugs();
        foreach ($data as &$row) {
            $row['bug_id'] = new Zend_Db_Expr($this->_db->quoteIdentifier('zfbugs_seq', true).'.NEXTVAL');
            $row['created_on'] = new Zend_Db_Expr($this->_db->quoteInto('DATE ?', $row['created_on']));
            $row['updated_on'] = new Zend_Db_Expr($this->_db->quoteInto('DATE ?', $row['updated_on']));
        }
        return $data;
    }

    protected function _getDataDocuments()
    {
        $data = parent::_getDataDocuments();
        foreach ($data as &$row) {
            $quoted = $this->_db->quote($row['doc_clob']);
            $hex = bin2hex($row['doc_clob']);
            $row['doc_clob'] = new Zend_Db_Expr("TO_CLOB($quoted)");
            $row['doc_blob'] = new Zend_Db_Expr("TO_BLOB(HEXTORAW('$hex'))");

        }
        return $data;
    }

    protected function _getDataProducts()
    {
        $data = parent::_getDataProducts();
        foreach ($data as &$row) {
            $row['product_id'] = new Zend_Db_Expr($this->_db->quoteIdentifier('zfproducts_seq', true).'.NEXTVAL');
        }
        return $data;
    }

    protected function _getSqlCreateView($viewName)
    {
        return 'CREATE OR REPLACE VIEW ' . $this->_db->quoteIdentifier($viewName, true);
    }

    /**
     * ZF-4330: schemas on Oracle are specifics:
     * "A schema is owned by a database user and has the same name as that user."
     * http://download-east.oracle.com/docs/cd/B19306_01/server.102/b14220/intro.htm#sthref69
     * @return string
     */
    public function getSchema()
    {
        $param = $this->getParams();
        return $param['username'];
    }

}
