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
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Ldap_Dn
 */
require_once 'Zend/Ldap/Dn.php';

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Dn
 */
class Zend_Ldap_Dn_MiscTest extends PHPUnit_Framework_TestCase
{
    public function testIsChildOfIllegalDn1()
    {
        $dn1='name1,cn=name2,dc=example,dc=org';
        $dn2='dc=example,dc=org';
        $this->assertFalse(Zend_Ldap_Dn::isChildOf($dn1, $dn2));
    }

    public function testIsChildOfIllegalDn2()
    {
        $dn1='cn=name1,cn=name2,dc=example,dc=org';
        $dn2='example,dc=org';
        $this->assertFalse(Zend_Ldap_Dn::isChildOf($dn1, $dn2));
    }

    public function testIsChildOfIllegalBothDn()
    {
        $dn1='name1,cn=name2,dc=example,dc=org';
        $dn2='example,dc=org';
        $this->assertFalse(Zend_Ldap_Dn::isChildOf($dn1, $dn2));
    }

    public function testIsChildOf()
    {
        $dn1='cb=name1,cn=name2,dc=example,dc=org';
        $dn2='dc=example,dc=org';
        $this->assertTrue(Zend_Ldap_Dn::isChildOf($dn1, $dn2));
    }

    public function testIsChildOfWithDnObjects()
    {
        $dn1=Zend_Ldap_Dn::fromString('cb=name1,cn=name2,dc=example,dc=org');
        $dn2=Zend_Ldap_Dn::fromString('dc=example,dc=org');
        $this->assertTrue(Zend_Ldap_Dn::isChildOf($dn1, $dn2));
    }

    public function testIsChildOfOtherSubtree()
    {
        $dn1='cb=name1,cn=name2,dc=example,dc=org';
        $dn2='dc=example,dc=de';
        $this->assertFalse(Zend_Ldap_Dn::isChildOf($dn1, $dn2));
    }

    public function testIsChildOfParentDnLonger()
    {
        $dn1='dc=example,dc=de';
        $dn2='cb=name1,cn=name2,dc=example,dc=org';
        $this->assertFalse(Zend_Ldap_Dn::isChildOf($dn1, $dn2));
    }
}
