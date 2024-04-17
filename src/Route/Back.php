<?php

namespace Exam\Route;

if (!session_id()) session_start();
class Back
{
    private static $ROUTES = [];

    public static function addRoute($path, $class, $method = null, $constructorParams = [])
    {
        self::$ROUTES[$path] = ['class' => $class, 'method' => $method, 'params' => $constructorParams];
    }

    public static function delRoute($path)
    {
        if (array_key_exists($path, self::$ROUTES)) unset(self::$ROUTES[$path]);
    }


    private function dispatch($url)
    {
        if (!array_key_exists($url, self::$ROUTES)) {
            header('Location: /404');
            die();
        } else {
            $route = self::$ROUTES[$url];
            $classWithNamespace = "\\Exam\\Pages\\Back\\" . $route['class'];
            if ($route['method']) {
                $reflectionClass = new \ReflectionClass($classWithNamespace);
                $instance = $reflectionClass->newInstanceArgs($route['params']);
                if (!method_exists($instance, $route['method'])) die("Method {$route['method']} not found in class {$route['class']}");
                call_user_func([$instance, $route['method']]);
            } else {
                $reflectionClass = new \ReflectionClass($classWithNamespace);
                $instance = $reflectionClass->newInstanceArgs($route['params']);
            }
        }
    }
    private function init_Routes()
    {
        self::addRoute("/dashboard", "dashboard");
        self::addRoute("/logout", "logout");
    }
    public function __construct(string $url)
    {
        if (!isset($_SESSION['userID'])) {
            header('Location: /404');
            die();
        }
        $this->init_Routes();
        $this->dispatch($url);
    }
}
