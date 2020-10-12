#!/bin/bash
composer self-update
composer install #--no-dev --optimize-autoloader
php bin/console doctrine:migrations:migrate -n
yarn encore production
php bin/console cache:clear