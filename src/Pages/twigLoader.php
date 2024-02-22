<?php
namespace Exam\Pages;
if (!session_id()) session_start();
chdir(dirname(__FILE__));
require_once('../../vendor/autoload.php');
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigLoader{
    function __construct(string $path,bool $error=false){
        $loader = new FilesystemLoader($_SESSION['rTemplates_PATH']);
        $twig = new Environment($loader,['debug'=>true]);
        $twig->addGlobal('session', $_SESSION);
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        if(!isset($path)){
            die('Path is not set');
        }
        if (!$error) echo $twig->render(basename($path, '.php').'.twig');
        else echo $twig->render($path.'.twig');
    }
}
