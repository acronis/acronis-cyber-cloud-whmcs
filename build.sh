#!/usr/bin/env bash
#
# @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
#

set -e

/usr/local/bin/composer install
php ./vendor/phpunit/phpunit/phpunit --configuration ./tests/phpunit.xml ./tests

cp "Vers.ion" "src/includes/acroniscloud/Vers.ion"

cd src
zip -r ../AcronisModulesForWHMCS.zip .