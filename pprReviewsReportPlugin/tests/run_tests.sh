#!/usr/bin/env sh

composer.phar --working-dir=tests install
./tests/lib/vendor/phpunit/phpunit/phpunit --debug --configuration ./tests/phpunit.xml -v tests/src/
