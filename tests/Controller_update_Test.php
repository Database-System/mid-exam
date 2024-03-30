<?php
use PHPUnit\Framework\TestCase;
use Exam\Core\Controller;

class Controller_update_Test extends TestCase{
    protected static $controller;
    public $handler = null;

    public static function setUpBeforeClass():void{
        self::$controller =new Controller();
    }
    protected function setUp(): void {
        self::$controller->insert_User('test_user', 'test_password',1,0);
    }
    public function testInserTimeTable(){
        $sql1 = "INSERT INTO TimeTable (course_ID, time_slot_id, user_id) VALUES 
            (1312,1,1),(1312,2,1),
            (2809,12,1)";
        $stmt = $this->handler->prepare($sql1);
        $stmt->execute();

        $sql2 = "INSERT INTO Course (ID, Name, Credits) VALUES 
            (1312, 'system program', 3),
            (1314, '機率與統計', 3),
            (1313, '資料庫系統', 3),
            (2864, '日文(一)', 2),
            (1311, '班級活動', 0),
            (1324, 'Web程式設計', 3),
            (2990, '漢字之美', 2),
            (1365, '程式設計與問題解決', 2),
            (2809, 'teaching mothod', 2),
            (3320, '大學精進英文(二)中級', 2)";
        $stmt = $this->handler->prepare($sql2);
        $stmt->execute();

        $sql3 = "INSERT INTO TimeSlot (time_slot_id, day, start_time,end_time) VALUES 
            (1, 'Monday', '10:10:00', '12:00:00'),
            (2, 'Wednesday', '11:10:00', '12:00:00'),
            (3, 'Monday', '13:10:00', '15:00:00'),
            (4, 'Tuesday', '11:10:00', '12:00:00'),
            (5, 'Monday', '15:10:00', '17:00:00'),
            (6, 'Tuesday', '10:10:00', '11:00:00'),
            (7, 'Tuesday', '13:10:00', '15:00:00'),
            (8, 'Tuesday', '16:10:00', '17:00:00'),
            (9, 'Wednesday', '08:10:00', '11:00:00'),
            (10, 'Thursday', '10:10:00', '12:00:00'),
            (11, 'Thursday', '13:10:00', '15:00:00'),
            (12, 'Thursday', '18:30:00', '20:15:00'),
            (13, 'Friday', '08:10:00', '10:00:00')";
        $stmt = $this->handler->prepare($sql3);
        $stmt->execute();
    }
    public function testSearch(){
        $result1= self::$controller->search_User_TimeTable('test_user',1312);
        $this->assertTrue($result1);

        $result2= self::$controller->search_User_TimeTable('test_user',1313);
        $this->assertFalse($result2);
    }
    public function testDisplayUserTimeTable(){
        $controller = $this->getMockBuilder(Controller::class)
                           ->onlyMethods(['display_User_TimeTable'])
                           ->getMock();

        $expectedResult = [
            [
                'Name' => 'system program',
                'day' => 'Monday',
                'start_time' => '08:10:00',
                'end_time' => '10:00:00'
            ],
            [
                'Name' => 'system program',
                'day' => 'Wedensday',
                'start_time' => '11:10:00',
                'end_time' => '12:00:00'
            ],
            [
                'Name' => 'teaching mothod',
                'day' => 'Thursday',
                'start_time' => '18:30:00',
                'end_time' => '20:15:00'
            ]
        ];
        $controller->expects($this->once())
                   ->method('display_User_TimeTable')
                   ->willReturn($expectedResult);

        $result = $controller->display_User_TimeTable('test_user');

        $this->assertEquals($expectedResult, $result);
    }
    protected function tearDown(): void {
        self::$controller->delete_User('test_user');
    }
    public static function tearDownAfterClass(): void{
        self::$controller=null;
    }
}
