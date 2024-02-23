<?php

namespace Exam\Pages;

use Exam\Pages\twigLoader;

class Home
{
    public function __construct()
    {
        new twigLoader(__FILE__);
    }
}
