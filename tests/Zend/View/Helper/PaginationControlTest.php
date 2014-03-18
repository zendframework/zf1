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

// Call Zend_View_Helper_PaginationControlTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_PaginationControlTest::main");
}

require_once 'Zend/View.php';
require_once 'Zend/Paginator.php';
require_once 'Zend/View/Helper/PaginationControl.php';

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_PaginationControlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_PaginationControl
     */
    private $_viewHelper;

    private $_paginator;

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {

        $suite = new PHPUnit_Framework_TestSuite("Zend_View_Helper_PaginationControlTest");
        PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $view = new Zend_View();
        $view->addBasePath(dirname(__FILE__) . '/_files');

        Zend_View_Helper_PaginationControl::setDefaultViewPartial(null);
        $this->_viewHelper = new Zend_View_Helper_PaginationControl();
        $this->_viewHelper->setView($view);
        $this->_paginator = Zend_Paginator::factory(range(1, 101));
    }

    public function tearDown()
    {
        unset($this->_viewHelper);
        unset($this->_paginator);
    }

    public function testGetsAndSetsView()
    {
        $view   = new Zend_View();
        $helper = new Zend_View_Helper_PaginationControl();
        $this->assertNull($helper->view);
        $helper->setView($view);
        $this->assertTrue($helper->view instanceof Zend_View_Interface);
    }

    public function testGetsAndSetsDefaultViewPartial()
    {
        $this->assertNull(Zend_View_Helper_PaginationControl::getDefaultViewPartial());
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('partial');
        $this->assertEquals('partial', Zend_View_Helper_PaginationControl::getDefaultViewPartial());
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(null);
    }

    public function testUsesDefaultViewPartialIfNoneSupplied()
    {
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('testPagination.phtml');
        $output = $this->_viewHelper->paginationControl($this->_paginator);
        $this->assertContains('pagination control', $output, $output);
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(null);
    }

    public function testThrowsExceptionIfNoViewPartialFound()
    {
        try {
            $this->_viewHelper->paginationControl($this->_paginator);
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_View_Exception);
            $this->assertEquals('No view partial provided and no default set', $e->getMessage());
        }
    }

    /**
     * @group ZF-4037
     */
    public function testUsesDefaultScrollingStyleIfNoneSupplied()
    {
        // First we'll make sure the base case works
        $output = $this->_viewHelper->paginationControl($this->_paginator, 'All', 'testPagination.phtml');
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);

        Zend_Paginator::setDefaultScrollingStyle('All');
        $output = $this->_viewHelper->paginationControl($this->_paginator, null, 'testPagination.phtml');
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);

        Zend_View_Helper_PaginationControl::setDefaultViewPartial('testPagination.phtml');
        $output = $this->_viewHelper->paginationControl($this->_paginator);
        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);
    }

    /**
     * @group ZF-4153
     */
    public function testUsesPaginatorFromViewIfNoneSupplied()
    {
        $this->_viewHelper->view->paginator = $this->_paginator;
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('testPagination.phtml');

        try {
            $output = $this->_viewHelper->paginationControl();
        } catch (Zend_View_Exception $e) {
            $this->fail('Could not find paginator in the view instance');
        }

        $this->assertContains('pagination control', $output, $output);
    }

    /**
     * @group ZF-4153
     */
    public function testThrowsExceptionIfNoPaginatorFound()
    {
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('testPagination.phtml');

        try {
            $output = $this->_viewHelper->paginationControl();
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_View_Exception);
            $this->assertEquals('No paginator instance provided or incorrect type', $e->getMessage());
        }
    }

    /**
     * @group ZF-4233
     */
    public function testAcceptsViewPartialInOtherModule()
    {
        try {
            $this->_viewHelper->paginationControl($this->_paginator, null, array('partial.phtml', 'test'));
        } catch (Exception $e) {
            /* We don't care whether or not the module exists--we just want to
             * make sure it gets to Zend_View_Helper_Partial and it's recognized
             * as a module. */
            $this->assertTrue($e instanceof Zend_View_Helper_Partial_Exception);
            $this->assertEquals('Cannot render partial; module does not exist', $e->getMessage());
        }
    }

    /**
     * @group ZF-4328
     */
    public function testUsesPaginatorFromViewOnlyIfNoneSupplied()
    {
        $this->_viewHelper->view->paginator  = $this->_paginator;
        $paginator = Zend_Paginator::factory(range(1, 30));
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('testPagination.phtml');

        $output = $this->_viewHelper->paginationControl($paginator);
        $this->assertContains('page count (3)', $output, $output);
    }

    /**
     * @group ZF-4878
     */
    public function testCanUseObjectForScrollingStyle()
    {
        $all = new Zend_Paginator_ScrollingStyle_All();

        try {
            $output = $this->_viewHelper->paginationControl($this->_paginator, $all, 'testPagination.phtml');
        } catch (Exception $e) {
            $this->fail('Could not use object for sliding style');
        }

        $this->assertContains('page count (11) equals pages in range (11)', $output, $output);
    }
}

// Call Zend_View_Helper_PaginationControlTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_PaginationControlTest::main") {
    Zend_View_Helper_PaginationControlTest::main();
}
