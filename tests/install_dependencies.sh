#!/bin/sh

# To install DbUnit, we have to install everything

pear channel-discover pear.phpunit.de
pear channel-discover pear.symfony-project.com
pear update-channels

# remove any existing phpunit packages
pear uninstall -n pear.phpunit.de/DbUnit
pear uninstall -n pear.phpunit.de/PHPUnit_Selenium
pear uninstall -n pear.phpunit.de/PHPUnit
pear uninstall -n pear.phpunit.de/File_Iterator
pear uninstall -n pear.phpunit.de/Text_Template
pear uninstall -n pear.phpunit.de/PHP_CodeCoverage
pear uninstall -n pear.phpunit.de/PHP_Timer
pear uninstall -n pear.phpunit.de/PHPUnit_MockObject
pear uninstall -n pear.symfony-project.com/YAML

# Install
pear config-set auto_discover 1
pear install -f -o pear.phpunit.de/PHPUnit
pear install -f -o pear.phpunit.de/PHPUnit_Selenium
pear install -f -o pear.phpunit.de/DbUnit
