@echo off
title Sistem Informasi Data Arsip - Dinas Kesehatan Kota Bandung
echo ========================================================
echo   SISTEM INFORMASI DATA ARSIP - DINAS KESEHATAN KOTA BANDUNG
echo ========================================================
echo.
echo Mempersiapkan server Laravel...

set "PHPRC=%~dp0"
set "PATH=C:\Program Files\php-8.5.12;C:\laragon\bin\composer;%PATH%"

echo Server berjalan di: http://localhost:8000
echo.
echo Tekan Ctrl+C untuk menghentikan server.
echo ========================================================
echo.

php artisan serve --port=8000
pause

