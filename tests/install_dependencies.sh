#!/bin/sh

# To install DbUnit, we have to install everything

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

# Install
pear config-set auto_discover 1
pear install pear.symfony-project.com/YAML
pear install pear.phpunit.de/PHPUnit_Selenium
pear install phpunit/PHPUnit_MockObject
pear install phpunit/PHP_Timer
pear install phpunit/File_Iterator
pear install phpunit/PHP_CodeCoverage
pear install phpunit/Text_Template
pear install phpunit/DbUnit
pear install pear.phpunit.de/PHPUnit
