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
 * @package    Zend_Http_UserAgent
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Http_UserAgent
 */
require_once 'Zend/Http/UserAgent.php';
require_once 'Zend/Http/UserAgent/Features/Adapter/WurflApi.php';

/** to generate the cache */
set_time_limit(0);

/**
 * @category   Zend
 * @package    Zend_Http_UserAgent
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Http_UserAgent_Features_Adapter_WurflApiTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!constant('TESTS_ZEND_HTTP_USERAGENT_WURFL_LIB_DIR')
            || !constant('TESTS_ZEND_HTTP_USERAGENT_WURFL_CONFIG_FILE')
        ) {
            $this->markTestSkipped('Requires WURFL library');
        }
        $this->config['wurflapi']['wurfl_lib_dir']     = constant('TESTS_ZEND_HTTP_USERAGENT_WURFL_LIB_DIR');
        $this->config['wurflapi']['wurfl_config_file'] = constant('TESTS_ZEND_HTTP_USERAGENT_WURFL_CONFIG_FILE');
    }

    public function testGetFromRequest()
    {
        $request['http_user_agent'] = 'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleW1ebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/4A102 Safari/419.3';
        $wurfl = Zend_Http_UserAgent_Features_Adapter_WurflApi::getFromRequest($request, $this->config);
        $this->assertEquals('Safari', $wurfl['mobile_browser']);
        $this->assertEquals('iPhone OS', $wurfl['device_os']);
        $this->assertEquals('1.0', $wurfl['device_os_version']);
        $this->assertEquals('true', $wurfl['has_qwerty_keyboard']);
        $this->assertEquals('touchscreen', $wurfl['pointing_method']);
        $this->assertEquals('false', $wurfl['is_tablet']);
        $this->assertEquals('iPhone', $wurfl['model_name']);
        $this->assertEquals('Apple', $wurfl['brand_name']);
    }
}
