<?php
namespace Exam\Pages;
if (!session_id()) session_start();
require_once($_SESSION['rVendor_PATH'] . 'autoload.php');
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Exam\Route\Router;
use Exam\Utils\Utils;
new Router;
Utils::GetRoot();
class TwigLoader{
    function __construct(string $path = null){
        $loader = new FilesystemLoader($_SESSION['rTemplates_PATH']);
        $twig = new Environment($loader);
        if(!isset($path)){
            die('Path is not set');
        }
        echo $twig->render(basename($path, '.php').'.twig');
    }
}
