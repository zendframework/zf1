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
 * @package    ZendX_Console
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_ProgressBar_Adapter_ConsoleTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "ZendX_Console_Process_UnixTest::main");
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * ZendX_Console_Process_Unix
 */
require_once 'ZendX/Console/Process/Unix.php';

/**
 * @category   Zend
 * @package    ZendX_Console
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendX_Console_Process_UnixTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("ZendX_Console_Process_UnixTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    public function setUp()
    {
        if (substr(PHP_OS, 0, 3) === 'WIN') {
            $this->markTestSkipped('Cannot run on Windows');
        } else if (!in_array(substr(PHP_SAPI, 0, 3), array('cli', 'cgi'))) {
            $this->markTestSkipped('Can only run on CLI or CGI enviroment');
        } else if (!function_exists('shmop_open')) {
            $this->markTestSkipped('shmop_* functions are required');
        } else if (!function_exists('pcntl_fork')) {
            $this->markTestSkipped('pcntl_* functions are required');
        } else if (!function_exists('posix_kill')) {
            $this->markTestSkipped('posix_* functions are required');
        }
    }
    
    public function testStop()
    {
        $startTime = microtime(true);
        
        $process = new sleepingProcess();
        
        $process->start();
        $process->stop();

        $diffTime = round(microtime(true) - $startTime);
        
        $this->assertEquals(0, $diffTime);
    }
    
    public function testAutomaticEnding()
    {
        $startTime = microtime(true);
        
        $process = new simpleProcess();
        
        $process->start();

        do {
            usleep(10);
            $diffTime = round(microtime(true) - $startTime);
        } while ($process->isRunning() && $diffTime < 2);
        
        $process->stop();
        
        $this->assertEquals(1, $diffTime);
    }
    
    public function testParallel()
    {
        $startTime = microtime(true);
        
        $process1 = new sleepingProcess();
        $process2 = new sleepingProcess();
        
        $process1->start();
        $process2->start();

        do {
            usleep(10);
            $diffTime = round(microtime(true) - $startTime);
        } while (($process1->isRunning() || $process2->isRunning()) && $diffTime < 3);
        
        $process1->stop();
        $process2->stop();
        
        $this->assertEquals(2, $diffTime);
    }
    
    public function testVariables()
    {
        $startTime = microtime(true);
        
        $process = new variableProcess();
        
        $process->start();
        $process->setVariable('request', true);

        do {
            usleep(10);
            $diffTime = round(microtime(true) - $startTime);
            $response = $process->getVariable('response');
        } while ($response === null && $diffTime < 3);
        
        $process->stop();
        
        $this->assertTrue($response);
    }
    
    public function testAlive()
    {
        $startTime = microtime(true);
        
        $process = new aliveProcess();
        
        $process->start();
        
        sleep(2);

        $this->assertEquals(1, $process->getLastAlive());
        
        $process->stop();
    }
}

class simpleProcess extends ZendX_Console_Process_Unix
{
    protected function _run()
    {
    }
}

class sleepingProcess extends ZendX_Console_Process_Unix
{
    protected function _run()
    {
        sleep(1);
    }
}

class variableProcess extends ZendX_Console_Process_Unix
{
    protected function _run()
    {
        $var = null;
        do {
            $var = $this->getVariable('request');
        } while ($var === NULL);
        
        $this->setVariable('response', true);
    }
}

class aliveProcess extends ZendX_Console_Process_Unix
{
    protected function _run()
    {
        $this->_setAlive();
    }
}

// Call ZendX_Console_Process_UnixTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "ZendX_Console_Process_UnixTest::main") {
    ZendX_Console_Process_UnixTest::main();
}
