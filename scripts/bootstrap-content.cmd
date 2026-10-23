@echo off
cd /d "%~dp0.."
python scripts\bootstrap_content.py %*
if errorlevel 1 (
  echo.
  echo If python is not found, try: py -3 scripts\bootstrap_content.py %*
  exit /b 1
)
