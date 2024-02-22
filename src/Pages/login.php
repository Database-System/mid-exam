<?php
namespace Exam\Pages;
if (!session_id()) session_start();
use Exam\Pages\twigLoader;
use Exam\Core\Connect;
class Login {
    public function __construct() {
        $connect = new Connect();
        $handler = $connect->getHandler();
        new twigLoader(__FILE__);
    }
}
