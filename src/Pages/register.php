<?php

namespace Exam\Pages;

use Exam\Pages\twigLoader;

class Register
{
    private array $OPTIONS = [
        "title" => "Add Your NID",
        "formTitle" => "FCU Test SignUp"
    ];
    public function __construct()
    {
        if (isset($_POST["user"]) && isset($_POST["password"])) {
            $user = $_POST["user"] ?? '';
            $pass = $_POST["password"] ?? '';
            $this->checkUser($user, $pass);
        } else new twigLoader(__FILE__, false, $this->OPTIONS);
    }
    private function checkUser(string $username, string $password)
    {
        var_dump($username, $password);
    }
}
