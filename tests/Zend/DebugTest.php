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
 * @package    Zend_Debug
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/**
 * Zend_Debug
 */
require_once 'Zend/Debug.php';

/**
 * @category   Zend
 * @package    Zend_Debug
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Debug
 */
class Zend_DebugTest extends PHPUnit_Framework_TestCase
{

    public function testDebugDefaultSapi()
    {
        $sapi = php_sapi_name();
        Zend_Debug::setSapi(null);
        $data = 'string';
        $result = Zend_Debug::Dump($data, null, false);
        $this->assertEquals($sapi, Zend_Debug::getSapi());
    }

    public function testDebugDump()
    {
        Zend_Debug::setSapi('cli');
        $data = 'string';
        $result = Zend_Debug::Dump($data, null, false);
        $result = str_replace(array(PHP_EOL, "\n"), '_', $result);
        $expected = "__string(6) \"string\"__";
        $this->assertEquals($expected, $result);
    }

    public function testDebugCgi()
    {
        Zend_Debug::setSapi('cgi');
        $data = 'string';
        $result = Zend_Debug::Dump($data, null, false);

        // Has to check for two strings, because xdebug internally handles CLI vs Web
        $this->assertContains($result,
            array(
                "<pre>string(6) \"string\"\n</pre>",
                "<pre>string(6) &quot;string&quot;\n</pre>",
            )
        );
    }

    public function testDebugDumpEcho()
    {
        Zend_Debug::setSapi('cli');
        $data = 'string';

        ob_start();
        $result1 = Zend_Debug::Dump($data, null, true);
        $result2 = ob_get_clean();

        $this->assertContains('string(6) "string"', $result1);
        $this->assertEquals($result1, $result2);
    }

    public function testDebugDumpLabel()
    {
        Zend_Debug::setSapi('cli');
        $data = 'string';
        $label = 'LABEL';
        $result = Zend_Debug::Dump($data, $label, false);
        $result = str_replace(array(PHP_EOL, "\n"), '_', $result);
        $expected = "_{$label} _string(6) \"string\"__";
        $this->assertEquals($expected, $result);
    }

    /**
     * @group ZF-4136
     * @group ZF-1663
     */
    public function testXdebugEnabledAndNonCliSapiDoesNotEscapeSpecialChars()
    {
        if(!extension_loaded('xdebug')) {
            $this->markTestSkipped("This test only works in combination with xdebug.");
        }

        Zend_Debug::setSapi('apache');
        $a = array("a" => "b");

        $result = Zend_Debug::dump($a, "LABEL", false);
        $this->assertContains("<pre>", $result);
        $this->assertContains("</pre>", $result);
    }

}
