#!/bin/sh

# To install DbUnit, we have to install everything

pear update-channels
pear upgrade-all
pear config-set auto_discover 1

# remove any existing phpunit packages
pear uninstall pear.phpunit.de/PHPUnit
pear uninstall pear.phpunit.de/DbUnit
pear uninstall pear.phpunit.de/PHP_CodeCoverage
pear uninstall pear.phpunit.de/File_Iterator
pear uninstall pear.phpunit.de/Text_Template
pear uninstall pear.phpunit.de/PHP_Timer
pear uninstall pear.phpunit.de/PHPUnit_MockObject
pear uninstall pear.phpunit.de/PHPUnit_Selenium
pear uninstall pear.symfony-project.com/YAML

# Install
pear config-set auto_discover 1
pear install pear.phpunit.de/PHPUnit
pear install pear.symfony-project.com/YAML
pear install pear.phpunit.de/PHPUnit_Selenium
pear install pear.phpunit.de/PHPUnit_MockObject
pear install pear.phpunit.de/PHP_Timer
pear install pear.phpunit.de/File_Iterator
pear install pear.phpunit.de/PHP_CodeCoverage
pear install pear.phpunit.de/Text_Template
pear install pear.phpunit.de/DbUnit
