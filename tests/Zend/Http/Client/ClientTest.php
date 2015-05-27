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
 * @package    Zend_Http
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Http/Client.php';

/**
 * @category   Zend
 * @package    Zend_Http_Client
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Http
 * @group      Zend_Http_Client
 */
class Zend_Http_Client_ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * Set up the test case
     *
     */
    protected function setUp()
    {
        $this->client = new Zend_Http_Client();
    }

    public function invalidHeaders()
    {
        return array(
            'invalid-name-cr'                      => array("X-Foo-\rBar", 'value'),
            'invalid-name-lf'                      => array("X-Foo-\nBar", 'value'),
            'invalid-name-crlf'                    => array("X-Foo-\r\nBar", 'value'),
            'invalid-value-cr'                     => array('X-Foo-Bar', "value\risEvil"),
            'invalid-value-lf'                     => array('X-Foo-Bar', "value\nisEvil"),
            'invalid-value-bad-continuation'       => array('X-Foo-Bar', "value\r\nisEvil"),
            'invalid-array-value-cr'               => array('X-Foo-Bar', array("value\risEvil")),
            'invalid-array-value-lf'               => array('X-Foo-Bar', array("value\nisEvil")),
            'invalid-array-value-bad-continuation' => array('X-Foo-Bar', array("value\r\nisEvil")),
        );
    }

    /**
     * @dataProvider invalidHeaders
     * @group ZF2015-04
     */
    public function testHeadersContainingCRLFInjectionRaiseAnException($name, $value)
    {
        $this->setExpectedException('Zend_Http_Exception');
        $this->client->setHeaders(array(
            $name => $value,
        ));
    }
}
