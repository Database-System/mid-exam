<?php

namespace Exam\Pages;

use Exam\Pages\twigLoader;
use Exam\Core\Controller;

class Register
{
    private array $OPTIONS = [
        "title" => "Add Your NID",
        "formTitle" => "FCU Test SignUp"
    ];
    public function __construct()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user = $_POST["user"] ?? '';
            $pass = $_POST["password"] ?? '';
            $this->register($user, $pass);
            header('Location: /login');
        } else new twigLoader(__FILE__, false, $this->OPTIONS);
    }
    private function register(string $user,string $pass){
        $controller = new Controller();
        $password = password_hash($pass, PASSWORD_DEFAULT);
        if (!$controller->check_User($user)) $controller->insert_User($user, $password);
    }
}
