@echo off
setlocal

:: ══════════════════════════════════════════════════════════
:: CrockeriesMart - Database Auto Backup Script
:: Schedule this via Windows Task Scheduler (taskschd.msc)
:: Recommended: Daily at 2:00 AM
:: ══════════════════════════════════════════════════════════

set MYSQL="C:\xampp\mysql\bin\mysqldump.exe"
set BACKUP_DIR="C:\xampp\htdocs\CrokersesMart\storage\backups"
set DB_NAME=crockeriesmart
set DB_USER=root
set DB_PASS=

:: Generate timestamp (YYYYMMDD_HHMMSS)
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value') do set dt=%%I
set TIMESTAMP=%dt:~0,4%%dt:~4,2%%dt:~6,2%_%dt:~8,2%%dt:~10,2%%dt:~12,2%

:: Create backup
%MYSQL% --user=%DB_USER% --password=%DB_PASS% --host=localhost --single-transaction --routines --triggers --result-file="%BACKUP_DIR%\crockeriesmart_%TIMESTAMP%.sql" %DB_NAME%

:: Delete backups older than 30 days
forfiles /p %BACKUP_DIR% /s /m *.sql /d -30 /c "cmd /c del @path" 2>nul

endlocal
