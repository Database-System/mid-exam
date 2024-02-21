<?php
require_once('./vendor/autoload.php');
if (!session_id()) session_start();
use Exam\Setting\Config;
use Exam\Route\Router;
use Exam\Utils\Utils;
new Router;
Utils::GetRoot();
var_dump($_SESSION);
echo "<br>" . "WEEEEEEEEEEEEEEEEEEEE" . "<br>";
