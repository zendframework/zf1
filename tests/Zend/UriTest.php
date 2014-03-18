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
 * @package    Zend_Uri
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_UriTest::main');
}

/**
 * Zend_Uri
 */
require_once 'Zend/Uri.php';

/**
 * Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * @category   Zend
 * @package    Zend_Uri
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Uri
 */
class Zend_UriTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_UriTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $this->notices = array();
        $this->errorReporting = error_reporting();
        $this->displayErrors  = ini_get('display_errors');
    }

    public function tearDown()
    {
        error_reporting($this->errorReporting);
        ini_set('display_errors', $this->displayErrors);
    }

    public function testSchemeEmpty()
    {
        $this->_testInvalidUri('', '/empty/i');
        $this->_testInvalidUri('://www.zend.com', '/empty/i');
    }

    public function testSchemeUnsupported()
    {
        $this->_testInvalidUri('unsupported', '/unsupported/i');
        $this->_testInvalidUri('unsupported://zend.com', '/unsupported/i');
    }

    public function testSchemeIllegal()
    {
        $this->_testInvalidUri('!@#$%^&*()', '/illegal/i');
    }

    public function testSchemeHttp()
    {
        $this->_testValidUri('http');
    }

    public function testSchemeHttps()
    {
        $this->_testValidUri('https');
    }

    public function testSchemeMailto()
    {
        $this->markTestIncomplete('Zend_Uri_Mailto is not implemented yet');
        $this->_testValidUri('mailto');
    }

    /**
     * Tests that Zend_Uri::setConfig() allows Zend_Config
     *
     * @group ZF-5578
     */
    public function testSetConfigWithArray()
    {
        Zend_Uri::setConfig(array('allow_unwise' => true));
    }

    /**
     * Tests that Zend_Uri::setConfig() allows Array
     *
     * @group ZF-5578
     */
    public function testSetConfigWithZendConfig()
    {
        Zend_Uri::setConfig(new Zend_Config(array('allow_unwise' => true)));
    }

    /**
     * Tests that Zend_Uri::setConfig() throws Zend_Uri_Exception if no array
     * nor Zend_Config is given as first parameter
     *
     * @group ZF-5578
     * @expectedException Zend_Uri_Exception
     */
    public function testSetConfigInvalid()
    {
        Zend_Uri::setConfig('This should cause an exception');
    }

    /**
     * Tests that if an exception is thrown when calling the __toString()
     * method an empty string is returned and a Warning is triggered, instead
     * of a Fatal Error being triggered.
     *
     * @group ZF-10405
     */
    public function testToStringRaisesWarningWhenExceptionCaught()
    {
        $uri = Zend_Uri::factory('http://example.com', 'Zend_Uri_ExceptionCausing');

        set_error_handler(array($this, 'handleErrors'), E_USER_WARNING);

        $text = sprintf('%s', $uri);

        restore_error_handler();

        $this->assertTrue(empty($text));
        $this->assertTrue(isset($this->error));
        $this->assertContains('Exception in getUri()', $this->error);

    }

    /**
     * Error handler for testExceptionThrownInToString()
     *
     * @group ZF-10405
     */
    public function handleErrors($errno, $errstr, $errfile = '', $errline = 0, array $errcontext = array())
    {
        $this->error = $errstr;
    }

    /**
     * Tests that an invalid $uri throws an exception and that the
     * message of that exception matches $regex.
     *
     * @param string $uri
     * @param string $regex
     */
    protected function _testInvalidUri($uri, $regex)
    {
        $e = null;
        try {
            $uri = Zend_Uri::factory($uri);
        } catch (Zend_Uri_Exception $e) {
            $this->assertRegExp($regex, $e->getMessage());
            return;
        }
        $this->fail('Zend_Uri_Exception was expected but not thrown');
    }

    /**
     * Tests that a valid $uri returns a Zend_Uri object.
     *
     * @param string $uri
     */
    protected function _testValidUri($uri, $className = null)
    {
        $uri = Zend_Uri::factory($uri, $className);
        $this->assertTrue($uri instanceof Zend_Uri, 'Zend_Uri object not returned.');
        return $uri;
    }

    public function testFactoryWithUnExistingClassThrowException()
    {
        $this->setExpectedException('Zend_Uri_Exception', '"This_Is_An_Unknown_Class" not found');
        Zend_Uri::factory('http://example.net', 'This_Is_An_Unknown_Class');
    }

    public function testFactoryWithExistingClassButNotImplementingZendUriThrowException()
    {
        $this->setExpectedException('Zend_Uri_Exception', '"Fake_Zend_Uri" is not an instance of Zend_Uri');
        Zend_Uri::factory('http://example.net', 'Fake_Zend_Uri');
    }

    public function testFactoryWithExistingClassReturnObject()
    {
        $uri = $this->_testValidUri('http://example.net', 'Zend_Uri_Mock');
        $this->assertTrue($uri instanceof Zend_Uri_Mock, 'Zend_Uri_Mock object not returned.');
    }

}
class Zend_Uri_Mock extends Zend_Uri
{
    protected function __construct($scheme, $schemeSpecific = '') { }
    public function getUri() { }
    public function valid() { }
}
class Zend_Uri_ExceptionCausing extends Zend_Uri
{
    protected function __construct($scheme, $schemeSpecific = '') { }
    public function valid() { }
    public function getUri()
    {
        throw new Exception('Exception in getUri()');
    }
}
class Fake_Zend_Uri
{
}

// Call Zend_UriTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_UriTest::main") {
    Zend_UriTest::main();
}
