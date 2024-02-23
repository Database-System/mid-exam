<?php

namespace Exam\Pages;

use Exam\Pages\twigLoader;

class Dashboard
{
    public function __construct()
    {
        if (!isset($_SESSION['userID'])) {
            header('Location: /login');
            exit;
        }
        new twigLoader(__FILE__);
    }
}
