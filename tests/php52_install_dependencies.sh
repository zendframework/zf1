#!/bin/sh

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

# memcache 2.1.0 is the last version to support the php 5.2 branch
pecl download memcached-2.1.0
tar zxvf memcached*.tgz && cd memcached*
make clean
printf "\n" | phpize
./configure --with-libmemcached-dir=/usr/local && make && make install

printf "\n" | pecl uninstall memcache
printf "\n" | pecl install memcache

# Install
pear install -o pear.phpunit.de/PHPUnit
pear install pear.phpunit.de/DbUnit


