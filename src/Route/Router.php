<?php
namespace Exam\Route;
use Exam\Utils\Utils;
if (!session_id()) session_start();
// chdir(dirname(__FILE__));
// require_once('../../vendor/autoload.php');
define("ROOT_PATH", dirname(dirname(dirname(__FILE__))) . '/');
define("rVendor_PATH", ROOT_PATH . 'vendor' . '/');
define("rCore_PATH", ROOT_PATH . 'src/Core' . '/');
define("rUtils_PATH", ROOT_PATH . 'src/Utils' . '/');
define("rRoute_PATH", ROOT_PATH . 'src/Route' . '/');
define("rSetting_PATH", ROOT_PATH . 'src/Setting' . '/');
define("rPages_PATH", ROOT_PATH . 'src/Pages' . '/');
define('rTemplates_PATH', ROOT_PATH . 'src/Templates' . '/');
//Define web Url
define('Web_Root_Path', $_SESSION['WEB_ROOT'] .'/'); 
define('Error_PATH', Web_Root_Path . 'src/Errors' . '/');
define('Resource_PATH', Web_Root_Path . 'src/Resource' . '/');
define('Pages_PATH', Web_Root_Path . 'src/Pages' . '/');
//Define Resource Path
define('Image_PATH', Resource_PATH . 'Images' . '/');
define('Css_PATH', Resource_PATH . 'Css' . '/');
define('Fonts_PATH', Resource_PATH . 'Fonts' . '/');
define('Js_PATH', Resource_PATH . 'Js' . '/');
define('Templates_PATH', Web_Root_Path . 'src/Templates' . '/');
define("Base", rRoute_PATH . 'Base.php');
// define('Logout', Web_Root_Path . 'Logout.php');

class Router 
{
    function __construct()
    {
        $_SESSION["Config"] = rSetting_PATH . 'config.php';
        $ret = Utils::OutputFiles(rPages_PATH);
        foreach ($ret as $file) {
            $_SESSION["Route-".Utils::strriposfunction($val=$file,$type="1")] = $file;
        }
        foreach(get_defined_constants(true)["user"] as $key => $val) {
            $_SESSION[$key] = $val;
        }
    }
    
    
}
