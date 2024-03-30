<?php
use PHPUnit\Framework\TestCase;
use Exam\Core\Controller;
require_once './src/Core/Controller.php';

class Insert_and_Delete_Test extends TestCase
{
    protected static $controller;

    public static function setUpBeforeClass(): void
    {
        self::$controller = new Controller();
       
        $reflection = new \ReflectionClass(self::$controller);
        $handlerProperty = $reflection->getProperty('handler');
        $handlerProperty->setAccessible(true);
        $handler = $handlerProperty->getValue(self::$controller);

        $handler->exec("ALTER TABLE TimeSlot AUTO_INCREMENT = 1;");
    }

    public function testInsertData()
    {
        // Insert test data and assert that each insertion is successful
        $this->assertTrue(self::$controller->insert_TimeSlot("Monday", "08:00:00", "10:00:00"));
        $this->assertTrue(self::$controller->insert_TimeSlot("Wednesday", "11:10:00", "12:00:00")); // Corrected spelling of "Wednesday"
        $this->assertTrue(self::$controller->insert_TimeSlot("Monday", "13:10:00", "15:00:00"));
        $this->assertTrue(self::$controller->insert_TimeSlot("Tuesday", "11:10:00", "12:00:00"));
        $this->assertTrue(self::$controller->insert_TimeSlot("Monday", "15:10:00", "17:00:00"));
        
        $this->assertTrue(self::$controller->insert_Course(1312, "System_Program", 3));
        $this->assertTrue(self::$controller->insert_Course(1314, "Probability_and_Statistics", 3));

        $this->assertTrue(self::$controller->insert_CourseTimeSlots(1312, 1));
        $this->assertTrue(self::$controller->insert_CourseTimeSlots(1312, 2));
        $this->assertTrue(self::$controller->insert_CourseTimeSlots(1314, 3));
        $this->assertTrue(self::$controller->insert_CourseTimeSlots(1314, 4));

        $this->assertTrue(self::$controller->insert_TimeTable(1312, 1, 1));
        $this->assertTrue(self::$controller->insert_TimeTable(1312, 2, 1));
    }

    public function testDeleteData()
    {
        // Delete test data and assert that each deletion is successful
        $this->assertTrue(self::$controller->delete_TimeTable(1312, 1, 1));
        $this->assertTrue(self::$controller->delete_TimeTable(1312, 2, 1));
        
        $this->assertTrue(self::$controller->delete_CourseTimeSlots(1312, 1));
        $this->assertTrue(self::$controller->delete_CourseTimeSlots(1312, 2));
        $this->assertTrue(self::$controller->delete_CourseTimeSlots(1314, 3));
        $this->assertTrue(self::$controller->delete_CourseTimeSlots(1314, 4));
        
        $this->assertTrue(self::$controller->delete_Course(1312, "System_Program", 3));
        $this->assertTrue(self::$controller->delete_Course(1314, "Probability_and_Statistics", 3));
        
        $this->assertTrue(self::$controller->delete_TimeSlot("Monday", "08:00:00", "10:00:00"));
        $this->assertTrue(self::$controller->delete_TimeSlot("Wednesday", "11:10:00", "12:00:00")); // Corrected spelling
        $this->assertTrue(self::$controller->delete_TimeSlot("Monday", "13:10:00", "15:00:00"));
        $this->assertTrue(self::$controller->delete_TimeSlot("Tuesday", "11:10:00", "12:00:00"));
        $this->assertTrue(self::$controller->delete_TimeSlot("Monday", "15:10:00", "17:00:00"));
    }
}
