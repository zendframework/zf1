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
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Db_Adapter_Pdo_Sqlite
 */
require_once 'Zend/Db/Adapter/Pdo/Sqlite.php';


/**
 * This is required because Zend_Db_Adapter_Pdo_Sqlite uses Zend_Db constants
 * but does not load the file containing the Zend_Db class.
 *
 * @see Zend_Db
 */
require_once 'Zend/Db.php';


/**
 * @see Zend_Auth_Adapter_DbTable
 */
require_once 'Zend/Auth/Adapter/DbTable.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Auth
 * @group      Zend_Db_Table
 */
class Zend_Auth_Adapter_DbTable_BasicSqliteTest extends PHPUnit_Framework_TestCase
{
    /**
     * Sqlite database connection
     *
     * @var Zend_Db_Adapter_Pdo_Sqlite
     */
    protected $_db = null;

    /**
     * Database table authentication adapter
     *
     * @var Zend_Auth_Adapter_DbTable
     */
    protected $_adapter = null;

    /**
     * Set up test configuration
     *
     * @return void
     */
    public function setUp()
    {
        $this->_setupDbAdapter();
        $this->_setupAuthAdapter();
    }

    public function tearDown()
    {
        $this->_adapter = null;
        $this->_db->query('DROP TABLE [users]');
        $this->_db = null;
    }

