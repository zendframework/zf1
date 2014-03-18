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
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_CodeGenerator_Php_AllTests::main');
}

require_once 'Zend/CodeGenerator/Php/ClassTest.php';
require_once 'Zend/CodeGenerator/Php/DocblockTest.php';
require_once 'Zend/CodeGenerator/Php/Docblock/TagTest.php';
require_once 'Zend/CodeGenerator/Php/Docblock/Tag/ParamTest.php';
require_once 'Zend/CodeGenerator/Php/Docblock/Tag/ReturnTest.php';
require_once 'Zend/CodeGenerator/Php/FileTest.php';
require_once 'Zend/CodeGenerator/Php/MethodTest.php';
require_once 'Zend/CodeGenerator/Php/ParameterTest.php';
require_once 'Zend/CodeGenerator/Php/PropertyTest.php';
require_once 'Zend/CodeGenerator/Php/Property/DefaultValueTest.php';

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_CodeGenerator
 * @group      Zend_CodeGenerator_Php
 */
class Zend_CodeGenerator_Php_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_CodeGenerator_Php');

        $suite->addTestSuite('Zend_CodeGenerator_Php_ClassTest');
        $suite->addTestSuite('Zend_CodeGenerator_Php_DocblockTest');
        $suite->addTestSuite('Zend_CodeGenerator_Php_Docblock_TagTest');
        $suite->addTestSuite('Zend_CodeGenerator_Php_Docblock_Tag_ParamTest');
        $suite->addTestSuite('Zend_CodeGenerator_Php_Docblock_Tag_ReturnTest');
        $suite->addTestSuite('Zend_CodeGenerator_Php_FileTest');
        $suite->addTestSuite('Zend_CodeGenerator_Php_MethodTest');
        $suite->addTestSuite('Zend_CodeGenerator_Php_ParameterTest');
        $suite->addTestSuite('Zend_CodeGenerator_Php_PropertyTest');
        $suite->addTestSuite('Zend_CodeGenerator_Php_Property_DefaultValueTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_CodeGenerator_Php_AllTests::main') {
    Zend_CodeGenerator_Php_AllTests::main();
}
