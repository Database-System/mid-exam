<?php
if (!session_id()) session_start();
require_once('./vendor/autoload.php');
use Exam\Route\Router;
new Router;
use Exam\Utils\Utils;
use Exam\Pages\twigLoader;
Utils::GetRoot();
new twigLoader(__FILE__);