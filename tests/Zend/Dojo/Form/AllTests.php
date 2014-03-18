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
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Dojo_Form_AllTests::main');
}

require_once 'Zend/Dojo/Form/Decorator/AllTests.php';
require_once 'Zend/Dojo/Form/Element/AllTests.php';
require_once 'Zend/Dojo/Form/FormTest.php';
require_once 'Zend/Dojo/Form/SubFormTest.php';

/**
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class Zend_Dojo_Form_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Dojo_Form');

        $suite->addTest(Zend_Dojo_Form_Decorator_AllTests::suite());
        $suite->addTest(Zend_Dojo_Form_Element_AllTests::suite());
        $suite->addTestSuite('Zend_Dojo_Form_FormTest');
        $suite->addTestSuite('Zend_Dojo_Form_SubFormTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Dojo_Form_AllTests::main') {
    Zend_Dojo_Form_AllTests::main();
}
