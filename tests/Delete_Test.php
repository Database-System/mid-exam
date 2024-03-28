<?php
use PHPUnit\Framework\TestCase;
use Exam\Core\Controller;

class Delete_Test extends TestCase
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

    public function testDeleteData()
    {
        // Delete test data
        self::$controller->delete_TimeTable(1312, 1, 1);
        self::$controller->delete_TimeTable(1312, 2, 1);

        self::$controller->delete_CourseTimeSlots(1312, 1);
        self::$controller->delete_CourseTimeSlots(1312, 2);
        self::$controller->delete_CourseTimeSlots(1314, 3);
        self::$controller->delete_CourseTimeSlots(1314, 4);

    }
}
?>