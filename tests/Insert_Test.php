<?php
require_once './src/Core/Controller.php';

class Hello_Test extends PHPUnit\Framework\TestCase
{
 public function testOutput()
 {
    // Capture the output of hello.php
    ob_start();
    include './src/Core/table.php';
    $output = ob_get_clean();

    // Assert that the output is "Hello, Docker!"
    $this->assertEquals("Hello, Docker!", $output);
 }
}