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
        // Clean up any inserted data after all tests in the class have run
        // This ensures that subsequent test runs are not affected by previous runs
        self::$controller->cleanupTestData();
    }

    public function testInsertData()
    {
        // Insert test data
        self::$controller->insert_TimeSlot("Monday", "08:00:00", "10:00:00");
        self::$controller->insert_Course(1, "Math", 3);
        self::$controller->insert_CourseTimeSlots(1, 1);
        self::$controller->insert_TimeTable(1, 1, 1);
        
        // Verify the inserted data
        $timeSlot = self::$controller->getTimeSlot(1);
        $this->assertNotNull($timeSlot);
        $this->assertEquals("Monday", $timeSlot['day']);
        $this->assertEquals("08:00:00", $timeSlot['start_time']);
        $this->assertEquals("10:00:00", $timeSlot['end_time']);

        $course = self::$controller->getCourse(1);
        $this->assertNotNull($course);
        $this->assertEquals("Math", $course['Name']);
        $this->assertEquals(3, $course['Credits']);

        $courseTimeSlots = self::$controller->getCourseTimeSlots(1);
        $this->assertNotNull($courseTimeSlots);

        $timeTable = self::$controller->getTimeTable(1, 1, 1);
        $this->assertNotNull($timeTable);
    }
}
?>
