@echo off
ECHO ======================================================
ECHO           Sistema de OS - Executando testes
ECHO ======================================================
ECHO.

REM Limpa o cache e reinicia a configuração
php artisan config:clear
php artisan cache:clear

ECHO.
ECHO ====================== TESTES UNITARIOS ======================
ECHO.
php artisan test --testsuite=Unit

ECHO.
ECHO ====================== TESTES DE FEATURE ======================
ECHO.
php artisan test --testsuite=Feature

ECHO.
ECHO ====================== TODOS OS TESTES ======================
ECHO.
php artisan test

ECHO.
ECHO ======================================================
ECHO                      Testes Concluídos
ECHO ======================================================
PAUSE
