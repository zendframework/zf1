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
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Log_Writer_AbstractTest::main');
}

/** Zend_Log_Writer_Abstract */
require_once 'Zend/Log/Writer/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Log
 */
class Zend_Log_Writer_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Log_Writer_Abstract
     */
    protected $_writer;

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    protected function setUp()
    {
        $this->_writer = new Zend_Log_Writer_AbstractTest_Concrete();
    }

    /**
     * @group ZF-6085
     */
    public function testSetFormatter()
    {
        if (version_compare(phpversion(), '7', '>=')) {
            $this->markTestSkipped('Invalid typehinting is PHP Fatal error in PHP7+');
        }

        require_once 'Zend/Log/Formatter/Simple.php';
        $this->_writer->setFormatter(new Zend_Log_Formatter_Simple());
        $this->setExpectedException('PHPUnit_Framework_Error');
        $this->_writer->setFormatter(new StdClass());
    }

    public function testAddFilter()
    {
        $this->_writer->addFilter(1);
        require_once 'Zend/Log/Filter/Message.php';
        $this->_writer->addFilter(new Zend_Log_Filter_Message('/mess/'));
        $this->setExpectedException('Zend_Log_Exception');
        $this->_writer->addFilter(new StdClass());
    }

    /**
     * @group ZF-8953
     */
    public function testFluentInterface()
    {
        require_once 'Zend/Log/Formatter/Simple.php';
        $instance = $this->_writer->addFilter(1)
                                  ->setFormatter(new Zend_Log_Formatter_Simple());

        $this->assertTrue($instance instanceof Zend_Log_Writer_AbstractTest_Concrete);
    }
}

class Zend_Log_Writer_AbstractTest_Concrete extends Zend_Log_Writer_Abstract
{
    protected function _write($event)
    {
    }

    static public function factory($config)
    {
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Log_Writer_AbstractTest::main') {
    Zend_Log_Writer_AbstractTest::main();
}
