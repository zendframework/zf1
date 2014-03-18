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
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once "Zend/Test/DbAdapter.php";

require_once "Zend/Test/PHPUnit/Db/Operation/Truncate.php";

require_once "PHPUnit/Extensions/Database/DataSet/FlatXmlDataSet.php";

require_once 'PHPUnit/Extensions/Database/DataSet/IDataSet.php';
require_once 'PHPUnit/Extensions/Database/DB/IDatabaseConnection.php';

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class Zend_Test_PHPUnit_Db_Operation_TruncateTest extends PHPUnit_Framework_TestCase
{
    private $operation = null;

    public function setUp()
    {
        $this->operation = new Zend_Test_PHPUnit_Db_Operation_Truncate();
    }

    public function testTruncateTablesExecutesAdapterQuery()
    {
        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(dirname(__FILE__)."/_files/truncateFixture.xml");

        $testAdapter = $this->getMock('Zend_Test_DbAdapter');
        $testAdapter->expects($this->at(0))
                    ->method('quoteIdentifier')
                    ->with('bar')->will($this->returnValue('bar'));
        $testAdapter->expects($this->at(1))
                    ->method('query')
                    ->with('TRUNCATE bar');
        $testAdapter->expects($this->at(2))
                    ->method('quoteIdentifier')
                    ->with('foo')->will($this->returnValue('foo'));
        $testAdapter->expects($this->at(3))
                    ->method('query')
                    ->with('TRUNCATE foo');

        $connection = new Zend_Test_PHPUnit_Db_Connection($testAdapter, "schema");

        $this->operation->execute($connection, $dataSet);
    }

    public function testTruncateTableInvalidQueryTransformsException()
    {
        $this->setExpectedException('PHPUnit_Extensions_Database_Operation_Exception');

        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(dirname(__FILE__)."/_files/insertFixture.xml");

        $testAdapter = $this->getMock('Zend_Test_DbAdapter');
        $testAdapter->expects($this->any())->method('query')->will($this->throwException(new Exception()));

        $connection = new Zend_Test_PHPUnit_Db_Connection($testAdapter, "schema");

        $this->operation->execute($connection, $dataSet);
    }

    public function testInvalidConnectionGivenThrowsException()
    {
        $this->setExpectedException("Zend_Test_PHPUnit_Db_Exception");

        $dataSet = $this->getMock('PHPUnit_Extensions_Database_DataSet_IDataSet');
        $connection = $this->getMock('PHPUnit_Extensions_Database_DB_IDatabaseConnection');

        $this->operation->execute($connection, $dataSet);
    }

    /**
     * @group ZF-7936
     */
    public function testTruncateAppliedToTablesInReverseOrder()
    {
        $testAdapter = new Zend_Test_DbAdapter();
        $connection = new Zend_Test_PHPUnit_Db_Connection($testAdapter, "schema");

        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(dirname(__FILE__)."/_files/truncateFixture.xml");

        $this->operation->execute($connection, $dataSet);

        $profiler = $testAdapter->getProfiler();
        $queries = $profiler->getQueryProfiles();

        $this->assertEquals(2, count($queries));
        $this->assertContains('bar', $queries[0]->getQuery());
        $this->assertContains('foo', $queries[1]->getQuery());
    }
}
