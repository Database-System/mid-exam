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
    public function searchinserttable()
    {
        $timeSlot = self::$controller->search_TimeSlot("Monday", "08:00:00", "10:00:00");
        $this->assertEquals("Monday", $timeSlot['day']);
        $this->assertEquals("08:00:00", $timeSlot['start_time']);
        $this->assertEquals("10:00:00", $timeSlot['end_time']);

        $course = self::$controller->search_Course(1312, "System_Program", 3);
        $this->assertEquals(1312, $course['ID']);
        $this->assertEquals("System_Program", $course['Name']);
        $this->assertEquals(3, $course['Credits']);

        $courseTimeSlots = self::$controller->search_CourseTimeSlots(1312, 1);
        $this->assertEquals(1312, $courseTimeSlots['Course_ID']);
        $this->assertEquals(1, $courseTimeSlots['Time_Slot_ID']);

        $timeTable = self::$controller->search_TimeTable(1312, 1, 1);
        $this->assertEquals(1312, $timeTable['course_ID']);
        $this->assertEquals(1, $timeTable['time_slot_id']);
        $this->assertEquals(1, $timeTable['user_id']);

    }

    public function testDeleteData()
    {
        // Delete test data
        self::$controller->delete_TimeTable(1312, 1, 1);
        self::$controller->delete_TimeTable(1312, 2, 1);
        
        self::$controller->delete_CourseTimeSlots(1312, 1);
        self::$controller->delete_CourseTimeSlots(1312, 2);
        self::$controller->delete_CourseTimeSlots(1314, 3);
        self::$controller->delete_CourseTimeSlots(1314, 4);
        
        self::$controller->delete_Course(1312,"System_Program", 3);
        self::$controller->delete_Course(1314, "Probability_and_Statistics", 3);
        
        self::$controller->delete_TimeSlot("Monday", "08:00:00", "10:00:00");
        self::$controller->delete_TimeSlot("Wedensday", "11:10:00", "12:00:00");
        self::$controller->delete_TimeSlot("Monday", "13:10:00", "15:00:00");
        self::$controller->delete_TimeSlot("Tuesday", "11:10:00", "12:00:00");
        self::$controller->delete_TimeSlot("Monday", "15:10:00", "17:00:00");
    }

    public function searchdeletetable()
    {
        $timeSlot = self::$controller->search_TimeSlot("Monday", "08:00:00", "10:00:00");
        $this->assertFalse($timeSlot);

        $course = self::$controller->search_Course(1312, "System_Program", 3);
        $this->assertFalse($course);

        $courseTimeSlots = self::$controller->search_CourseTimeSlots(1312, 1);
        $this->assertFalse($courseTimeSlots);

        $timeTable = self::$controller->search_TimeTable(1312, 1, 1);
        $this->assertFalse($timeTable);
    }

    public static function tearDownAfterClass(): void
    {
        self::$controller = null;
    }
}
?>
