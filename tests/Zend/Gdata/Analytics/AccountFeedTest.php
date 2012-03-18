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
 * @package    Zend_Gdata_Analytics
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Gdata/Analytics.php';
require_once 'Zend/Http/Client.php';

/**
 * @category   Zend
 * @package    Zend_Gdata_Analytics
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Analytics
 */
class Zend_Gdata_Analytics_AccountFeedTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->accountFeed = new Zend_Gdata_Analytics_AccountFeed(
            file_get_contents(dirname(__FILE__) . '/_files/TestAccountFeed.xml'),
            true
        );
    }

    public function testAccountFeed()
    {
        $this->assertEquals(count($this->accountFeed->entries), 3);
        $this->assertEquals($this->accountFeed->entries->count(), 3);
        foreach ($this->accountFeed->entries as $entry) {
            $this->assertTrue($entry instanceof Zend_Gdata_Analytics_AccountEntry);
        }
    }
}
