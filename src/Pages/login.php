<?php

namespace Exam\Pages;

if (!session_id()) session_start();

use Exam\Core\Controller;
use Exam\Pages\twigLoader;

class Login
{
    private array $OPTIONS = [
        "title" => "Login",
        "formTitle" => "FCU Test Login"
    ];
    public function __construct()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user = $_POST["user"] ?? '';
            $pass = $_POST["password"] ?? '';
            $this->login($user,$pass);
        } else new twigLoader(__FILE__, false, $this->OPTIONS);

    }

    //TODO 需要添加登入邏輯
    private function login(string $user, string $password){
        $controller = new Controller();
        $data = $controller->check_User($user);
        //die(var_dump($data));
        if (password_verify($password , $data['password'])) {
            echo 'Password is valid!';
        } else {
            echo 'Invalid password.';
        }
    }
    
}
