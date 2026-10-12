@echo off
title UPHS Laboratory Management System - GUI Launcher
cd /d "%~dp0"

echo =====================================================================
echo  University of Perpetual Help System - Laboratory Management System
echo  Launching Standalone Desktop Offline GUI Control Panel...
echo =====================================================================

powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0Launch-GUI.ps1"

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo An error occurred launching the GUI control panel.
    pause
)
