# Script para executar testes no PowerShell

Write-Host "======================================================" -ForegroundColor Cyan
Write-Host "           Sistema de OS - Executando testes" -ForegroundColor Cyan
Write-Host "======================================================" -ForegroundColor Cyan
Write-Host ""

# Limpa o cache e reinicia a configuração
Write-Host "Limpando cache e configurações..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear

Write-Host ""
Write-Host "====================== TESTES UNITARIOS ======================" -ForegroundColor Green
Write-Host ""
php artisan test --testsuite=Unit

Write-Host ""
Write-Host "====================== TESTES DE FEATURE ======================" -ForegroundColor Green
Write-Host ""
php artisan test --testsuite=Feature

Write-Host ""
Write-Host "====================== TODOS OS TESTES ======================" -ForegroundColor Green
Write-Host ""
php artisan test

Write-Host ""
Write-Host "======================================================" -ForegroundColor Cyan
Write-Host "                      Testes Concluídos" -ForegroundColor Cyan
Write-Host "======================================================" -ForegroundColor Cyan

Read-Host -Prompt "Pressione ENTER para sair"
