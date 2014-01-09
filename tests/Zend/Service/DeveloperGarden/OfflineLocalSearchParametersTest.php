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
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_DeveloperGarden_LocalSearchTest::main');
}

/**
 * @see Zend_Service_DeveloperGarden_LocalSearch
 */
require_once 'Zend/Service/DeveloperGarden/LocalSearch.php';

/**
 * Zend_Service_DeveloperGarden test case
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Service_DeveloperGarden_OfflineLocalSearchParametersTest extends PHPUnit_Framework_TestCase
{
    /**
     * @todo add more search param tests
     */

    /**
     * @var Zend_Service_DeveloperGarden_OfflineLocalSearch_Mock
     */
    protected $_service = null;

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN', 'Unknown');
        }
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD', 'Unknown');
        }
        $config = array(
            'username' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN,
            'password' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD,
        );
        $this->service = new Zend_Service_DeveloperGarden_OfflineLocalSearch_Mock($config);
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_LocalSearch_Exception
     */
    public function testSetHitsNegative()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $param->setHits(-1);
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_LocalSearch_Exception
     */
    public function testSetHitsToHigh()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $param->setHits(1001);
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_LocalSearch_Exception
     */
    public function testSetHitsToHighVeryLarge()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $param->setHits(100001);
    }

    public function testSetHits()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertTrue(
            $param->setHits(1000) instanceof Zend_Service_DeveloperGarden_LocalSearch_SearchParameters
        );
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_LocalSearch_Exception
     */
    public function testCollapseValuesWrong()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertTrue(
            $param->setCollapse('SomeStrangeValue') instanceof Zend_Service_DeveloperGarden_LocalSearch_SearchParameters
        );
    }

    public function testCollapseValuesTrue()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertTrue(
            $param->setCollapse(true) instanceof Zend_Service_DeveloperGarden_LocalSearch_SearchParameters
        );
    }

    public function testCollapseValuesFalse()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertTrue(
            $param->setCollapse(false) instanceof Zend_Service_DeveloperGarden_LocalSearch_SearchParameters
        );
    }

    public function testCollapseValuesAddressCompany()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertTrue(
            $param->setCollapse('ADDRESS_COMPANY') instanceof Zend_Service_DeveloperGarden_LocalSearch_SearchParameters
        );
    }

    public function testCollapseValuesDomain()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertTrue(
            $param->setCollapse('DOMAIN') instanceof Zend_Service_DeveloperGarden_LocalSearch_SearchParameters
        );
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_LocalSearch_Exception
     */
    public function testWhereEmpty()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertTrue(
            $param->setWhere(null) instanceof Zend_Service_DeveloperGarden_LocalSearch_SearchParameters
        );
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_LocalSearch_Exception
     */
    public function testRadiusWithString()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertTrue(
            $param->setRadius('foobar') instanceof Zend_Service_DeveloperGarden_LocalSearch_SearchParameters
        );
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_LocalSearch_Exception
     */
    public function testRadiusWithStringAndInteger()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertTrue(
            $param->setRadius('1a') instanceof Zend_Service_DeveloperGarden_LocalSearch_SearchParameters
        );
    }

    public function testRadiusWithIntegerAsString()
    {
        $param = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $this->assertTrue(
            $param->setRadius('-100') instanceof Zend_Service_DeveloperGarden_LocalSearch_SearchParameters
        );
    }
}

class Zend_Service_DeveloperGarden_OfflineLocalSearch_Mock
    extends Zend_Service_DeveloperGarden_LocalSearch
{

}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_DeveloperGarden_OfflineLocalSearchParametersTest::main') {
    Zend_Service_DeveloperGarden_OfflineLocalSearchParametersTest::main();
}
