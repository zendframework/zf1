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
 * @see Zend_Db_Table_Select_TestCommon
 */
require_once 'Zend/Db/Table/Select/TestCommon.php';





/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Table
 * @group      Zend_Db_Table_Select
 */
class Zend_Db_Table_Select_Pdo_IbmTest extends Zend_Db_Table_Select_TestCommon
{

    public function getDriver()
    {
        return 'Pdo_Ibm';
    }

    public function testSelectGroupByExpr()
    {
       $server = $this->_util->getServer();

        if ($server == 'IDS') {
            $this->markTestIncomplete('IDS does not support this SQL syntax');
        } else {
            parent::testSelectGroupByExpr();
        }
    }

    public function testSelectGroupByAutoExpr()
    {
       $server = $this->_util->getServer();

        if ($server == 'IDS') {
            $this->markTestIncomplete('IDS does not support this SQL syntax');
        } else {
            parent::testSelectGroupByAutoExpr();
        }
    }

    public function testSelectJoinCross()
    {
        $this->markTestSkipped($this->getDriver() . ' adapter support for CROSS JOIN not yet available');
    }
}
