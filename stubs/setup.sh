#!/bin/bash


php artisan optimize:clear
php artisan key:generate
php artisan optimize
php artisan route:clear
php artisan route:cache
php artisan migrate:fresh --seed

ln -s #--SOURCE_FILE--# #--SYMBOLIC-LINK--#