    /**
     * Ensures expected behavior for authentication success
     *
     * @return void
     */
    public function testAuthenticateSuccess()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $result = $this->_adapter->authenticate();
        $this->assertTrue($result->isValid());
    }

    /**
     * Ensures expected behavior for authentication success
     *
     * @return void
     */
    public function testAuthenticateSuccessWithTreatment()
    {
        $this->_adapter = new Zend_Auth_Adapter_DbTable($this->_db, 'users', 'username', 'password', '?');
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $result = $this->_adapter->authenticate();
        $this->assertTrue($result->isValid());
    }

    /**
     * Ensures expected behavior for for authentication failure
     * reason: Identity not found.
     *
     */
    public function testAuthenticateFailureIdentityNotFound()
    {
        $this->_adapter->setIdentity('non_existent_username');
        $this->_adapter->setCredential('my_password');

        try {
            $result = $this->_adapter->authenticate();
            $this->assertEquals(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
        } catch (Zend_Auth_Exception $e) {
            $this->fail('Exception should have been thrown');
        }
    }

    /**
     * Ensures expected behavior for for authentication failure
     * reason: Identity not found.
     *
     */
    public function testAuthenticateFailureIdentityAmbigious()
    {
        $sql_insert = 'INSERT INTO users (username, password, real_name) VALUES ("my_username", "my_password", "My Real Name")';
        $this->_db->query($sql_insert);

        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');

        try {
            $result = $this->_adapter->authenticate();
            $this->assertEquals(Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS, $result->getCode());
        } catch (Zend_Auth_Exception $e) {
            $this->fail('Exception should have been thrown');
        }
    }

    /**
     * Ensures expected behavior for authentication failure because of a bad password
     *
     * @return void
     */
    public function testAuthenticateFailureInvalidCredential()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password_bad');
        $result = $this->_adapter->authenticate();
        $this->assertFalse($result->isValid());
    }

    /**
     * Ensures that getResultRowObject() works for successful authentication
     *
     * @return void
     */
    public function testGetResultRow()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $result = $this->_adapter->authenticate();
        $resultRow = $this->_adapter->getResultRowObject();
        $this->assertEquals($resultRow->username, 'my_username');
    }

    /**
     * Ensure that ResultRowObject returns only what told to be included
     *
     */
    public function testGetSpecificResultRow()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $result = $this->_adapter->authenticate();
        $resultRow = $this->_adapter->getResultRowObject(array('username', 'real_name'));
        $this->assertEquals('O:8:"stdClass":2:{s:8:"username";s:11:"my_username";s:9:"real_name";s:12:"My Real Name";}', serialize($resultRow));
    }

    /**
     * Ensure that ResultRowObject returns an object has specific omissions
     *
     */
    public function testGetOmittedResultRow()
    {
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $result = $this->_adapter->authenticate();
        $resultRow = $this->_adapter->getResultRowObject(null, 'password');
        $this->assertEquals('O:8:"stdClass":3:{s:2:"id";s:1:"1";s:8:"username";s:11:"my_username";s:9:"real_name";s:12:"My Real Name";}', serialize($resultRow));
    }

    /**
     * @group ZF-5957
     */
    public function testAdapterCanReturnDbSelectObject()
    {
        $this->assertTrue($this->_adapter->getDbSelect() instanceof Zend_Db_Select);
    }

    /**
     * @group ZF-5957
     */
    public function testAdapterCanUseModifiedDbSelectObject()
    {
        $this->_db->getProfiler()->setEnabled(true);
        $select = $this->_adapter->getDbSelect();
        $select->where('1 = 1');
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $this->_adapter->authenticate();
        $profiler = $this->_db->getProfiler();
        $this->assertEquals(
            'SELECT "users".*, (CASE WHEN "password" = \'my_password\' THEN 1 ELSE 0 END) AS "zend_auth_credential_match" FROM "users" WHERE (1 = 1) AND ("username" = \'my_username\')',
            $profiler->getLastQueryProfile()->getQuery()
            );
    }

    /**
     * @group ZF-5957
     */
    public function testAdapterReturnsASelectObjectWithoutAuthTimeModificationsAfterAuth()
    {
        $select = $this->_adapter->getDbSelect();
        $select->where('1 = 1');
        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $this->_adapter->authenticate();
        $selectAfterAuth = $this->_adapter->getDbSelect();
        $whereParts = $selectAfterAuth->getPart(Zend_Db_Select::WHERE);
        $this->assertEquals(1, count($whereParts));
        $this->assertEquals('(1 = 1)', array_pop($whereParts));
    }

    /**
     * Ensure that exceptions are caught
     *
     * @expectedException Zend_Auth_Exception
     */
    public function testCatchExceptionNoTable()
    {
        $adapter = new Zend_Auth_Adapter_DbTable($this->_db);
        $result = $adapter->authenticate();
        //  $this->assertEquals($e->getMessage(), 'A table must be supplied for the Zend_Auth_Adapter_DbTable authentication adapter.');
    }

    /**
     * Ensure that exceptions are caught
     *
     * @expectedException Zend_Auth_Exception
     */
    public function testCatchExceptionNoIdentityColumn()
    {
        $adapter = new Zend_Auth_Adapter_DbTable($this->_db, 'users');
        $result = $adapter->authenticate();
        // $this->assertEquals($e->getMessage(), 'An identity column must be supplied for the Zend_Auth_Adapter_DbTable authentication adapter.');
    }

    /**
     * Ensure that exceptions are caught
     *
     * @expectedException Zend_Auth_Exception
     */
    public function testCatchExceptionNoCredentialColumn()
    {
        $adapter = new Zend_Auth_Adapter_DbTable($this->_db, 'users', 'username');
        $result = $adapter->authenticate();
        // $this->assertEquals($e->getMessage(), 'A credential column must be supplied for the Zend_Auth_Adapter_DbTable authentication adapter.');
    }

    /**
     * Ensure that exceptions are caught
     *
     * @expectedException Zend_Auth_Exception
     */
    public function testCatchExceptionNoIdentity()
    {
        $result = $this->_adapter->authenticate();
        // $this->assertEquals($e->getMessage(), 'A value for the identity was not provided prior to authentication with Zend_Auth_Adapter_DbTable.');
    }

    /**
     * Ensure that exceptions are caught
     *
     * @expectedException Zend_Auth_Exception
     */
    public function testCatchExceptionNoCredential()
    {
        $this->_adapter->setIdentity('my_username');
        $result = $this->_adapter->authenticate();
        // $this->assertEquals($e->getMessage(), 'A credential value was not provided prior to authentication with Zend_Auth_Adapter_DbTable.');
    }

    /**
     * Ensure that exceptions are caught
     *
     * @expectedException Zend_Auth_Exception
     */
    public function testCatchExceptionBadSql()
    {
        $this->_adapter->setTableName('bad_table_name');
        $this->_adapter->setIdentity('value');
        $this->_adapter->setCredential('value');
        $result = $this->_adapter->authenticate();
        // $this->assertEquals($e->getMessage(), 'The supplied parameters to Zend_Auth_Adapter_DbTable failed to produce a valid sql statement, please check table and column names for validity.');
    }

    /**
     *
     * @group ZF-3068
     */
    public function testDbTableAdapterUsesCaseFolding()
    {
        $this->tearDown();
        $this->_setupDbAdapter(array(Zend_Db::CASE_FOLDING => Zend_Db::CASE_UPPER));
        $this->_setupAuthAdapter();

        $this->_adapter->setIdentity('my_username');
        $this->_adapter->setCredential('my_password');
        $this->_db->foldCase(Zend_Db::CASE_UPPER);
        $this->_adapter->authenticate();
    }


    /**
     * Test fallback to default database adapter, when no such adapter set
     *
     * @expectedException Zend_Auth_Adapter_Exception
     * @group ZF-7510
     */
    public function testAuthenticateWithDefaultDbAdapterNoAdapterException()
    {
        require_once('Zend/Db/Table/Abstract.php');
        // preserve default db adapter between cases
        $tmp = Zend_Db_Table_Abstract::getDefaultAdapter();

        // make sure that no default adapter exists
        Zend_Db_Table_Abstract::setDefaultAdapter(null);
        try {
            $this->_adapter = new Zend_Auth_Adapter_DbTable();
        } catch (Exception $e) {
            $this->assertContains('No database adapter present', $e->getMessage());
            throw $e;
        }

        // restore adapter
        Zend_Db_Table_Abstract::setDefaultAdapter($tmp);
    }

    /**
     * Test fallback to default database adapter
     *
     * @group ZF-7510
     */
    public function testAuthenticateWithDefaultDbAdapter()
    {
        require_once('Zend/Db/Table/Abstract.php');
        // preserve default adapter between cases
        $tmp = Zend_Db_Table_Abstract::getDefaultAdapter();

        // make sure that default db adapter exists
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_db);

        // check w/o passing adapter
        $this->_adapter = new Zend_Auth_Adapter_DbTable();
        $this->_adapter
            ->setTableName('users')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password')
            ->setTableName('users')
            ->setIdentity('my_username')
            ->setCredential('my_password');
        $result = $this->_adapter->authenticate();
        $this->assertTrue($result->isValid());

        // restore adapter
        Zend_Db_Table_Abstract::setDefaultAdapter($tmp);
    }
    /**
     * Test to see same usernames with different passwords can not authenticate
     * when flag is not set. This is the current state of
     * Zend_Auth_Adapter_DbTable (up to ZF 1.10.6)
     *
     * @group   ZF-7289
     */
    public function testEqualUsernamesDifferentPasswordShouldNotAuthenticateWhenFlagIsNotSet()
    {
        $this->_db->insert('users', array (
            'username' => 'my_username',
            'password' => 'my_otherpass',
            'real_name' => 'Test user 2',
        ));

        // test if user 1 can authenticate
        $this->_adapter->setIdentity('my_username')
                       ->setCredential('my_password');
        $result = $this->_adapter->authenticate();
        $this->assertTrue(in_array('More than one record matches the supplied identity.',
            $result->getMessages()));
        $this->assertFalse($result->isValid());
    }
    /**
     * Test to see same usernames with different passwords can authenticate when
     * a flag is set
     *
     * @group   ZF-7289
     */
    public function testEqualUsernamesDifferentPasswordShouldAuthenticateWhenFlagIsSet()
    {
        $this->_db->insert('users', array (
            'username' => 'my_username',
            'password' => 'my_otherpass',
            'real_name' => 'Test user 2',
        ));

        // test if user 1 can authenticate
        $this->_adapter->setIdentity('my_username')
                       ->setCredential('my_password')
                       ->setAmbiguityIdentity(true);
        $result = $this->_adapter->authenticate();
        $this->assertFalse(in_array('More than one record matches the supplied identity.',
            $result->getMessages()));
        $this->assertTrue($result->isValid());
        $this->assertEquals('my_username', $result->getIdentity());

        $this->_adapter = null;
        $this->_setupAuthAdapter();

        // test if user 2 can authenticate
        $this->_adapter->setIdentity('my_username')
                       ->setCredential('my_otherpass')
                       ->setAmbiguityIdentity(true);
        $result2 = $this->_adapter->authenticate();
        $this->assertFalse(in_array('More than one record matches the supplied identity.',
            $result->getMessages()));
        $this->assertTrue($result->isValid());
        $this->assertEquals('my_username', $result->getIdentity());
    }


    protected function _setupDbAdapter($optionalParams = array())
    {
        $params = array('dbname' => TESTS_ZEND_AUTH_ADAPTER_DBTABLE_PDO_SQLITE_DATABASE);

        if (!empty($optionalParams)) {
            $params['options'] = $optionalParams;
        }

        $this->_db = new Zend_Db_Adapter_Pdo_Sqlite($params);

        $sqlCreate = 'CREATE TABLE [users] ( '
                   . '[id] INTEGER  NOT NULL PRIMARY KEY, '
                   . '[username] VARCHAR(50) NOT NULL, '
                   . '[password] VARCHAR(32) NULL, '
                   . '[real_name] VARCHAR(150) NULL)';
        $this->_db->query($sqlCreate);

        $sqlInsert = 'INSERT INTO users (username, password, real_name) '
                   . 'VALUES ("my_username", "my_password", "My Real Name")';
        $this->_db->query($sqlInsert);
    }

    protected function _setupAuthAdapter()
    {
        $this->_adapter = new Zend_Auth_Adapter_DbTable($this->_db, 'users', 'username', 'password');
    }


}


class Zend_Auth_Adapter_DbTable_BasicSqliteTest_Skip extends Zend_Auth_Adapter_DbTable_BasicSqliteTest
{
    public function setUp()
    {
        $this->markTestSkipped('Zend_Auth_Adapter_DbTable Sqlite tests are not enabled in TestConfiguration.php');
    }
}
