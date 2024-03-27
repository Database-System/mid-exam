<?php
require_once './src/Core/Controller.php'; 

class Controller_Test extends PHPUnit\Framework\TestCase
{
    public function testDisplayUserTimeTable()
    { 
        $controller = new Controller();
        include './src/Core/Controller.php';
        ob_start();
        $controller->display_User_TimeTable('test_user');
        $output = ob_get_clean();
        $this->assertContains('課程名稱', $output); 
    }
}
