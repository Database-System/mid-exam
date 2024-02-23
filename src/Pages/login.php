<?php
namespace Exam\Pages;
if (!session_id()) session_start();
use Exam\Pages\twigLoader;
use Exam\Core\Connect;
class Login {
    private static array $OPTIONS = [
        "title" => "Login",
        "formTitle" => "FCU Test Login"
    ];
    public function __construct() {
        $connect = new Connect();
        $handler = $connect->getHandler();
        if (isset($_POST["email"]) && isset($_POST["password"])){
            
        }
        else new twigLoader(__FILE__,false,self::$OPTIONS);
    }
    private function getPost(){

    }
    //TODO 需要添加登入邏輯
}
