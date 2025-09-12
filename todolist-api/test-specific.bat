@echo off
echo ====================================
echo      TESTES ESPECIFICOS DA API
echo ====================================

if "%1"=="" (
    echo Uso: %0 [nome-do-teste]
    echo.
    echo Exemplos:
    echo   %0 auth          - Testes de autenticacao
    echo   %0 task          - Testes de tarefas
    echo   %0 category      - Testes de categorias
    echo   %0 endpoints     - Testes dos endpoints utilitarios
    echo   %0 all           - Todos os testes
    echo.
    pause
    exit /b 1
)

REM Verificar se PHP está disponível
where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERRO] PHP nao esta no PATH do sistema!
    echo Veja o arquivo run-tests.bat para instrucoes de instalacao.
    pause
    exit /b 1
)

if /i "%1"=="auth" (
    echo Executando testes de AUTENTICACAO...
    php vendor/bin/phpunit tests/Feature/AuthTest.php --testdox
) else if /i "%1"=="task" (
    echo Executando testes de TAREFAS...
    php vendor/bin/phpunit tests/Feature/TaskTest.php --testdox
) else if /i "%1"=="category" (
    echo Executando testes de CATEGORIAS...
    php vendor/bin/phpunit tests/Feature/CategoryTest.php --testdox
) else if /i "%1"=="endpoints" (
    echo Executando testes de ENDPOINTS UTILITARIOS...
    php vendor/bin/phpunit tests/Feature/TestEndpointsTest.php --testdox
) else if /i "%1"=="all" (
    echo Executando TODOS OS TESTES...
    php vendor/bin/phpunit --testdox
) else (
    echo Parametro invalido: %1
    echo Use: auth, task, category, endpoints ou all
)

echo.
pause
