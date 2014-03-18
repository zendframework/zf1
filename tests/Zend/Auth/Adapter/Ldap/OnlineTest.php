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
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Ldap
 */
require_once 'Zend/Ldap.php';

/**
 * @see Zend_Auth_Adapter_Ldap
 */
require_once 'Zend/Auth/Adapter/Ldap.php';

/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Auth
 */
class Zend_Auth_Adapter_Ldap_OnlineTest extends PHPUnit_Framework_TestCase
{
    /**
     * LDAP connection options
     *
     * @var array
     */
    protected $_options = array();

    /**
     * @var array
     */
    protected $_names = array();

    public function setUp()
    {
        $this->_options = array(
            'host' => TESTS_ZEND_LDAP_HOST,
            'username' => TESTS_ZEND_LDAP_USERNAME,
            'password' => TESTS_ZEND_LDAP_PASSWORD,
            'baseDn' => TESTS_ZEND_LDAP_BASE_DN,
        );
        if (defined('TESTS_ZEND_LDAP_PORT'))
            $this->_options['port'] = TESTS_ZEND_LDAP_PORT;
        if (defined('TESTS_ZEND_LDAP_USE_START_TLS'))
            $this->_options['useStartTls'] = TESTS_ZEND_LDAP_USE_START_TLS;
        if (defined('TESTS_ZEND_LDAP_USE_SSL'))
            $this->_options['useSsl'] = TESTS_ZEND_LDAP_USE_SSL;
        if (defined('TESTS_ZEND_LDAP_BIND_REQUIRES_DN'))
            $this->_options['bindRequiresDn'] = TESTS_ZEND_LDAP_BIND_REQUIRES_DN;
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_FILTER_FORMAT'))
            $this->_options['accountFilterFormat'] = TESTS_ZEND_LDAP_ACCOUNT_FILTER_FORMAT;
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME'))
            $this->_options['accountDomainName'] = TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME;
        if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT'))
            $this->_options['accountDomainNameShort'] = TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT;

        if (defined('TESTS_ZEND_LDAP_ALT_USERNAME')) {
            $this->_names[Zend_Ldap::ACCTNAME_FORM_USERNAME] = TESTS_ZEND_LDAP_ALT_USERNAME;
            if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME')) {
                $this->_names[Zend_Ldap::ACCTNAME_FORM_PRINCIPAL] =
                    TESTS_ZEND_LDAP_ALT_USERNAME . '@' . TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME;
            }
            if (defined('TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT')) {
                $this->_names[Zend_Ldap::ACCTNAME_FORM_BACKSLASH] =
                    TESTS_ZEND_LDAP_ACCOUNT_DOMAIN_NAME_SHORT . '\\' . TESTS_ZEND_LDAP_ALT_USERNAME;
            }
        }
    }

    public function testSimpleAuth()
    {
        $adapter = new Zend_Auth_Adapter_Ldap(
            array($this->_options),
            TESTS_ZEND_LDAP_ALT_USERNAME,
            TESTS_ZEND_LDAP_ALT_PASSWORD
        );

        $result = $adapter->authenticate();

        $this->assertTrue($result instanceof Zend_Auth_Result);
        $this->assertTrue($result->isValid());
        $this->assertTrue($result->getCode() == Zend_Auth_Result::SUCCESS);
    }

    public function testCanonAuth()
    {
        /* This test authenticates with each of the account name forms
         * (uname, uname@example.com, EXAMPLE\uname) AND it does so with
         * the accountCanonicalForm set to each of the account name forms
         * (e.g. authenticate with uname@example.com but getIdentity() returns
         * EXAMPLE\uname). A total of 9 authentications are performed.
         */
        foreach ($this->_names as $form => $formName) {
            $options = $this->_options;
            $options['accountCanonicalForm'] = $form;
            $adapter = new Zend_Auth_Adapter_Ldap(array($options));
            $adapter->setPassword(TESTS_ZEND_LDAP_ALT_PASSWORD);
            foreach ($this->_names as $username) {
                $adapter->setUsername($username);
                $result = $adapter->authenticate();
                $this->assertTrue($result instanceof Zend_Auth_Result);
                $this->assertTrue($result->isValid());
                $this->assertTrue($result->getCode() == Zend_Auth_Result::SUCCESS);
                $this->assertTrue($result->getIdentity() === $formName);
            }
        }
    }

    public function testInvalidPassAuth()
    {
        $adapter = new Zend_Auth_Adapter_Ldap(
            array($this->_options),
            TESTS_ZEND_LDAP_ALT_USERNAME,
            'invalid'
        );

        $result = $adapter->authenticate();
        $this->assertTrue($result instanceof Zend_Auth_Result);
        $this->assertTrue($result->isValid() === false);
        $this->assertTrue($result->getCode() == Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID);
    }

    public function testInvalidUserAuth()
    {
        $adapter = new Zend_Auth_Adapter_Ldap(
            array($this->_options),
            'invalid',
            'doesntmatter'
        );

        $result = $adapter->authenticate();
        $this->assertTrue($result instanceof Zend_Auth_Result);
        $this->assertTrue($result->isValid() === false);
        $this->assertTrue(
            $result->getCode() == Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND ||
            $result->getCode() == Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID
        );
    }

    public function testMismatchDomainAuth()
    {
        $adapter = new Zend_Auth_Adapter_Ldap(
            array($this->_options),
            'EXAMPLE\\doesntmatter',
            'doesntmatter'
        );

        $result = $adapter->authenticate();
        $this->assertTrue($result instanceof Zend_Auth_Result);
        $this->assertFalse($result->isValid());
        $this->assertThat($result->getCode(), $this->lessThanOrEqual(Zend_Auth_Result::FAILURE));
        $messages = $result->getMessages();
        $this->assertContains('not found', $messages[0]);
    }

    public function testAccountObjectRetrieval()
    {
        $adapter = new Zend_Auth_Adapter_Ldap(
            array($this->_options),
            TESTS_ZEND_LDAP_ALT_USERNAME,
            TESTS_ZEND_LDAP_ALT_PASSWORD
        );

        $result = $adapter->authenticate();
        $account = $adapter->getAccountObject();

        $this->assertTrue($result->isValid());
        $this->assertTrue($account instanceof stdClass);
        $this->assertEquals(TESTS_ZEND_LDAP_ALT_DN, $account->dn);
    }

    public function testAccountObjectRetrievalWithOmittedAttributes()
    {
        $adapter = new Zend_Auth_Adapter_Ldap(
            array($this->_options),
            TESTS_ZEND_LDAP_ALT_USERNAME,
            TESTS_ZEND_LDAP_ALT_PASSWORD
        );

        $result = $adapter->authenticate();
        $account = $adapter->getAccountObject(array(), array('userPassword'));

        $this->assertTrue($account instanceof stdClass);
        $this->assertFalse(isset($account->userpassword));
    }
}
