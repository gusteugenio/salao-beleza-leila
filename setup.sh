#!/bin/bash

docker compose up -d --build

docker compose exec php composer install

docker compose exec php php artisan key:generate

docker compose exec php php artisan migrate --seed
