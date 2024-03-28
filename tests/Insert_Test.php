<?php
require_once './src/Core/Controller.php';

class Insert_Test extends PHPUnit\Framework\TestCase
{
   
    public function testInsertData()
    {
      $controller = new Controller();
        $controller->insert_TimeSlot("Monday", "08:00:00", "10:00:00");
        $controller->insert_Course(1, "Math", 3);
        $controller->insert_CourseTimeSlots(1, 1);
        $controller->insert_TimeTable(1, 1, 1);
        
        // Assertions to verify the expected behavior
        $stmt = $controller->handler->prepare("SELECT * FROM TimeSlot WHERE day = 'Monday' AND start_time = '08:00:00' AND end_time = '10:00:00'");
        $stmt->execute();
        $timeSlot = $stmt->fetch();
        $this->assertNotNull($timeSlot);
    
        $stmt = $controller->handler->prepare("SELECT * FROM Course WHERE ID = 1 AND Name = 'Math' AND Credits = 3");
        $stmt->execute();
        $course = $stmt->fetch();
        $this->assertNotNull($course);
    
        $stmt = $controller->handler->prepare("SELECT * FROM CourseTimeSlots WHERE Course_ID = 1 AND Time_Slot_ID = 1");
        $stmt->execute();
        $courseTimeSlots = $stmt->fetch();
        $this->assertNotNull($courseTimeSlots);
    
        $stmt = $controller->handler->prepare("SELECT * FROM TimeTable WHERE course_ID = 1 AND time_slot_id = 1 AND user_id = 1");
        $stmt->execute();
        $timeTable = $stmt->fetch();
        $this->assertNotNull($timeTable);
    }
}  
?>
