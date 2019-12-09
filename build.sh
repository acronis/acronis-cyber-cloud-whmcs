#!/usr/bin/env bash
#
# @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
#

set -e

/usr/local/bin/composer install
php ./vendor/phpunit/phpunit/phpunit --configuration ./tests/phpunit.xml ./tests

cp "Vers.ion" "src/includes/acroniscloud/Vers.ion"

cd src
zip -r ../AcronisModulesForWHMCS.zip .