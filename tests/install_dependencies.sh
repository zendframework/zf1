#!/bin/sh

pear update-channels
pear upgrade-all
pear config-set auto_discover 1

# remove any existing phpunit packages
pear uninstall phpunit/PHPUnit
pear uninstall phpunit/DbUnit
pear uninstall phpunit/PHP_CodeCoverage
pear uninstall phpunit/File_Iterator
pear uninstall phpunit/Text_Template
pear uninstall phpunit/PHP_Timer
pear uninstall phpunit/PHPUnit_MockObject
pear uninstall phpunit/PHPUnit_Selenium
pear uninstall pear.symfony-project.com/YAML

# install phpunit 3.4
pear update-channels
pear install pear.symfony-project.com/YAML-1.0.2
pear install phpunit/PHPUnit_Selenium-1.0.1
pear install phpunit/PHPUnit_MockObject-1.0.3
pear install phpunit/PHP_Timer-1.0.0
pear install phpunit/File_Iterator-1.2.3
pear install phpunit/PHP_CodeCoverage-1.0.2
pear install phpunit/Text_Template-1.0.0
pear install phpunit/DbUnit-1.0.0
pear install pear.phpunit.de/PHPUnit-3.4.15