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
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Search_Lucene_Index_TermInfo
 */
require_once 'Zend/Search/Lucene/Index/TermInfo.php';

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class Zend_Search_Lucene_Index_TermInfoTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $termInfo = new Zend_Search_Lucene_Index_TermInfo(0, 1, 2, 3);
        $this->assertTrue($termInfo instanceof Zend_Search_Lucene_Index_TermInfo);

        $this->assertEquals($termInfo->docFreq,      0);
        $this->assertEquals($termInfo->freqPointer,  1);
        $this->assertEquals($termInfo->proxPointer,  2);
        $this->assertEquals($termInfo->skipOffset,   3);
        $this->assertEquals($termInfo->indexPointer, null);

        $termInfo = new Zend_Search_Lucene_Index_TermInfo(0, 1, 2, 3, 4);
        $this->assertEquals($termInfo->indexPointer, 4);
    }
}

