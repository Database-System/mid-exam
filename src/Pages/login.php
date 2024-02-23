<?php
namespace Exam\Pages;
if (!session_id()) session_start();

use Exam\Core\Controller;
use Exam\Pages\twigLoader;
class Login {
    private array $OPTIONS = [
        "title" => "Login",
        "formTitle" => "FCU Test Login"
    ];
    public function __construct() {
        if (isset($_POST["user"]) && isset($_POST["password"])){
            $user = $_POST["user"] ?? '';
            $pass = $_POST["password"] ?? '';
            $this->checkUser($user, $pass);
        }
        else new twigLoader(__FILE__,false,$this->OPTIONS);
    }
    private function checkUser(string $username, string $password) {
        var_dump($username,$password);
    }
    //TODO 需要添加登入邏輯
}
