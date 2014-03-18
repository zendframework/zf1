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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Filter_Digits
 */
require_once 'Zend/Filter/Digits.php';


/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class Zend_Filter_DigitsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Filter_Digits object extended for checking whether Unicode PCRE is enabled
     *
     * @var Zend_Filter_Digits
     */
    protected $_filter;

    /**
     * Is PCRE is compiled with UTF-8 and Unicode support
     *
     * @var mixed
     **/
    protected static $_unicodeEnabled;

    /**
     * Creates a new Zend_Filter_Digits object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new Zend_Filter_Digits();
        if (null === self::$_unicodeEnabled) {
            self::$_unicodeEnabled = (@preg_match('/\pL/u', 'a')) ? true : false;
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        if (self::$_unicodeEnabled && extension_loaded('mbstring')) {
            // Filter for the value with mbstring
            /**
             * The first element of $valuesExpected contains multibyte digit characters.
             *   But , Zend_Filter_Digits is expected to return only singlebyte digits.
             *
             * The second contains multibyte or singebyte space, and also alphabet.
             * The third  contains various multibyte characters.
             * The last contains only singlebyte digits.
             */
            $valuesExpected = array(
                '1９2八3四８'     => '123',
                'Ｃ 4.5B　6'      => '456',
                '9壱8＠7．6，5＃4' => '987654',
                '789'              => '789'
                );
        } else {
            // POSIX named classes are not supported, use alternative 0-9 match
            // Or filter for the value without mbstring
            $valuesExpected = array(
                'abc123'  => '123',
                'abc 123' => '123',
                'abcxyz'  => '',
                'AZ@#4.3' => '43',
                '1.23'    => '123',
                '0x9f'    => '09'
                );
        }

        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals(
                $output,
                $result = $this->_filter->filter($input),
                "Expected '$input' to filter to '$output', but received '$result' instead"
                );
        }
    }
}
