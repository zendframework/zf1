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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_View_Helper_TranslateTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_TranslateTest::main");
}

/** Zend_View_Helper_Translate */
require_once 'Zend/View/Helper/Translate.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Translate */
require_once 'Zend/Translate.php';
require_once 'Zend/Translate/Adapter/Array.php';

/**
 * Test class for Zend_View_Helper_Translate.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_TranslateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_Translate
     */
    public $helper;

    /**
     * @var string
     */
    public $basePath;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_TranslateTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function clearRegistry()
    {
        $regKey = 'Zend_Translate';
        if (Zend_Registry::isRegistered($regKey)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[$regKey]);
        }
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->clearRegistry();
        $this->helper = new Zend_View_Helper_Translate();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
        $this->clearRegistry();
    }

    public function testTranslationObjectPassedToConstructorUsedForTranslation()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');

        $helper = new Zend_View_Helper_Translate($trans);
        $this->assertEquals('eins', $helper->translate('one'));
        $this->assertEquals('three', $helper->translate('three'));
    }

    public function testLocalTranslationObjectUsedForTranslationsWhenPresent()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');

        $this->helper->setTranslator($trans);
        $this->assertEquals('eins', $this->helper->translate('one'));
        $this->assertEquals('three', $this->helper->translate('three'));
    }

    public function testTranslationObjectInRegistryUsedForTranslationsInAbsenceOfLocalTranslationObject()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');
        Zend_Registry::set('Zend_Translate', $trans);
        $this->assertEquals('eins', $this->helper->translate('one'));
    }

    public function testOriginalMessagesAreReturnedWhenNoTranslationObjectPresent()
    {
        $this->assertEquals('one', $this->helper->translate('one'));
        $this->assertEquals('three', $this->helper->translate('three'));
    }

    public function testPassingNonNullNonTranslationObjectToConstructorThrowsException()
    {
        try {
            $helper = new Zend_View_Helper_Translate('something');
        } catch (Zend_View_Exception $e) {
            $this->assertContains('must set an instance of Zend_Translate', $e->getMessage());
        }
    }

    public function testPassingNonTranslationObjectToSetTranslatorThrowsException()
    {
        try {
            $this->helper->setTranslator('something');
        } catch (Zend_View_Exception $e) {
            $this->assertContains('must set an instance of Zend_Translate', $e->getMessage());
        }
    }

    public function testRetrievingLocaleWhenNoTranslationObjectSetThrowsException()
    {
        try {
            $this->helper->getLocale();
        } catch (Zend_View_Exception $e) {
            $this->assertContains('must set an instance of Zend_Translate', $e->getMessage());
        }
    }

    public function testSettingLocaleWhenNoTranslationObjectSetThrowsException()
    {
        try {
            $this->helper->setLocale('de');
        } catch (Zend_View_Exception $e) {
            $this->assertContains('must set an instance of Zend_Translate', $e->getMessage());
        }
    }

    public function testCanSetLocale()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');
        $trans->addTranslation(array('one' => 'uno', 'two %1\$s' => 'duo %2\$s'), 'it');
        $trans->setLocale('de');

        $this->helper->setTranslator($trans);
        $this->assertEquals('eins', $this->helper->translate('one'));
        $new = $this->helper->setLocale('it');
        $this->assertTrue($new instanceof Zend_View_Helper_Translate);
        $this->assertEquals('it', $new->getLocale());
        $this->assertEquals('uno', $this->helper->translate('one'));
    }

    public function testHelperImplementsFluentInterface()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');
        $trans->addTranslation(array('one' => 'uno', 'two %1\$s' => 'duo %2\$s'), 'it');
        $trans->setLocale('de');

        $locale = $this->helper->translate()->setTranslator($trans)->getLocale();

        $this->assertEquals('de', $locale);
    }

    public function testCanTranslateWithOptions()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', "two %1\$s" => "zwei %1\$s",
            "three %1\$s %2\$s" => "drei %1\$s %2\$s"), 'de');
        $trans->addTranslation(array('one' => 'uno', "two %1\$s" => "duo %2\$s",
            "three %1\$s %2\$s" => "tre %1\$s %2\$s"), 'it');
        $trans->setLocale('de');

        $this->helper->setTranslator($trans);
        $this->assertEquals("drei 100 200", $this->helper->translate("three %1\$s %2\$s", "100", "200"));
        $this->assertEquals("tre 100 200", $this->helper->translate("three %1\$s %2\$s", "100", "200", 'it'));
        $this->assertEquals("drei 100 200", $this->helper->translate("three %1\$s %2\$s", array("100", "200")));
        $this->assertEquals("tre 100 200", $this->helper->translate("three %1\$s %2\$s", array("100", "200"), 'it'));
    }

    public function testTranslationObjectNullByDefault()
    {
        $this->assertNull($this->helper->getTranslator());
    }

    public function testLocalTranslationObjectIsPreferredOverRegistry()
    {
        $transReg = new Zend_Translate('array', array('one' => 'eins'));
        Zend_Registry::set('Zend_Translate', $transReg);

        $this->assertSame($transReg->getAdapter(), $this->helper->getTranslator());

        $transLoc = new Zend_Translate('array', array('one' => 'uno'));
        $this->helper->setTranslator($transLoc);
        $this->assertSame($transLoc->getAdapter(), $this->helper->getTranslator());
        $this->assertNotSame($transLoc->getAdapter(), $transReg->getAdapter());
    }

    public function testHelperObjectReturnedWhenNoArgumentsPassed()
    {
        $helper = $this->helper->translate();
        $this->assertSame($this->helper, $helper);

        $transLoc = new Zend_Translate('array', array('one' => 'eins'));
        $this->helper->setTranslator($transLoc);
        $helper = $this->helper->translate();
        $this->assertSame($this->helper, $helper);
    }

    /**
     * ZF-6724
     */
    public function testTranslationWithPercent()
    {
        $trans = new Zend_Translate('array', array('one' => 'eins', "two %1\$s" => "zwei %1\$s",
            "three %1\$s %2\$s" => "drei %1\$s %2\$s", 'vier%ig' => 'four%'), 'de');
        $trans->setLocale('de');

        $this->helper->setTranslator($trans);
        $this->assertEquals("four%", $this->helper->translate("vier%ig"));
        $this->assertEquals("zwei 100", $this->helper->translate("two %1\$s", "100"));
    }

    /**
     * ZF-7937
     */
    public function testTranslationWithoutTranslator()
    {
        $result = $this->helper->translate("test %1\$s", "100");
        $this->assertEquals('test 100', $result);
    }
}

// Call Zend_View_Helper_TranslateTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_TranslateTest::main") {
    Zend_View_Helper_TranslateTest::main();
}
