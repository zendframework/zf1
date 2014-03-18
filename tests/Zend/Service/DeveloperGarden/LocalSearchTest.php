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
class Zend_Service_DeveloperGarden_LocalSearchTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Service_DeveloperGarden_LocalSearch_Mock
     */
    protected $_service = null;

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED') ||
            TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED !== true) {
            $this->markTestSkipped('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED is not enabled');
        }
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
        $this->service = new Zend_Service_DeveloperGarden_LocalSearch_Mock($config);
    }

    public function testLocalSearchValid()
    {
        $searchParameters = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $searchParameters->setWhere('Berlin')
                         ->setWhat('Pizza')
                         ->setHits(3);
        try {
            $result = $this->service->localSearch($searchParameters);
            $this->assertTrue(
                $result->getSearchResult() instanceof Zend_Service_DeveloperGarden_Response_LocalSearch_LocalSearchResponseType
            );
            $this->assertEquals('0000', $result->getErrorCode());
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Response_Exception
     */
    public function testLocalSearchInValid()
    {
        $searchParameters = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $searchParameters->setWhere('Berlin');
        $result = $this->service->localSearch($searchParameters);
    }
}

class Zend_Service_DeveloperGarden_LocalSearch_Mock
    extends Zend_Service_DeveloperGarden_LocalSearch
{

}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_DeveloperGarden_LocalSearchTest::main') {
    Zend_Service_DeveloperGarden_LocalSearchTest::main();
}
