#!/bin/bash
composer install #--no-dev --optimize-autoloader
php bin/console doctrine:migrations:migrate -n
yarn install
yarn encore production
php bin/console cache:clear