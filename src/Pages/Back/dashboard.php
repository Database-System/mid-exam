<?php

namespace Exam\Pages\Back;

use Exam\Pages\twigLoader;
use Exam\Utils\Utils;

class Dashboard
{
    public function __construct()
    {
        Utils::isLogin();
        new twigLoader(__FILE__);
    }
}
