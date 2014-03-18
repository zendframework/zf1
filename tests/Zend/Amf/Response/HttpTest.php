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
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Amf_Response_HttpTest::main');
}

/**
 * @see Zend_Amf_Response_Http
 */
require_once 'Zend/Amf/Response/Http.php';

/**
 * Test case for Zend_Amf_Response
 *
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Amf
 * @group      Zend_Amf_Response
 */
class Zend_Amf_Response_HttpTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Amf_Response_HttpTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Ensure isIeOverSsl() does not emit a notice when $_SERVER['HTTPS'] not set
     * @group ZF-11783
     */
    public function testDoesNotEmitNoticeWhenHttpsServerKeyNotSet()
    {
        unset($_SERVER['HTTPS']);
        $req = new ZF11783_ExposeIsIeOverSsl();
        $this->assertFalse($req->isIeOverSsl());
    }

}

/**
 * Expose Zend_Amf_Response_Http::isIeOverSsl for testing
 * @see ZF-11783
 */
class ZF11783_ExposeIsIeOverSsl extends Zend_Amf_Response_Http
{
    public function isIeOverSsl() {
        return parent::isIeOverSsl();
    }
}


if (PHPUnit_MAIN_METHOD == 'Zend_Amf_Response_HttpTest::main') {
    Zend_Amf_Response_HttpTest::main();
}


