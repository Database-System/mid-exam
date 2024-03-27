function Invoke-DockerCompose {
    param (
        [string]$command,
        [string]$additionalArgs = ""
    )

    
    # Set-Location $dir

    # docker compose $command $additionalArgs
    Write-Host "docker-compose $command $additionalArgs"
}

$scriptName = $MyInvocation.MyCommand.Name
switch ($args[0]) {
    "start" {
        Invoke-DockerCompose -command "up --build -d"
    }
    "stop" {
        Invoke-DockerCompose -command "down"
    }
    "restart" {
        Invoke-DockerCompose -command "restart"
    }
    "test" {
        $testName = $args[1]
        $dir = Split-Path $script:MyInvocation.MyCommand.Path -Parent
        $dir = Resolve-Path "$dir\.."
        cd $dir
        Invoke-DockerCompose -command "run --build --rm server .\vendor\bin\phpunit tests\$testName.php"
    }
    "composer" {
        $additionalArgs = $args[1..$args.Length] -join " "
        $dir = Split-Path $script:MyInvocation.MyCommand.Path -Parent
        $dir = Resolve-Path "$dir\.."
        docker run --rm -it -v "${dir}:/app" composer $additionalArgs
    }
    "watch" {
        Invoke-DockerCompose -command "watch"
    }
    "ps" {
        Invoke-DockerCompose -command "ps"
    }
    default {
        Write-Host "Usage: $scriptName {start|stop|restart|test|composer|watch|ps}"
        exit 1
    }
}