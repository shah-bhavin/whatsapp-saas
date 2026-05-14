@echo off

cd /d C:\laragon\www\whatsapp-saas

start /min cmd /c "php artisan queue:work"

start /min cmd /c "php artisan reverb:start"

start /min cmd /c "npm run dev"