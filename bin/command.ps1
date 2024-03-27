$scriptName = $MyInvocation.MyCommand.Name
switch ($args[0]) {
    "start" {
        docker compose up --build -d
    }
    "stop" {
        docker compose down
    }
    "restart" {
        docker compose restart
    }
    "test" {
        $testName = $args[1]
        $dir = Split-Path $script:MyInvocation.MyCommand.Path -Parent
        $dir = Resolve-Path "$dir\.."
        docker compose run --build --rm server ${dir}\vendor\bin\phpunit tests\${testName}.php
    }
    "composer" {
        $additionalArgs = $args[1..$args.Length] -join " "
        $dir = Split-Path $script:MyInvocation.MyCommand.Path -Parent
        $dir = Resolve-Path "$dir\.."
        docker run --rm -it -v "${dir}:/app" composer $additionalArgs
    }
    "watch" {
        docker compose watch
    }
    "ps" {
        docker compose ps
    }
    default {
        Write-Host "Usage: $scriptName {start|stop|restart|test|composer|watch|ps}"
        exit 1
    }
}