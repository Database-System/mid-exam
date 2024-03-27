@echo off
cd %~dp0..
set DIR=%cd%
set NAME=%0

if "%1"=="start" (
    docker compose up --build -d
) else if "%1"=="stop" (
    docker compose down
) else if "%1"=="restart" (
    docker compose restart
) else if "%1"=="test" (
    shift
    docker compose run --build --rm server ./vendor/bin/phpunit tests/%1.php
) else if "%1"=="composer" (
    shift
    docker run --rm -it -v %DIR%:/app composer %*
) else if "%1"=="watch" (
    docker compose watch
) else if "%1"=="ps" (
    docker compose ps
) else (
    echo Usage: %NAME% {start|stop|restart|test|composer|watch|ps}
    exit /b 1
)

