@echo off
echo ====================================
echo       EXECUTANDO TESTES DA API
echo ====================================

REM Verificar se PHP está disponível
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERRO] PHP nao esta no PATH do sistema!
    echo.
    echo OPCOES PARA RESOLVER:
    echo 1. Instalar PHP e adicionar ao PATH
    echo 2. Usar XAMPP/WAMP/LARAGON e adicionar PHP ao PATH
    echo 3. Usar Docker com Sail
    echo.
    echo Exemplo para adicionar PHP ao PATH:
    echo - XAMPP: C:\xampp\php
    echo - WAMP: C:\wamp64\bin\php\php8.x
    echo - LARAGON: C:\laragon\bin\php\php8.x
    echo.
    pause
    exit /b 1
)

echo [INFO] PHP encontrado! Executando testes...
echo.

REM Executar todos os testes
echo ====================================
echo         TODOS OS TESTES
echo ====================================
php vendor/bin/phpunit --testdox

echo.
echo ====================================
echo    TESTES COM COBERTURA RESUMIDA
echo ====================================
php vendor/bin/phpunit --testdox --colors=always

echo.
echo ====================================
echo         TESTES CONCLUIDOS
echo ====================================
pause
