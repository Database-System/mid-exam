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
            $user = htmlspecialchars($_POST["user"]) ?? '';
            $pass = $_POST["password"] ?? '';
            $dept = htmlspecialchars($_POST["dept"]) ?? '';
            $cls_name = htmlspecialchars($_POST["class"]) ?? '';
            if ($this->register($user, $pass, $dept, $cls_name)) {
                header('Location: /login');
            }
        } else new twigLoader(__FILE__, false, $this->OPTIONS);
    }
    private function register(string $user, string $pass, string $dept, string $cls_name): bool
    {
        $controller = new Controller();
        $password = password_hash($pass, PASSWORD_DEFAULT);
        if (!$controller->check_User($user)) {
            if (!$controller->insert_User($user, $password)) {
                $controller->Update_User_dept($user, $dept);
                $controller->Update_User_clsname($user, $cls_name);
                $controller->Insert_Request_Course($user, $dept, $cls_name);
                //$controller->updateTotalCredits($user,29);
                $controller->Update_User_TotalCerdits($user);
                header('Location: /login');
            }
            return true;
        }
        echo "<script type='text/javascript'>
            if (confirm('已註冊，要返回登入嗎')) {
                window.location.href = '/login';
            } else {
                window.location.href = '/';
            }
            </script>";
        return false;
    }
}
