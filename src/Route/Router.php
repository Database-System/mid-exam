<?php

namespace Exam\Route;

use Exam\Utils\Utils;

Utils::GetRoot();
define("ROOT_PATH", dirname(dirname(dirname(__FILE__))) . '/');
define("rVendor_PATH", ROOT_PATH . 'vendor' . '/');
define("rCore_PATH", ROOT_PATH . 'src/Core' . '/');
define("rUtils_PATH", ROOT_PATH . 'src/Utils' . '/');
define("rRoute_PATH", ROOT_PATH . 'src/Route' . '/');
define("rSetting_PATH", ROOT_PATH . 'src/Setting' . '/');
define("rPages_PATH", ROOT_PATH . 'src/Pages' . '/');
define('rTemplates_PATH', ROOT_PATH . 'src/Templates' . '/');
//Define web Url
define('Web_Root', $_SESSION['WEB_ROOT'] . '/');
define('Resource', Web_Root . 'src/Resource' . '/');
define('Pages', Web_Root . 'src/Pages' . '/');
//Define Resource Path
define('Image', Resource . 'Images' . '/');
define('Css', Resource . 'Css' . '/');
define('Fonts', Resource . 'Fonts' . '/');
define('Js', Resource . 'Js' . '/');
define('Templates', Web_Root . 'src/Templates' . '/');
define("Base", rRoute_PATH . 'Base.php');
class Router
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
            header('location: /404');
            die();
        }
        $route = self::$ROUTES[$url];
        $classWithNamespace = "\\Exam\\Pages\\" . $route['class'];
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
    private function init_Session()
    {
        $_SESSION["Config"] = rSetting_PATH . 'config.php';
        $ret = Utils::OutputFiles(rPages_PATH);
        foreach ($ret as $file) {
            $_SESSION["Route-" . Utils::strriposfunction($val = $file, $type = "1")] = $file;
        }
        foreach (get_defined_constants(true)["user"] as $key => $val) {
            $_SESSION[$key] = $val;
        }
    }
    private function init_Routes()
    {
        self::addRoute('/', 'home');
        self::addRoute('/login', 'login');
        self::addRoute('/404', 'errors', null, ["404"]);
        self::addRoute("/dashboard", "dashboard");
        self::addRoute("/signup", "register");
        self::addRoute("/logout","logout");
    }
    public function __construct()
    {
        self::init_Session();
        self::init_Routes();
        self::dispatch(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    }
}
