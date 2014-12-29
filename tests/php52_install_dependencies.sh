#!/bin/sh

# remove any existing phpunit packages
pear uninstall -n pear.phpunit.de/PHPUnit
pear uninstall -n pear.phpunit.de/PHP_CodeCoverage
pear uninstall -n pear.phpunit.de/PHPUnit_MockObject
pear uninstall -n pear.phpunit.de/File_Iterator
pear uninstall -n pear.phpunit.de/Text_Template
pear uninstall -n pear.phpunit.de/PHP_Timer

# Install PHPUnit from git (only possibility for PHPUnit 3.6)
cd tests
mkdir phpunit && cd phpunit
git clone git://github.com/sebastianbergmann/phpunit.git
git clone git://github.com/sebastianbergmann/dbunit.git
git clone git://github.com/sebastianbergmann/php-file-iterator.git
git clone git://github.com/sebastianbergmann/php-text-template.git
git clone git://github.com/sebastianbergmann/php-code-coverage.git
git clone git://github.com/sebastianbergmann/php-token-stream.git
git clone git://github.com/sebastianbergmann/php-timer.git
git clone git://github.com/sebastianbergmann/phpunit-mock-objects.git
git clone git://github.com/sebastianbergmann/phpunit-selenium.git
git clone git://github.com/sebastianbergmann/phpunit-story.git
git clone git://github.com/sebastianbergmann/php-invoker.git

# last versions without anonymous functions
cd dbunit && git checkout 1.1 && cd ..
cd php-code-coverage && git checkout 1.1 && cd ..
cd php-file-iterator && git checkout 1.3.2 && cd ..
cd php-invoker && git checkout 1.1.1 && cd ..
cd php-text-template && git checkout 1.1.2 && cd ..
cd php-timer && git checkout 1.0.3 && cd ..
cd php-token-stream && git checkout 1.1.4 && cd ..
cd phpunit && git checkout 3.6.12 && cd ..
cd phpunit-mock-objects && git checkout 1.1 && cd ..
cd phpunit-selenium && git checkout 1.1 && cd ..
cd phpunit-story && git checkout 1.0.0 && cd ..

sed -i 's/@package_version@/3.6.12/g' phpunit/PHPUnit/Runner/Version.php
cat phpunit/PHPUnit/Runner/Version.php

cd phpunit

php -d include_path='.:../phpunit/:../dbunit/:../php-code-coverage/:../php-file-iterator/:../php-invoker/:../php-text-template/:../php-timer:../php-token-stream:../phpunit-mock-objects/:../phpunit-selenium/:../phpunit-story/:/usr/local/lib/php' ../phpunit/phpunit.php --version

cd ..
cd ..

# memcache 2.1.0 is the last version to support the php 5.2 branch
pecl download memcached-2.1.0
tar zxvf memcached*.tgz && cd memcached*
make clean
printf "\n" | phpize
./configure --with-libmemcached-dir=/usr/local && make && make install

printf "\n" | pecl uninstall memcache
printf "\n" | pecl install memcache
