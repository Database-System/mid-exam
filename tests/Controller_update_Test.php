<?php
use PHPUnit\Framework\TestCase;
use Exam\Core\Controller;
require_once './src/Core/Controller.php';

class Controller_update_Test extends TestCase{
    public static $controller;

    public static function setUpBeforeClass():void{
        self::$controller =new Controller();

        $reflection = new \ReflectionClass(self::$controller);
        $handlerProperty = $reflection->getProperty('handler');
        $handlerProperty->setAccessible(true);
        $handler = $handlerProperty->getValue(self::$controller);

        $handler->exec("ALTER TABLE TimeSlot AUTO_INCREMENT = 1;");
    }

    public function testInsertData()
    {
        // Insert test data and assert that each insertion is successful
        self::$controller->insert_TimeSlot("Monday", "08:00:00", "10:00:00");
        self::$controller->insert_TimeSlot("Wednesday", "11:10:00", "12:00:00"); // Corrected spelling of "Wednesday"
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

    public function testSearch(){
        $result1= self::$controller->search_User_TimeTable('test_user',1312);
        $this->assertFalse($result1);

        $result2= self::$controller->search_User_TimeTable('test_user',1314);
        $this->assertFalse($result2);
    }
    public function testDisplayUserTimeTable(){
        $controller = $this->getMockBuilder(Controller::class)
                           ->onlyMethods(['display_User_TimeTable'])
                           ->getMock();

        $expectedResult = [
            [
                'Name' => 'System_Program',
                'day' => 'Monday',
                'start_time' => '08:00:00',
                'end_time' => '10:00:00'
            ],
            [
                'Name' => 'System_Program',
                'day' => 'Wedensday',
                'start_time' => '11:00:00',
                'end_time' => '12:00:00'
            ]
        ];
        $controller->expects($this->once())
                   ->method('display_User_TimeTable')
                   ->willReturn($expectedResult);

        $result = $controller->display_User_TimeTable('test_user');

        $this->assertEquals($expectedResult, $result);
    }
    public function tearDown(): void { }
    public static function tearDownAfterClass(): void{
        self::$controller=null;
    }
}
