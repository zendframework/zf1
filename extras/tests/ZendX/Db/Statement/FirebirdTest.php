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

require_once 'Zend/Db/Statement/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class ZendX_Db_Statement_FirebirdTest extends Zend_Db_Statement_TestCommon
{

    public function testStatementBindParamByName()
    {
        $this->markTestSkipped($this->getDriver() . ' don\' suport Param by Name');
    }

    public function testStatementBindValueByName()
    {
        $this->markTestSkipped($this->getDriver() . ' don\'t suport Param by Name');
    }

    public function testStatementGetColumnMeta()
    {
        $this->markTestIncomplete($this->getDriver() . ' has not implemented getColumnMeta() yet [ZF-1424]');
    }

    public function getDriver()
    {
        return 'Firebird';
    }

}
