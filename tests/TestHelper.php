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
 * @package    Zend
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Include PHPUnit dependencies
 */
require_once 'PHPUnit/Runner/Version.php';

$phpunitVersion = PHPUnit_Runner_Version::id();
if ($phpunitVersion == '@package_version@' || version_compare($phpunitVersion, '3.5.5', '>=')) {
    if (version_compare($phpunitVersion, '3.6.0', '>=')) {
        echo <<<EOT
This version of PHPUnit is not supported in Zend Framework 1.x unit tests.

To install PHPUnit 3.4:

    sudo pear config-set auto_discover 1
    sudo pear install --installroot /usr/local/phpunit34 pear.phpunit.de/PHPUnit-3.4.15

    This will install PHPUnit-3.4.15 to /usr/local/phpunit34. 
    

    Now edit /usr/local/phpunit34/usr/bin/phpunit. Before the first 
    require_once statement in that file, enter the following code:

        set_include_path(implode(PATH_SEPARATOR, array(
            __DIR__ . '/../share/php',
            '/usr/share/php',
            get_include_path()
        )));

    Note the actual directory (share/php in the code above) depends on your
    particular installation. The correct path can be found by typing:

        pear config-show|grep php_dir

    (on Centos it is share/php, on Ubuntu/Debian it is share/pear and on
     OS X it is lib/php/pear)


    Lastly, we need a symlink:

        sudo ln -s /some/path/phpunit34/usr/bin/phpunit /usr/bin/phpunit34

    Now you can run the unit tests with:

        phpunit34 --stderr -d memory_limit=-1 Zend/{Name}/AllTests.php 

    (Based on information from Christer Edvartsen's article published at
     http://tech.vg.no/2011/11/29/running-multiple-versions-of-phpunit/)


EOT;

        exit(1);
    }
    require_once 'PHPUnit/Autoload.php'; // >= PHPUnit 3.5.5
} else {
    require_once 'PHPUnit/Framework.php'; // < PHPUnit 3.5.5
}

/*
 * Set error reporting to the level to which Zend Framework code must comply.
 */
error_reporting(E_ALL | E_STRICT);

/*
 * Determine the root, library, and tests directories of the framework
 * distribution.
 */
$zfRoot        = realpath(dirname(dirname(__FILE__)));
$zfCoreLibrary = "$zfRoot/library";
$zfCoreTests   = "$zfRoot/tests";

/*
 * Prepend the Zend Framework library/ and tests/ directories to the
 * include_path. This allows the tests to run out of the box and helps prevent
 * loading other copies of the framework code and tests that would supersede
 * this copy.
 */
$path = array(
    $zfCoreLibrary,
    $zfCoreTests,
    get_include_path()
    );
set_include_path(implode(PATH_SEPARATOR, $path));

/*
 * Load the user-defined test configuration file, if it exists; otherwise, load
 * the default configuration.
 */
if (is_readable($zfCoreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
    require_once $zfCoreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php';
} else {
    require_once $zfCoreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php.dist';
}

/**
 * Start output buffering, if enabled
 */
if (defined('TESTS_ZEND_OB_ENABLED') && constant('TESTS_ZEND_OB_ENABLED')) {
    ob_start();
}

/*
 * Unset global variables that are no longer needed.
 */
unset($zfRoot, $zfCoreLibrary, $zfCoreTests, $path);

// Suppress DateTime warnings
date_default_timezone_set(@date_default_timezone_get());

