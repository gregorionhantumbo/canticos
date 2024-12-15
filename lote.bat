@echo off
:: Caminho para o WAMP Server
start "" "C:\wamp64\wampmanager.exe"

:: Aguarde alguns segundos para o servidor iniciar
timeout /t 0 /nobreak > nul

:: Abra o navegador no projeto
start "" "http://localhost/Eucanto/eucanto/"

:: Fechar automaticamente após execução
exit
