<?php

namespace Exam\Pages;

use Exam\Pages\twigLoader;

class Errors
{
    public function __construct(string $code)
    {
        new twigLoader($code, true);
    }
}
