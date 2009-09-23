@echo off
IF EXIST "C:\program files\apache\php5\php.exe" GOTO :START
:ERROR
ECHO Error: wrong path to php.exe
ECHO Edit doctrine-cli.bat to set up correct path
GOTO :END
:START
"C:\program files\apache\php5\php.exe" doctrine-cli.sh %1
:END