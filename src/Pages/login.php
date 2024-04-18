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
            $this->login($user, $pass);
        }
        if (isset($_SESSION['userID'])) header('Location: /back/dashboard');
        else new twigLoader(__FILE__, false, $this->OPTIONS);
    }

    private function login(string $user, string $password)
    {
        $controller = new Controller();
        $data = $controller->check_User($user);
        if (is_array($data) && password_verify($password, $data['password'])) $this->mark_User($data);
        else echo "<script type='text/javascript'>
        alert('帳號密碼錯誤');
        window.location.href = '/login';
        </script>";
    }
    private function mark_User(array $data)
    {
        $_SESSION['userID'] = $data["username"];
        header('Location: /back/dashboard');
    }
}
