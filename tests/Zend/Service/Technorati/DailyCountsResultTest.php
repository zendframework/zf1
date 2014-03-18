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
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TestCase.php';

/**
 * @see Zend_Service_Technorati_DailyCountsResult
 */
require_once 'Zend/Service/Technorati/DailyCountsResult.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class Zend_Service_Technorati_DailyCountsResultTest extends Zend_Service_Technorati_TestCase
{
    public function setUp()
    {
        $this->domElements = self::getTestFileElementsAsDom('TestDailyCountsResultSet.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend_Service_Technorati_CosmosResult', array($this->domElements->item(0)));
    }

    public function testConstructThrowsExceptionWithInvalidDom()
    {
        $this->_testConstructThrowsExceptionWithInvalidDom('Zend_Service_Technorati_DailyCountsResult', 'DOMElement');
    }

    public function testDailyCountsResult()
    {
        $object = new Zend_Service_Technorati_DailyCountsResult($this->domElements->item(1));

        // check properties
        $this->assertTrue($object->getDate() instanceof Zend_Date);
        $this->assertEquals(new Zend_Date(strtotime('2007-11-13')), $object->getDate());
        $this->assertTrue(is_int($object->getCount()));
        $this->assertEquals(54414, $object->getCount());
    }

    public function testDailyCountsResultSerialization()
    {
        $this->_testResultSerialization(new Zend_Service_Technorati_DailyCountsResult($this->domElements->item(0)));
    }
}
