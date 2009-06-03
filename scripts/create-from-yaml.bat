@echo off
call doctrine-cli.bat generate-models-yaml
call doctrine-cli.bat generate-sql
call doctrine-cli.bat create-tables