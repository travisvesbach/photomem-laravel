#!/bin/bash
# this script runs once a container is started.

# composer install
composer install

# check if sqlite is being used
# create database
# run migrations
connection=$(grep DB_CONNECTION .env | xargs)
connection=${connection#*=}
if [[ $connection = sqlite ]]; then
    db_path=$(grep DB_DATABASE .env | xargs)
    db_path=${db_path#*=}
    touch $db_path
    php artisan migrate
fi

# start worker
php artisan queue:work --verbose --tries=3 --timeout=90
