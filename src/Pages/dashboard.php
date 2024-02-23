<?php

namespace Exam\Pages;

use Exam\Pages\twigLoader;

class Dashboard
{
    public function __construct()
    {
        new twigLoader(__FILE__);
    }
}
