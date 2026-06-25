@echo off
cd /d %~dp0
if exist public\storage rmdir /s /q public\storage
php artisan storage:link
php artisan optimize:clear
pause
