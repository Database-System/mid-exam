<?php
namespace Exam\Pages;
if (!session_id()) session_start();
require_once($_SESSION['rVendor_PATH'] . 'autoload.php');
use Exam\Pages\twigLoader;
use Exam\Core\Connect;
$connect = new Connect();
$handler = $connect->getHandler();
new twigLoader(__FILE__);