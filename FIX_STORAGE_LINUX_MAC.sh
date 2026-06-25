#!/usr/bin/env bash
set -e
cd "$(dirname "$0")"
rm -rf public/storage
php artisan storage:link
php artisan optimize:clear
