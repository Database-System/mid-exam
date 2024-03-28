<?php
require_once './src/hello.php';

class Hello_Test extends PHPUnit\Framework\TestCase
{
   public function testOutput()
   {
      // Capture the output of hello.php
      ob_start();
      include './src/hello.php';
      $output = ob_get_clean();

      // Assert that the output is "Hello, Docker!"
      $this->assertEquals("10Hello, Docker!", $output);
   }
   public function testOutput1()
   {
      $this->markTestSkipped(
         'This test has not been implemented yet.'
      );
   }
}
