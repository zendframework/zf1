#!/bin/sh

echo $TRAVIS_PHP_VERSION;

if [ "$TRAVIS_PHP_VERSION" = "5.2" ]; then
    # To install DbUnit, we have to install everything
    pear channel-discover pear.phpunit.de
    pear channel-discover pear.symfony-project.com
    pear update-channels

    # remove any existing phpunit packages
    pear uninstall -n pear.phpunit.de/PHPUnit
    pear uninstall -n pear.phpunit.de/PHP_CodeCoverage
    pear uninstall -n pear.phpunit.de/PHPUnit_MockObject
    pear uninstall -n pear.phpunit.de/File_Iterator
    pear uninstall -n pear.phpunit.de/Text_Template
    pear uninstall -n pear.phpunit.de/PHP_Timer
    pear uninstall -n pear.symfony-project.com/YAML

    # Install
    pear install -o pear.phpunit.de/PHPUnit
    pear install pear.phpunit.de/DbUnit
else
    composer install --no-interaction --prefer-source --dev
fi


