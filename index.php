<?php
require_once('./vendor/autoload.php');

use Exam\Setting\Config;

var_dump(Config::get("db_host"));
echo "<br>" . "Hello, world!" . "<br>";
