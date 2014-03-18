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
class Zend_Ldap_Dn_ModificationTest extends PHPUnit_Framework_TestCase
{
    public function testDnManipulationGet()
    {
        $dnString='cn=Baker\\, Alice,cn=Users+ou=Lab,dc=example,dc=com';
        $dn=Zend_Ldap_Dn::fromString($dnString);

        $this->assertEquals(array('cn' => 'Baker, Alice'), $dn->get(0));
        $this->assertEquals(array('cn' => 'Users', 'ou' => 'Lab'), $dn->get(1));
        $this->assertEquals(array('dc' => 'example'), $dn->get(2));
        $this->assertEquals(array('dc' => 'com'), $dn->get(3));
        try {
            $this->assertEquals(array('dc' => 'com'), $dn->get('string'));
            $this->fail('Expected Zend_Ldap_Exception not thrown');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals('Parameter $index must be an integer', $e->getMessage());
        }
        try {
            $this->assertEquals(array('cn' => 'Baker, Alice'), $dn->get(-1));
            $this->fail('Expected Zend_Ldap_Exception not thrown');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals('Parameter $index out of bounds', $e->getMessage());
        }
        try {
            $this->assertEquals(array('dc' => 'com'), $dn->get(4));
            $this->fail('Expected Zend_Ldap_Exception not thrown');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals('Parameter $index out of bounds', $e->getMessage());
        }

        $this->assertEquals(array(
            array('cn' => 'Baker, Alice'),
            array('cn' => 'Users', 'ou' => 'Lab')
            ), $dn->get(0, 2));
        $this->assertEquals(array(
            array('cn' => 'Baker, Alice'),
            array('cn' => 'Users', 'ou' => 'Lab'),
            array('dc' => 'example')
            ), $dn->get(0, 3));
        $this->assertEquals(array(
            array('cn' => 'Baker, Alice'),
            array('cn' => 'Users', 'ou' => 'Lab'),
            array('dc' => 'example'),
            array('dc' => 'com')
            ), $dn->get(0, 4));
        $this->assertEquals(array(
            array('cn' => 'Baker, Alice'),
            array('cn' => 'Users', 'ou' => 'Lab'),
            array('dc' => 'example'),
            array('dc' => 'com')
            ), $dn->get(0, 5));

        $this->assertEquals(array(
            array('cn' => 'Users', 'ou' => 'Lab'),
            array('dc' => 'example')
            ), $dn->get(1, 2));
        $this->assertEquals(array(
            array('cn' => 'Users', 'ou' => 'Lab'),
            array('dc' => 'example'),
            array('dc' => 'com')
            ), $dn->get(1, 3));
        $this->assertEquals(array(
            array('cn' => 'Users', 'ou' => 'Lab'),
            array('dc' => 'example'),
            array('dc' => 'com')
            ), $dn->get(1, 4));

        $this->assertEquals(array(
            array('dc' => 'example'),
            array('dc' => 'com')
            ), $dn->get(2, 2));
        $this->assertEquals(array(
            array('dc' => 'example'),
            array('dc' => 'com')
            ), $dn->get(2, 3));

        $this->assertEquals(array(
            array('dc' => 'com')
            ), $dn->get(3, 2));
    }

    public function testDnManipulationSet()
    {
        $dnString='cn=Baker\\, Alice,cn=Users+ou=Lab,dc=example,dc=com';
        $dn=Zend_Ldap_Dn::fromString($dnString);

        $this->assertEquals('uid=abaker,cn=Users+ou=Lab,dc=example,dc=com',
            $dn->set(0, array('uid' => 'abaker'))->toString());
        $this->assertEquals('uid=abaker,ou=Lab,dc=example,dc=com',
            $dn->set(1, array('ou' => 'Lab'))->toString());
        $this->assertEquals('uid=abaker,ou=Lab,dc=example+ou=Test,dc=com',
            $dn->set(2, array('dc' => 'example', 'ou' => 'Test'))->toString());
        $this->assertEquals('uid=abaker,ou=Lab,dc=example+ou=Test,dc=de\+fr',
            $dn->set(3, array('dc' => 'de+fr'))->toString());

        try {
            $dn->set(4, array('dc' => 'de'));
            $this->fail('Expected Zend_Ldap_Exception not thrown');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals('Parameter $index out of bounds', $e->getMessage());
        }
        try {
            $dn->set(3, array('dc' => 'de', 'ou'));
            $this->fail('Expected Zend_Ldap_Exception not thrown');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals('RDN Array is malformed: it must use string keys', $e->getMessage());
        }
    }

    public function testDnManipulationRemove()
    {
        $dnString='cn=Baker\\, Alice,cn=Users+ou=Lab,dc=example,dc=com';

        $dn=Zend_Ldap_Dn::fromString($dnString);
        $this->assertEquals('cn=Users+ou=Lab,dc=example,dc=com', $dn->remove(0)->toString());

        $dn=Zend_Ldap_Dn::fromString($dnString);
        $this->assertEquals('cn=Baker\\, Alice,dc=example,dc=com', $dn->remove(1)->toString());

        $dn=Zend_Ldap_Dn::fromString($dnString);
        $this->assertEquals('cn=Baker\\, Alice,cn=Users+ou=Lab,dc=com', $dn->remove(2)->toString());

        $dn=Zend_Ldap_Dn::fromString($dnString);
        $this->assertEquals('cn=Baker\\, Alice,cn=Users+ou=Lab,dc=example',
            $dn->remove(3)->toString());

        try {
            $dn=Zend_Ldap_Dn::fromString($dnString);
            $dn->remove(4);
            $this->fail('Expected Zend_Ldap_Exception not thrown');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals('Parameter $index out of bounds', $e->getMessage());
        }

        $dn=Zend_Ldap_Dn::fromString($dnString);
        $this->assertEquals('cn=Baker\\, Alice,dc=com',
            $dn->remove(1, 2)->toString());

        $dn=Zend_Ldap_Dn::fromString($dnString);
        $this->assertEquals('cn=Baker\\, Alice',
            $dn->remove(1, 3)->toString());

        $dn=Zend_Ldap_Dn::fromString($dnString);
        $this->assertEquals('cn=Baker\\, Alice',
            $dn->remove(1, 4)->toString());
    }

