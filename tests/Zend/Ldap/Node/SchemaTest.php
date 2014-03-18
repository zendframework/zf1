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
 * Zend_Ldap_OnlineTestCase
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'OnlineTestCase.php';

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Node
 */
class Zend_Ldap_Node_SchemaTest extends Zend_Ldap_OnlineTestCase
{
    /**
     * @var Zend_Ldap_Node_Schema
     */
    private $_schema;

    protected function setUp()
    {
        parent::setUp();
        $this->_schema=$this->_getLdap()->getSchema();
    }

    public function testSchemaNode()
    {
        $schema=$this->_getLdap()->getSchema();

        $this->assertEquals($this->_schema, $schema);
        $this->assertSame($this->_schema, $schema);

        $serial=serialize($this->_schema);
        $schemaUn=unserialize($serial);
        $this->assertEquals($this->_schema, $schemaUn);
        $this->assertNotSame($this->_schema, $schemaUn);
    }

    public function testGetters()
    {
        $this->assertTrue(is_array($this->_schema->getAttributeTypes()));
        $this->assertTrue(is_array($this->_schema->getObjectClasses()));

        switch ($this->_getLdap()->getRootDse()->getServerType()) {
            case Zend_Ldap_Node_RootDse::SERVER_TYPE_ACTIVEDIRECTORY:
                break;
            case Zend_Ldap_Node_RootDse::SERVER_TYPE_EDIRECTORY:
                break;
            case Zend_Ldap_Node_RootDse::SERVER_TYPE_OPENLDAP:
                $this->assertTrue(is_array($this->_schema->getLdapSyntaxes()));
                $this->assertTrue(is_array($this->_schema->getMatchingRules()));
                $this->assertTrue(is_array($this->_schema->getMatchingRuleUse()));
                break;
        }
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testSetterWillThrowException()
    {
          $this->_schema->objectClass='illegal';
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testOffsetSetWillThrowException()
    {
          $this->_schema['objectClass']='illegal';
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testUnsetterWillThrowException()
    {
          unset($this->_schema->objectClass);
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testOffsetUnsetWillThrowException()
    {
          unset($this->_schema['objectClass']);
    }

    public function testOpenLdapSchema()
    {
        if ($this->_getLdap()->getRootDse()->getServerType() !==
                Zend_Ldap_Node_RootDse::SERVER_TYPE_OPENLDAP) {
            $this->markTestSkipped('Test can only be run on an OpenLDAP server');
        }

        $objectClasses=$this->_schema->getObjectClasses();
        $attributeTypes=$this->_schema->getAttributeTypes();

        $this->assertArrayHasKey('organizationalUnit', $objectClasses);
        $ou=$objectClasses['organizationalUnit'];
        $this->assertTrue($ou instanceof Zend_Ldap_Node_Schema_ObjectClass_OpenLdap);
        $this->assertEquals('organizationalUnit', $ou->getName());
        $this->assertEquals('2.5.6.5', $ou->getOid());
        $this->assertEquals(array('objectClass', 'ou'), $ou->getMustContain());
        $this->assertEquals(array('businessCategory', 'description', 'destinationIndicator',
            'facsimileTelephoneNumber', 'internationaliSDNNumber', 'l',
            'physicalDeliveryOfficeName', 'postOfficeBox', 'postalAddress', 'postalCode',
            'preferredDeliveryMethod', 'registeredAddress', 'searchGuide', 'seeAlso', 'st',
            'street', 'telephoneNumber', 'teletexTerminalIdentifier', 'telexNumber',
            'userPassword', 'x121Address'), $ou->getMayContain());
        $this->assertEquals('RFC2256: an organizational unit', $ou->getDescription());
        $this->assertEquals(Zend_Ldap_Node_Schema::OBJECTCLASS_TYPE_STRUCTURAL, $ou->getType());
        $this->assertEquals(array('top'), $ou->getParentClasses());

        $this->assertEquals('2.5.6.5', $ou->oid);
        $this->assertEquals('organizationalUnit', $ou->name);
        $this->assertEquals('RFC2256: an organizational unit', $ou->desc);
        $this->assertFalse($ou->obsolete);
        $this->assertEquals(array('top'), $ou->sup);
        $this->assertFalse($ou->abstract);
        $this->assertTrue($ou->structural);
        $this->assertFalse($ou->auxiliary);
        $this->assertEquals(array('ou'), $ou->must);
        $this->assertEquals(array('userPassword', 'searchGuide', 'seeAlso', 'businessCategory',
            'x121Address', 'registeredAddress', 'destinationIndicator', 'preferredDeliveryMethod',
            'telexNumber', 'teletexTerminalIdentifier', 'telephoneNumber',
            'internationaliSDNNumber', 'facsimileTelephoneNumber', 'street', 'postOfficeBox',
            'postalCode', 'postalAddress', 'physicalDeliveryOfficeName', 'st', 'l',
            'description'), $ou->may);
        $this->assertEquals("( 2.5.6.5 NAME 'organizationalUnit' " .
            "DESC 'RFC2256: an organizational unit' SUP top STRUCTURAL MUST ou " .
            "MAY ( userPassword $ searchGuide $ seeAlso $ businessCategory $ x121Address $ " .
            "registeredAddress $ destinationIndicator $ preferredDeliveryMethod $ telexNumber $ " .
            "teletexTerminalIdentifier $ telephoneNumber $ internationaliSDNNumber $ " .
            "facsimileTelephoneNumber $ street $ postOfficeBox $ postalCode $ postalAddress $ " .
            "physicalDeliveryOfficeName $ st $ l $ description ) )", $ou->_string);
        $this->assertEquals(array(), $ou->aliases);
        $this->assertSame($objectClasses['top'], $ou->_parents[0]);

        $this->assertArrayHasKey('ou', $attributeTypes);
        $ou=$attributeTypes['ou'];
        $this->assertTrue($ou instanceof Zend_Ldap_Node_Schema_AttributeType_OpenLdap);
        $this->assertEquals('ou', $ou->getName());
        $this->assertEquals('2.5.4.11', $ou->getOid());
        $this->assertEquals('1.3.6.1.4.1.1466.115.121.1.15', $ou->getSyntax());
        $this->assertEquals(32768, $ou->getMaxLength());
        $this->assertFalse($ou->isSingleValued());
        $this->assertEquals('RFC2256: organizational unit this object belongs to', $ou->getDescription());

        $this->assertEquals('2.5.4.11', $ou->oid);
        $this->assertEquals('ou', $ou->name);
        $this->assertEquals('RFC2256: organizational unit this object belongs to', $ou->desc);
        $this->assertFalse($ou->obsolete);
        $this->assertEquals(array('name'), $ou->sup);
        $this->assertNull($ou->equality);
        $this->assertNull($ou->ordering);
        $this->assertNull($ou->substr);
        $this->assertNull($ou->syntax);
        $this->assertNull($ou->{'max-length'});
        $this->assertFalse($ou->{'single-value'});
        $this->assertFalse($ou->collective);
        $this->assertFalse($ou->{'no-user-modification'});
        $this->assertEquals('userApplications', $ou->usage);
        $this->assertEquals("( 2.5.4.11 NAME ( 'ou' 'organizationalUnitName' ) " .
            "DESC 'RFC2256: organizational unit this object belongs to' SUP name )", $ou->_string);
        $this->assertEquals(array('organizationalUnitName'), $ou->aliases);
        $this->assertSame($attributeTypes['name'], $ou->_parents[0]);
    }

    public function testActiveDirectorySchema()
    {
        if ($this->_getLdap()->getRootDse()->getServerType() !==
                Zend_Ldap_Node_RootDse::SERVER_TYPE_ACTIVEDIRECTORY) {
            $this->markTestSkipped('Test can only be run on an Active Directory server');
        }

        $objectClasses=$this->_schema->getObjectClasses();
        $attributeTypes=$this->_schema->getAttributeTypes();
    }

    public function testeDirectorySchema()
    {
        if ($this->_getLdap()->getRootDse()->getServerType() !==
                Zend_Ldap_Node_RootDse::SERVER_TYPE_EDIRECTORY) {
            $this->markTestSkipped('Test can only be run on an eDirectory server');
        }
        $this->markTestIncomplete("Novell eDirectory schema parsing is incomplete");
    }

    public function testOpenLdapSchemaAttributeTypeInheritance()
    {
        if ($this->_getLdap()->getRootDse()->getServerType() !==
                Zend_Ldap_Node_RootDse::SERVER_TYPE_OPENLDAP) {
            $this->markTestSkipped('Test can only be run on an OpenLDAP server');
        }

        $attributeTypes=$this->_schema->getAttributeTypes();

        $name=$attributeTypes['name'];
        $cn=$attributeTypes['cn'];

        $this->assertEquals('2.5.4.41', $name->getOid());
        $this->assertEquals('2.5.4.3', $cn->getOid());
        $this->assertNull($name->sup);
        $this->assertEquals(array('name'), $cn->sup);

        $this->assertEquals('caseIgnoreMatch', $name->equality);
        $this->assertNull($name->ordering);
        $this->assertEquals('caseIgnoreSubstringsMatch', $name->substr);
        $this->assertEquals('1.3.6.1.4.1.1466.115.121.1.15', $name->syntax);
        $this->assertEquals('1.3.6.1.4.1.1466.115.121.1.15', $name->getSyntax());
        $this->assertEquals(32768, $name->{'max-length'});
        $this->assertEquals(32768, $name->getMaxLength());

        $this->assertNull($cn->equality);
        $this->assertNull($cn->ordering);
        $this->assertNull($cn->substr);
        $this->assertNull($cn->syntax);
        $this->assertEquals('1.3.6.1.4.1.1466.115.121.1.15', $cn->getSyntax());
        $this->assertNull($cn->{'max-length'});
        $this->assertEquals(32768, $cn->getMaxLength());
    }

    public function testOpenLdapSchemaObjectClassInheritance()
    {
        if ($this->_getLdap()->getRootDse()->getServerType() !==
                Zend_Ldap_Node_RootDse::SERVER_TYPE_OPENLDAP) {
            $this->markTestSkipped('Test can only be run on an OpenLDAP server');
        }

        $objectClasses=$this->_schema->getObjectClasses();

        if (!array_key_exists('certificationAuthority', $objectClasses) ||
                !array_key_exists('certificationAuthority-V2', $objectClasses)) {
            $this->markTestSkipped('This requires OpenLDAP core schema');
        }

        $ca=$objectClasses['certificationAuthority'];
        $ca2=$objectClasses['certificationAuthority-V2'];

        $this->assertEquals('2.5.6.16', $ca->getOid());
        $this->assertEquals('2.5.6.16.2', $ca2->getOid());
        $this->assertEquals(array('top'), $ca->sup);
        $this->assertEquals(array('certificationAuthority'), $ca2->sup);

        $this->assertEquals(array('authorityRevocationList', 'certificateRevocationList',
            'cACertificate'), $ca->must);
        $this->assertEquals(array('authorityRevocationList', 'cACertificate',
            'certificateRevocationList', 'objectClass'), $ca->getMustContain());
        $this->assertEquals(array('crossCertificatePair'), $ca->may);
        $this->assertEquals(array('crossCertificatePair'), $ca->getMayContain());

        $this->assertEquals(array(), $ca2->must);
        $this->assertEquals(array('authorityRevocationList', 'cACertificate',
            'certificateRevocationList', 'objectClass'), $ca2->getMustContain());
        $this->assertEquals(array('deltaRevocationList'), $ca2->may);
        $this->assertEquals(array('crossCertificatePair', 'deltaRevocationList'),
            $ca2->getMayContain());
    }

    public function testOpenLdapSchemaAttributeTypeAliases()
    {
        if ($this->_getLdap()->getRootDse()->getServerType() !==
                Zend_Ldap_Node_RootDse::SERVER_TYPE_OPENLDAP) {
            $this->markTestSkipped('Test can only be run on an OpenLDAP server');
        }

        $attributeTypes=$this->_schema->getAttributeTypes();
        $this->assertArrayHasKey('cn', $attributeTypes);
        $this->assertArrayHasKey('commonName', $attributeTypes);
        $ob1=$attributeTypes['cn'];
        $ob2=$attributeTypes['commonName'];
        $this->assertSame($ob1, $ob2);
    }

    public function testOpenLdapSchemaObjectClassAliases()
    {
        if ($this->_getLdap()->getRootDse()->getServerType() !==
                Zend_Ldap_Node_RootDse::SERVER_TYPE_OPENLDAP) {
            $this->markTestSkipped('Test can only be run on an OpenLDAP server');
        }

        $objectClasses=$this->_schema->getObjectClasses();
        $this->assertArrayHasKey('OpenLDAProotDSE', $objectClasses);
        $this->assertArrayHasKey('LDAProotDSE', $objectClasses);
        $ob1=$objectClasses['OpenLDAProotDSE'];
        $ob2=$objectClasses['LDAProotDSE'];
        $this->assertSame($ob1, $ob2);
    }
}
