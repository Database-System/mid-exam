<?php
require_once './src/Core/Controller.php'; 

class Controller_Test extends PHPUnit\Framework\TestCase
{
    private $controller;

    protected function setUp(): void{
        parent::setUp();
        $this->controller= new \Exam\Core\Controller(); //還是要用include?
        $this->insertTestData();

    }

    private function insertTestData() {
        $this->controller->insert_Courses();
        $this->controller->insert_timeslot();
        $this->controller->insert_coursetimeslots();
        $this->controller->insert_timetable();        
    }

    private function insert_Courses(){
        $sql = "INSERT INTO Course (ID, Name, Credits) VALUES 
            (1312, '系統程式', 3),
            (1314, '機率與統計', 3),
            (1313, '資料庫系統', 3),
            (2864, '日文(一)', 2),
            (1311, '班級活動', 0),
            (1324, 'Web程式設計', 3),
            (2990, '漢字之美', 2),
            (1365, '程式設計與問題解決', 2),
            (2809, '華語教材教法', 2),
            (3320, '大學精進英文(二)中級', 2)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute();
        if (!$ret) {
            $errorInfo = $stmt->errorInfo();
            die("SQL 錯誤：" . $errorInfo[2]);
        }
    }

    private function insert_timeslot(){
        $sql = "INSERT INTO TimeSlot (time_slot_id, day, start_time,end_time) VALUES 
            (1, '星期一', '10:10:00', '12:00:00'),
            (2, '星期三', '11:10:00', '12:00:00'),
            (3, '星期一', '13:10:00', '15:00:00'),
            (4, '星期二', '11:10:00', '12:00:00'),
            (5, '星期一', '15:10:00', '17:00:00'),
            (6, '星期二', '10:10:00', '11:00:00'),
            (7, '星期二', '13:10:00', '15:00:00'),
            (8, '星期二', '16:10:00', '17:00:00'),
            (9, '星期三', '08:10:00', '11:00:00'),
            (10, '星期四', '10:10:00', '12:00:00'),
            (11, '星期四', '13:10:00', '15:00:00'),
            (12, '星期四', '18:30:00', '20:15:00'),
            (13, '星期五', '08:10:00', '10:00:00')";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute();
        if (!$ret) {
            $errorInfo = $stmt->errorInfo();
            die("SQL 錯誤：" . $errorInfo[2]);
        }
    }

    private function insert_coursetimeslots(){
        $sql = "INSERT INTO CourseTimeSlots (Course_ID, Time_Slot_ID) VALUES 
            (1312,1),(1312,2),
            (1314,3),(1314,4),
            (1313,5),(1313,6),
            (2864,7),
            (1311,8),
            (1324,9),
            (2990,10),
            (1365,11),
            (2809,12),
            (3320,13)";
    }

    private function insert_timetable(){
        $sql = "INSERT INTO TimeTable (course_ID, time_slot_id, user_id) VALUES 
            (1312,1,1),(1312,2,1),
            (2864,7,1),
            (1311,8,1),
            (2809,12,1)";
    }
    public function testDisplayUserTimeTable()
    { 
        include './src/Core/Controller.php';
        ob_start();
        $this->controller->display_User_TimeTable('test_user');
        $output = ob_get_clean();
        //$this->assertContains('課程名稱', $output); 
    }
}
