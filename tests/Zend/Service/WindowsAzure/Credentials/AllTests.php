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
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @version    $Id$
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Test helpers
 */
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_WindowsAzure_Credentials_AllTests::main');
}

require_once 'Zend/Service/WindowsAzure/Credentials/SharedKeyTest.php';
require_once 'Zend/Service/WindowsAzure/Credentials/SharedKeyLiteTest.php';
require_once 'Zend/Service/WindowsAzure/Credentials/SharedAccessSignatureTest.php';

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @version    $Id$
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_WindowsAzure_Credentials_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite(__CLASS__);

        $suite->addTestSuite('Zend_Service_WindowsAzure_Credentials_SharedKeyTest');
        $suite->addTestSuite('Zend_Service_WindowsAzure_Credentials_SharedKeyLiteTest');
        $suite->addTestSuite('Zend_Service_WindowsAzure_Credentials_SharedAccessSignatureTest');
        
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_WindowsAzure_Credentials_AllTests::main') {
    Zend_Service_WindowsAzure_Credentials_AllTests::main();
}
