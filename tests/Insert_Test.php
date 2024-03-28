<?php
use PHPUnit\Framework\TestCase;
use Exam\Core\Controller;

class Insert_Test extends TestCase
{
    protected static $controller;

    public static function setUpBeforeClass(): void
    {
        self::$controller = new Controller();
    }

    public static function tearDownAfterClass(): void
    {
        self::$controller->cleanupTestData();
    }

    public function testInsertData()
    {
        // Insert test data
        self::$controller->insert_TimeSlot("Monday", "08:00:00", "10:00:00");
        self::$controller->insert_TimeSlot("Wedensday", "11:10:00", "12:00:00");
        self::$controller->insert_TimeSlot("Monday", "13:10:00", "15:00:00");
        self::$controller->insert_TimeSlot("Tuesday", "11:10:00", "12:00:00");
        self::$controller->insert_TimeSlot("Monday", "15:10:00", "17:00:00");
        
        self::$controller->insert_Course(1312, "System_Program", 3);
        self::$controller->insert_Course(1314, "Probability_and_Statistics", 3);

        self::$controller->insert_CourseTimeSlots(1312, 1);
        self::$controller->insert_CourseTimeSlots(1312, 2);
        self::$controller->insert_CourseTimeSlots(1314, 3);
        self::$controller->insert_CourseTimeSlots(1314, 4);

        self::$controller->insert_TimeTable(1312, 1, 1);
        self::$controller->insert_TimeTable(1312, 2, 1);
        

    }
}
?>
