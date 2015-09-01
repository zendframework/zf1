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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Validate_EmailAddressTest::main');
}

/**
 * @see Zend_Validate_EmailAddress
 */
require_once 'Zend/Validate/EmailAddress.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_EmailAddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * Default instance created for all test methods
     *
     * @var Zend_Validate_EmailAddress
     */
    protected $_validator;

    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Creates a new Zend_Validate_EmailAddress object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Zend_Validate_EmailAddress();
    }

    /**
     * Ensures that a basic valid e-mail address passes validation
     *
     * @return void
     */
    public function testBasic()
    {
        $this->assertTrue($this->_validator->isValid('username@example.com'));
    }

    /**
     * Ensures that localhost address is valid
     *
     * @return void
     */
    public function testLocalhostAllowed()
    {
        $validator = new Zend_Validate_EmailAddress(Zend_Validate_Hostname::ALLOW_ALL);
        $this->assertTrue($validator->isValid('username@localhost'));
    }

    /**
     * Ensures that local domain names are valid
     *
     * @return void
     */
    public function testLocaldomainAllowed()
    {
        $validator = new Zend_Validate_EmailAddress(Zend_Validate_Hostname::ALLOW_ALL);
        $this->assertTrue($validator->isValid('username@localhost.localdomain'));
    }

    /**
     * Ensures that IP hostnames are valid
     *
     * @return void
     */
    public function testIPAllowed()
    {
        $validator = new Zend_Validate_EmailAddress(Zend_Validate_Hostname::ALLOW_DNS | Zend_Validate_Hostname::ALLOW_IP);
        $valuesExpected = array(
            array(Zend_Validate_Hostname::ALLOW_DNS, true, array('bob@212.212.20.4')),
            array(Zend_Validate_Hostname::ALLOW_DNS, false, array('bob@localhost'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input), implode("\n", $validator->getMessages()));
            }
        }
    }

    /**
     * Ensures that validation fails when the local part is missing
     *
     * @return void
     */
    public function testLocalPartMissing()
    {
        $this->assertFalse($this->_validator->isValid('@example.com'));
        $messages = $this->_validator->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertContains('local-part@hostname', current($messages));
    }

    /**
     * Ensures that validation fails and produces the expected messages when the local part is invalid
     *
     * @return void
     */
    public function testLocalPartInvalid()
    {
        $this->assertFalse($this->_validator->isValid('Some User@example.com'));

        $messages = $this->_validator->getMessages();

        $this->assertEquals(3, count($messages));

        $this->assertContains('Some User', current($messages));
        $this->assertContains('dot-atom', current($messages));

        $this->assertContains('Some User', next($messages));
        $this->assertContains('quoted-string', current($messages));

        $this->assertContains('Some User', next($messages));
        $this->assertContains('not a valid local part', current($messages));
    }

    /**
     * Ensures that no validation failure message is produced when the local part follows the quoted-string format
     *
     * @return void
     */
    public function testLocalPartQuotedString()
    {
        $this->assertTrue($this->_validator->isValid('"Some User"@example.com'));

        $messages = $this->_validator->getMessages();

        $this->assertTrue(is_array($messages));
        $this->assertEquals(0, count($messages));
    }

    /**
     * Ensures that validation fails when the hostname is invalid
     *
     * @return void
     */
    public function testHostnameInvalid()
    {
        $this->assertFalse($this->_validator->isValid('username@ example . com'));
        $messages = $this->_validator->getMessages();
        $this->assertThat(count($messages), $this->greaterThanOrEqual(1));
        $this->assertContains('not a valid hostname', current($messages));
    }

    /**
     * Ensures that quoted-string local part is considered valid
     *
     * @return void
     */
    public function testQuotedString()
    {
        $emailAddresses = array(
            '""@domain.com', // Optional
            '" "@domain.com', // x20
            '"!"@domain.com', // x21
            '"\""@domain.com', // \" (escaped x22)
            '"#"@domain.com', // x23
            '"$"@domain.com', // x24
            '"Z"@domain.com', // x5A
            '"["@domain.com', // x5B
            '"\\\"@domain.com', // \\ (escaped x5C)
            '"]"@domain.com', // x5D
            '"^"@domain.com', // x5E
            '"}"@domain.com', // x7D
            '"~"@domain.com', // x7E
            '"username"@example.com',
            '"bob%jones"@domain.com',
            '"bob jones"@domain.com',
            '"bob@jones"@domain.com',
            '"[[ bob ]]"@domain.com',
            '"jones"@domain.com'
            );
        foreach ($emailAddresses as $input) {
            $this->assertTrue($this->_validator->isValid($input), "$input failed to pass validation:\n"
                            . implode("\n", $this->_validator->getMessages()));
        }
    }

    /**
     * Ensures that quoted-string local part is considered invalid
     *
     * @return void
     */
    public function testInvalidQuotedString()
    {
        $emailAddresses = array(
            "\"\x00\"@example.com",
            "\"\x01\"@example.com",
            "\"\x1E\"@example.com",
            "\"\x1F\"@example.com",
            '"""@example.com', // x22 (not escaped)
            '"\"@example.com', // x5C (not escaped)
            "\"\x7F\"@example.com",
        );
        foreach ($emailAddresses as $input) {
            $this->assertFalse($this->_validator->isValid($input), "$input failed to pass validation:\n"
                . implode("\n", $this->_validator->getMessages()));
        }
    }


    /**
     * Ensures that validation fails when the e-mail is given as for display,
     * with angle brackets around the actual address
     *
     * @return void
     */
    public function testEmailDisplay()
    {
        $this->assertFalse($this->_validator->isValid('User Name <username@example.com>'));
        $messages = $this->_validator->getMessages();
        $this->assertThat(count($messages), $this->greaterThanOrEqual(3));
        $this->assertContains('not a valid hostname', current($messages));
        $this->assertContains('cannot match TLD', next($messages));
        $this->assertContains('does not appear to be a valid local network name', next($messages));
    }

    /**
     * Ensures that the validator follows expected behavior for valid email addresses
     *
     * @return void
     */
    public function testBasicValid()
    {
        $emailAddresses = array(
            'bob@domain.com',
            'bob.jones@domain.co.uk',
            'bob.jones.smythe@domain.co.uk',
            'BoB@domain.museum',
            'bobjones@domain.info',
            "B.O'Callaghan@domain.com",
            'bob+jones@domain.us',
            'bob+jones@domain.co.uk',
            'bob@some.domain.uk.com',
            'bob@verylongdomainsupercalifragilisticexpialidociousspoonfulofsugar.com'
            );
        foreach ($emailAddresses as $input) {
            $this->assertTrue($this->_validator->isValid($input), "$input failed to pass validation:\n"
                            . implode("\n", $this->_validator->getMessages()));
        }
    }

    /**
     * Ensures that the validator follows expected behavior for invalid email addresses
     *
     * @return void
     */
    public function testBasicInvalid()
    {
        $emailAddresses = array(
            '',
            'bob

            @domain.com',
            'bob jones@domain.com',
            '.bobJones@studio24.com',
            'bobJones.@studio24.com',
            'bob.Jones.@studio24.com',
            '"bob%jones@domain.com',
            'bob@verylongdomainsupercalifragilisticexpialidociousaspoonfulofsugar.com',
            'bob+domain.com',
            'bob.domain.com',
            'bob @domain.com',
            'bob@ domain.com',
            'bob @ domain.com',
            'Abc..123@example.com'
            );
        foreach ($emailAddresses as $input) {
            $this->assertFalse($this->_validator->isValid($input), implode("\n", $this->_validator->getMessages()) . $input);
        }
    }

   /**
     * Ensures that the validator follows expected behavior for valid email addresses with complex local parts
     *
     * @return void
     */
    public function testComplexLocalValid()
    {
        $emailAddresses = array(
            'Bob.Jones@domain.com',
            'Bob.Jones!@domain.com',
            'Bob&Jones@domain.com',
            '/Bob.Jones@domain.com',
            '#Bob.Jones@domain.com',
            'Bob.Jones?@domain.com',
            'Bob~Jones@domain.com'
            );
        foreach ($emailAddresses as $input) {
            $this->assertTrue($this->_validator->isValid($input));
        }
    }


   /**
     * Ensures that the validator follows expected behavior for checking MX records
     *
     * @return void
     */
    public function testMXRecords()
    {
        if (!defined('TESTS_ZEND_VALIDATE_ONLINE_ENABLED')
            || !constant('TESTS_ZEND_VALIDATE_ONLINE_ENABLED')
        ) {
            $this->markTestSkipped('Testing MX records only works when a valid internet connection is available');
            return;
        }

        $validator = new Zend_Validate_EmailAddress(Zend_Validate_Hostname::ALLOW_DNS, true);

        // Are MX checks supported by this system?
        if (!$validator->validateMxSupported()) {
            $this->markTestSkipped('Testing MX records is not supported with this configuration');
            return;
        }

        $valuesExpected = array(
            array(true, array('Bob.Jones@zend.com', 'Bob.Jones@php.net')),
            array(false, array('Bob.Jones@bad.example.com', 'Bob.Jones@anotherbad.example.com'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()));
            }
        }

        // Try a check via setting the option via a method
        unset($validator);
        $validator = new Zend_Validate_EmailAddress();
        $validator->setValidateMx(true);
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()));
            }
        }
    }

   /**
     * Test changing hostname settings via EmailAddress object
     *
     * @return void
     */
    public function testHostnameSettings()
    {
        $validator = new Zend_Validate_EmailAddress();

        // Check no IDN matching
        $validator->getHostnameValidator()->setValidateIdn(false);
        $valuesExpected = array(
            array(false, array('name@b�rger.de', 'name@h�llo.de', 'name@h�llo.se'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()));
            }
        }

        // Check no TLD matching
        $validator->getHostnameValidator()->setValidateTld(false);
        $valuesExpected = array(
            array(true, array('name@domain.xx', 'name@domain.zz', 'name@domain.madeup'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()));
            }
        }
    }

    /**
     * Ensures that getMessages() returns expected default value (an empty array)
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    /**
     * @group ZF-2861
     */
    public function testHostnameValidatorMessagesShouldBeTranslated()
    {
        require_once 'Zend/Validate/Hostname.php';
        $hostnameValidator = new Zend_Validate_Hostname();
        require_once 'Zend/Translate.php';
        $translations = array(
            'hostnameIpAddressNotAllowed' => 'hostnameIpAddressNotAllowed translation',
            'hostnameUnknownTld' => 'hostnameUnknownTld translation',
            'hostnameDashCharacter' => 'hostnameDashCharacter translation',
            'hostnameInvalidHostnameSchema' => 'hostnameInvalidHostnameSchema translation',
            'hostnameUndecipherableTld' => 'hostnameUndecipherableTld translation',
            'hostnameInvalidHostname' => 'hostnameInvalidHostname translation',
            'hostnameInvalidLocalName' => 'hostnameInvalidLocalName translation',
            'hostnameLocalNameNotAllowed' => 'hostnameLocalNameNotAllowed translation',
        );
        $translator = new Zend_Translate('array', $translations);
        $this->_validator->setTranslator($translator)->setHostnameValidator($hostnameValidator);

        $this->_validator->isValid('_XX.!!3xx@0.239,512.777');
        $messages = $hostnameValidator->getMessages();
        $found = false;
        foreach ($messages as $code => $message) {
            if (array_key_exists($code, $translations)) {
                $this->assertEquals($translations[$code], $message);
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    /**
     * @group ZF-4888
     */
    public function testEmailsExceedingLength()
    {
        $emailAddresses = array(
            'thislocalpathoftheemailadressislongerthantheallowedsizeof64characters@domain.com',
            'bob@verylongdomainsupercalifragilisticexpialidociousspoonfulofsugarverylongdomainsupercalifragilisticexpialidociousspoonfulofsugarverylongdomainsupercalifragilisticexpialidociousspoonfulofsugarverylongdomainsupercalifragilisticexpialidociousspoonfulofsugarexpialidociousspoonfulofsugar.com',
            );
        foreach ($emailAddresses as $input) {
            $this->assertFalse($this->_validator->isValid($input));
        }
    }

    /**
     * @group ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->_validator->isValid(array(1 => 1)));
    }

    /**
     * @group ZF-7490
     */
    public function testSettingHostnameMessagesThroughEmailValidator()
    {
        $translations = array(
            'hostnameIpAddressNotAllowed' => 'hostnameIpAddressNotAllowed translation',
            'hostnameUnknownTld' => 'hostnameUnknownTld translation',
            'hostnameDashCharacter' => 'hostnameDashCharacter translation',
            'hostnameInvalidHostnameSchema' => 'hostnameInvalidHostnameSchema translation',
            'hostnameUndecipherableTld' => 'hostnameUndecipherableTld translation',
            'hostnameInvalidHostname' => 'hostnameInvalidHostname translation',
            'hostnameInvalidLocalName' => 'hostnameInvalidLocalName translation',
            'hostnameLocalNameNotAllowed' => 'hostnameLocalNameNotAllowed translation',
        );

        $this->_validator->setMessages($translations);
        $this->_validator->isValid('_XX.!!3xx@0.239,512.777');
        $messages = $this->_validator->getMessages();
        $found = false;
        foreach ($messages as $code => $message) {
            if (array_key_exists($code, $translations)) {
                $this->assertEquals($translations[$code], $message);
                $found = true;
                break;
            }
        }

        $this->assertTrue($found);
    }

    /**
     * Testing initializing with several options
     */
    public function testInstanceWithOldOptions()
    {
        $handler = set_error_handler(array($this, 'errorHandler'), E_USER_NOTICE);
        $validator = new Zend_Validate_EmailAddress();
        $options   = $validator->getOptions();

        $this->assertEquals(Zend_Validate_Hostname::ALLOW_DNS, $options['allow']);
        $this->assertFalse($options['mx']);

        try {
            $validator = new Zend_Validate_EmailAddress(Zend_Validate_Hostname::ALLOW_ALL, true, new Zend_Validate_Hostname(Zend_Validate_Hostname::ALLOW_ALL));
            $options   = $validator->getOptions();

            $this->assertEquals(Zend_Validate_Hostname::ALLOW_ALL, $options['allow']);
            $this->assertTrue($options['mx']);
            set_error_handler($handler);
        } catch (Zend_Exception $e) {
            $this->markTestSkipped('MX not available on this system');
        }
    }

    /**
     * Testing setOptions
     */
    public function testSetOptions()
    {
        $this->_validator->setOptions(array('messages' => array(Zend_Validate_EmailAddress::INVALID => 'TestMessage')));
        $messages = $this->_validator->getMessageTemplates();
        $this->assertEquals('TestMessage', $messages[Zend_Validate_EmailAddress::INVALID]);

        $oldHostname = $this->_validator->getHostnameValidator();
        $this->_validator->setOptions(array('hostname' => new Zend_Validate_Hostname(Zend_Validate_Hostname::ALLOW_ALL)));
        $hostname = $this->_validator->getHostnameValidator();
        $this->assertNotEquals($oldHostname, $hostname);
    }

    /**
     * Testing setMessage
     */
    public function testSetSingleMessage()
    {
        $messages = $this->_validator->getMessageTemplates();
        $this->assertNotEquals('TestMessage', $messages[Zend_Validate_EmailAddress::INVALID]);
        $this->_validator->setMessage('TestMessage');
        $messages = $this->_validator->getMessageTemplates();
        $this->assertEquals('TestMessage', $messages[Zend_Validate_EmailAddress::INVALID]);
    }
    
    /**
     * Testing setMessage for all messages
     *
     * @group ZF-10690
     */
    public function testSetMultipleMessages()
    {
        $messages = $this->_validator->getMessageTemplates();
        $this->assertNotEquals('TestMessage', $messages[Zend_Validate_EmailAddress::INVALID]);
        $this->_validator->setMessage('TestMessage');
        $messages = $this->_validator->getMessageTemplates();
        $this->assertEquals('TestMessage', $messages[Zend_Validate_EmailAddress::INVALID]);
        $this->assertEquals('TestMessage', $messages[Zend_Validate_EmailAddress::INVALID_FORMAT]);
        $this->assertEquals('TestMessage', $messages[Zend_Validate_EmailAddress::DOT_ATOM]);
    }

    /**
     * Testing validateMxSupported
     */
    public function testValidateMxSupported()
    {
        if (function_exists('getmxrr')) {
            $this->assertTrue($this->_validator->validateMxSupported());
        } else {
            $this->assertFalse($this->_validator->validateMxSupported());
        }
    }

    /**
     * Testing getValidateMx
     */
    public function testGetValidateMx()
    {
        $this->assertFalse($this->_validator->getValidateMx());
    }

    /**
     * Testing getDeepMxCheck
     */
    public function testGetDeepMxCheck()
    {
        $this->assertFalse($this->_validator->getDeepMxCheck());
    }

    /**
     * Testing getDomainCheck
     */
    public function testGetDomainCheck()
    {
        $this->assertTrue($this->_validator->getDomainCheck());
    }

    public function errorHandler($errno, $errstr)
    {
        if (strstr($errstr, 'deprecated')) {
            $this->multipleOptionsDetected = true;
        }
    }
    
    /**
     * @group ZF-11239
     */
    public function testNotSetHostnameValidator()
    {
        $hostname = $this->_validator->getHostnameValidator();
        $this->assertTrue($hostname instanceof Zend_Validate_Hostname);
    }

    /**
     * @group GH-62
     */
    public function testIdnHostnameInEmaillAddress()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped('idn_to_ascii() is available in intl in PHP 5.3.0+');
        }
        $validator = new Zend_Validate_EmailAddress();
        $validator->setValidateMx(true);
        $this->assertTrue($validator->isValid('testmail@zürich.ch'));
    }

    /**
     * @group GH-517
     */
    public function testNonReservedIp()
    {
        $validator = new Zend_Validate_EmailAddress(Zend_Validate_Hostname::ALLOW_IP);
        $this->assertTrue($validator->isValid('bob@192.162.33.24'));
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Validate_EmailAddressTest::main') {
    Zend_Validate_EmailAddressTest::main();
}
