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
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Feed/Pubsubhubbub.php';

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Subsubhubbub
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Pubsubhubbub_PubsubhubbubTest extends PHPUnit_Framework_TestCase
{

    public function teardown()
    {
        Zend_Feed_Pubsubhubbub::clearHttpClient();
    }

    public function testCanSetCustomHttpClient()
    {
        Zend_Feed_Pubsubhubbub::setHttpClient(new Test_Http_Client_Pubsub());
        $this->assertTrue(
            Zend_Feed_Pubsubhubbub::getHttpClient() instanceof Test_Http_Client_Pubsub
        );
    }

    public function testCanDetectHubs()
    {
        $feed = Zend_Feed_Reader::importFile(dirname(__FILE__) . '/_files/rss20.xml');
        $this->assertEquals(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ), Zend_Feed_Pubsubhubbub::detectHubs($feed));
    }

}

class Test_Http_Client_Pubsub extends Zend_Http_Client {}
