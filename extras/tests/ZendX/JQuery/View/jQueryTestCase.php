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
 * @category    ZendX
 * @package     ZendX_JQuery
 * @subpackage  View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id: DatePickerTest.php 20165 2010-01-09 18:57:56Z bkarwin $
 */

require_once dirname(__FILE__)."/../../../TestHelper.php";

require_once "Zend/Registry.php";
require_once "Zend/View.php";
require_once "ZendX/JQuery.php";
require_once "ZendX/JQuery/View/Helper/JQuery.php";

abstract class ZendX_JQuery_View_jQueryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View
     */
    protected $view = null;

    /**
     * @var ZendX_JQuery_View_Helper_JQuery_Container
     */
    protected $jquery = null;

    public function setUp()
    {
        Zend_Registry::_unsetInstance();
        $this->view   = $this->getView();
        $this->jquery = new ZendX_JQuery_View_Helper_JQuery_Container();
        $this->jquery->setView($this->view);
        Zend_Registry::set('ZendX_JQuery_View_Helper_JQuery', $this->jquery);
    }

    public function tearDown()
    {
        ZendX_JQuery_View_Helper_JQuery::disableNoConflictMode();
    }

    /**
     * Get jQuery View
     *
     * @return Zend_View
     */
    public function getView()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
        return $view;
    }
}