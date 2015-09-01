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

# last versions without anonymous functions
git clone --depth=1 --branch=3.6.12 git://github.com/sebastianbergmann/phpunit.git
git clone --depth=1 --branch=1.1 git://github.com/sebastianbergmann/dbunit.git
git clone --depth=1 --branch=1.3.2 git://github.com/sebastianbergmann/php-file-iterator.git
git clone --depth=1 --branch=1.1.2 git://github.com/sebastianbergmann/php-text-template.git
git clone --depth=1 --branch=1.1 git://github.com/sebastianbergmann/php-code-coverage.git
git clone --depth=1 --branch=1.1.4 git://github.com/sebastianbergmann/php-token-stream.git
git clone --depth=1 --branch=1.0.3 git://github.com/sebastianbergmann/php-timer.git
git clone --depth=1 --branch=1.1 git://github.com/sebastianbergmann/phpunit-mock-objects.git
git clone --depth=1 --branch=1.1 git://github.com/sebastianbergmann/phpunit-selenium.git
git clone --depth=1 --branch=1.0.0 git://github.com/sebastianbergmann/phpunit-story.git
git clone --depth=1 --branch=1.1.1 git://github.com/sebastianbergmann/php-invoker.git

sed -i 's/@package_version@/3.6.12/g' phpunit/PHPUnit/Runner/Version.php
cat phpunit/PHPUnit/Runner/Version.php

cd phpunit

php -d include_path='.:../phpunit/:../dbunit/:../php-code-coverage/:../php-file-iterator/:../php-invoker/:../php-text-template/:../php-timer:../php-token-stream:../phpunit-mock-objects/:../phpunit-selenium/:../phpunit-story/:/usr/local/lib/php' ../phpunit/phpunit.php --version
