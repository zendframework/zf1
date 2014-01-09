#!/bin/sh

pear update-channels
pear upgrade-all
pear config-set auto_discover 1

pear install pear.phpunit.de/DbUnit
