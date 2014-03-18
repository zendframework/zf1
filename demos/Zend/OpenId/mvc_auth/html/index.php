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
 * @package    Zend_OpenId
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @see Zend_Config_Ini
 */
require_once 'Zend/Config/Ini.php';

$config = new Zend_Config_Ini(dirname(__FILE__)."/config.ini");
$front = Zend_Controller_Front::getInstance();
$front->setControllerDirectory(dirname(dirname(__FILE__)) . '/application/controllers')
      ->setBaseUrl($config->baseUrl);
$front->dispatch();
