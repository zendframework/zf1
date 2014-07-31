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
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (version_compare(PHPUnit_Runner_Version::id(), '4.1', '>=')) {
    $alias = 'Zend_Test_PHPUnit_Constraint_ResponseHeader41';
} elseif (version_compare(PHPUnit_Runner_Version::id(), '3.5', '>=')) {
    $alias = 'Zend_Test_PHPUnit_Constraint_ResponseHeader37';
} else {
    $alias = 'Zend_Test_PHPUnit_Constraint_ResponseHeader34';
}

class_alias(
    $alias,
    'Zend_Test_PHPUnit_Constraint_ResponseHeader'
);