    public function testDnManipulationAppendAndPrepend()
    {
        $dnString='OU=Sales,DC=example';
        $dn=Zend_Ldap_Dn::fromString($dnString);

        $this->assertEquals('OU=Sales,DC=example,DC=com',
            $dn->append(array('DC' => 'com'))->toString());

        $this->assertEquals('OU=New York,OU=Sales,DC=example,DC=com',
            $dn->prepend(array('OU' => 'New York'))->toString());

        try {
            $dn->append(array('dc' => 'de', 'ou'));
            $this->fail('Expected Zend_Ldap_Exception not thrown');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals('RDN Array is malformed: it must use string keys', $e->getMessage());
        }
        try {
            $dn->prepend(array('dc' => 'de', 'ou'));
            $this->fail('Expected Zend_Ldap_Exception not thrown');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals('RDN Array is malformed: it must use string keys', $e->getMessage());
        }
    }

    public function testDnManipulationInsert()
    {
        $dnString='cn=Baker\\, Alice,cn=Users,dc=example,dc=com';

        $dn=Zend_Ldap_Dn::fromString($dnString);
        $this->assertEquals('cn=Baker\\, Alice,dc=test,cn=Users,dc=example,dc=com',
            $dn->insert(0, array('dc' => 'test'))->toString());

        $dn=Zend_Ldap_Dn::fromString($dnString);
        $this->assertEquals('cn=Baker\\, Alice,cn=Users,dc=test,dc=example,dc=com',
            $dn->insert(1, array('dc' => 'test'))->toString());

        $dn=Zend_Ldap_Dn::fromString($dnString);
        $this->assertEquals('cn=Baker\\, Alice,cn=Users,dc=example,dc=test,dc=com',
            $dn->insert(2, array('dc' => 'test'))->toString());

        $dn=Zend_Ldap_Dn::fromString($dnString);
        $this->assertEquals('cn=Baker\\, Alice,cn=Users,dc=example,dc=com,dc=test',
            $dn->insert(3, array('dc' => 'test'))->toString());

        try {
            $dn=Zend_Ldap_Dn::fromString($dnString);
            $dn->insert(4, array('dc' => 'de'));
            $this->fail('Expected Zend_Ldap_Exception not thrown');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals('Parameter $index out of bounds', $e->getMessage());
        }
        try {
            $dn=Zend_Ldap_Dn::fromString($dnString);
            $dn->insert(3, array('dc' => 'de', 'ou'));
            $this->fail('Expected Zend_Ldap_Exception not thrown');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals('RDN Array is malformed: it must use string keys', $e->getMessage());
        }
    }

    public function testArrayAccessImplementation()
    {
        $dnString='cn=Baker\\, Alice,cn=Users,dc=example,dc=com';
        $dn=Zend_Ldap_Dn::fromString($dnString);

        $this->assertEquals(array('cn' => 'Baker, Alice'), $dn[0]);
        $this->assertEquals(array('cn' => 'Users'), $dn[1]);
        $this->assertEquals(array('dc' => 'example'), $dn[2]);
        $this->assertEquals(array('dc' => 'com'), $dn[3]);

        $this->assertTrue(isset($dn[0]));
        $this->assertTrue(isset($dn[1]));
        $this->assertTrue(isset($dn[2]));
        $this->assertTrue(isset($dn[3]));
        $this->assertFalse(isset($dn[-1]));
        $this->assertFalse(isset($dn[4]));

        $dn=Zend_Ldap_Dn::fromString($dnString);
        unset($dn[0]);
        $this->assertEquals('cn=Users,dc=example,dc=com', $dn->toString());

        $dn=Zend_Ldap_Dn::fromString($dnString);
        unset($dn[1]);
        $this->assertEquals('cn=Baker\\, Alice,dc=example,dc=com', $dn->toString());

        $dn=Zend_Ldap_Dn::fromString($dnString);
        unset($dn[2]);
        $this->assertEquals('cn=Baker\\, Alice,cn=Users,dc=com', $dn->toString());

        $dn=Zend_Ldap_Dn::fromString($dnString);
        unset($dn[3]);
        $this->assertEquals('cn=Baker\\, Alice,cn=Users,dc=example', $dn->toString());

        $dn=Zend_Ldap_Dn::fromString($dnString);
        $dn[0]=array('uid' => 'abaker');
        $this->assertEquals('uid=abaker,cn=Users,dc=example,dc=com', $dn->toString());

        $dn=Zend_Ldap_Dn::fromString($dnString);
        $dn[1]=array('ou' => 'Lab');
        $this->assertEquals('cn=Baker\\, Alice,ou=Lab,dc=example,dc=com', $dn->toString());

        $dn=Zend_Ldap_Dn::fromString($dnString);
        $dn[2]=array('dc' => 'example', 'ou' => 'Test');
        $this->assertEquals('cn=Baker\\, Alice,cn=Users,dc=example+ou=Test,dc=com', $dn->toString());

        $dn=Zend_Ldap_Dn::fromString($dnString);
        $dn[3]=array('dc' => 'de+fr');
        $this->assertEquals('cn=Baker\\, Alice,cn=Users,dc=example,dc=de\+fr', $dn->toString());
    }
}
