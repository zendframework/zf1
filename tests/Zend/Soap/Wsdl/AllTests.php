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
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Soap_Wsdl_AllTests::main');
}

require_once "ArrayOfTypeComplexStrategyTest.php";
require_once "ArrayOfTypeSequenceStrategyTest.php";
require_once "DefaultComplexTypeTest.php";

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Soap
 * @group      Zend_Soap_Wsdl
 */
class Zend_Soap_Wsdl_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Soap_Wsdl');

        $suite->addTestSuite('Zend_Soap_Wsdl_ArrayOfTypeComplexStrategyTest');
        $suite->addTestSuite('Zend_Soap_Wsdl_ArrayOfTypeSequenceStrategyTest');
        $suite->addTestSuite('Zend_Soap_Wsdl_DefaultComplexTypeTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Soap_Wsdl_AllTests::main') {
    Zend_Soap_Wsdl_AllTests::main();
}
