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

    //search
    public function display_User_TimeTable($username){
        $sql = "SELECT Course.Name, TimeSlot.day, TimeSlot.start_time, TimeSlot.end_time
                FROM Users
                INNER JOIN TimeTable ON Users.id = TimeTable.user_id
                INNER JOIN Course ON TimeTable.course_ID = Course.ID
                INNER JOIN TimeSlot ON TimeTable.time_slot_id = TimeSlot.time_slot_id
                WHERE Users.username = ?";
        $stmt = $this->handler->prepare($sql);
        $stmt->execute([$username]);
        $result = $stmt->fetchAll();

        if ($result) {
            echo "功課表";
            echo "<table border='1'>";
            echo "<tr><th>课程名稱</th><th>星期</th><th>開始時間</th><th>結束時間</th></tr>";
            foreach ($result as $row) {
                echo "<tr>";
                echo "<td>" . $row['Name'] . "</td>";
                echo "<td>" . $row['day'] . "</td>";
                echo "<td>" . $row['start_time'] . "</td>";
                echo "<td>" . $row['end_time'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "沒課";
        }
    }
}
