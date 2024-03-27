<?php

namespace Exam\Core;

use Exam\Core\Connect;

class Controller
{
    private $handler = null;
    private $need_tables = [
        "Users" => "CREATE TABLE `Users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(50) NOT NULL,
            `password` varchar(255) NOT NULL,
            `Total_credits` int(10) UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `username_UNIQUE` (`username`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    
        "TimeSlot" => "CREATE TABLE `TimeSlot` (
            `time_slot_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `day` VARCHAR(255) NOT NULL,
            `start_time` TIME NOT NULL,
            `end_time` TIME NOT NULL,
            PRIMARY KEY (`time_slot_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    
        "Course" => "CREATE TABLE `Course` (
            `ID` INT(10) UNSIGNED NOT NULL,
            `Name` VARCHAR(255) NOT NULL,
            `Credits` INT(10) UNSIGNED NOT NULL,
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    
        "CourseTimeSlots" => "CREATE TABLE `CourseTimeSlots` (
            `Course_ID` INT(10) UNSIGNED NOT NULL,
            `Time_Slot_ID` INT(10) UNSIGNED NOT NULL,
            PRIMARY KEY (`Course_ID`, `Time_Slot_ID`),
            FOREIGN KEY (`Course_ID`) REFERENCES `Course`(`ID`),
            FOREIGN KEY (`Time_Slot_ID`) REFERENCES `TimeSlot`(`time_slot_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    
        "TimeTable" => "CREATE TABLE `TimeTable` (
            `course_ID` INT(10) UNSIGNED NOT NULL,
            `time_slot_id` INT(10) UNSIGNED NOT NULL,
            `user_id` INT(11) NOT NULL,
            FOREIGN KEY (`course_ID`) REFERENCES `Course`(`ID`),
            FOREIGN KEY (`time_slot_id`) REFERENCES `TimeSlot`(`time_slot_id`),
            FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
    ];
    
    public function __construct()
    {
        $connect = new Connect();
        $this->handler = $connect->getHandler();
        if (!isset($this->handler)) die("Can't get DB handler");
        $this->init_Table();
    }
    private function init_Table()
    {
        foreach ($this->need_tables as $table => $val) {
            if (!$this->table_Exists($table)) {
                $this->handler->exec($val);
            }
        }
    }
    private function table_Exists($table)
    {
        $stmt = $this->handler->query("SHOW TABLES LIKE '$table'");
        return !($stmt->rowCount() == 0);
    }
    public function insert_User(string $user, string $password)
    {
        $sql = "INSERT INTO Users (`username`,`password`) VALUES (?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$user, $password]);
        if (!$ret) {
            $errorInfo = $stmt->errorInfo();
            die("SQL 錯誤：" . $errorInfo[2]);
        }
    }
    public function check_User(string $user)
    {
        $sql = "SELECT * from `Users` WHERE `username` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$user]);
        if (!$ret) return false;
        return $stmt->fetch();
    }
    public function insert_TimeSlot(string $day,string $start_time,string $end_time)
    {
        $sql = "INSERT INTO TimeSlot (`day`,`start_time`,`end_time`) VALUES (?,?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$day,$start_time,$end_time]);
        if (!$ret) {
            $errorInfo = $stmt->errorInfo();
            die("SQL 錯誤：" . $errorInfo[2]);
        }
    }
    public function insert_Course(int $ID,string $Name,int $Credits)
    {
        $sql = "INSERT INTO Course (`ID`,`Name`,`Credits`) VALUES (?,?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$ID,$Name,$Credits]);
        if (!$ret) {
            $errorInfo = $stmt->errorInfo();
            die("SQL 錯誤：" . $errorInfo[2]);
        }
    }
    public function insert_CourseTimeSlots(int $Course_ID,int $Time_Slot_ID)
    {
        $sql = "INSERT INTO CourseTimeSlots (`Course_ID`,`Time_Slot_ID`) VALUES (?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$Course_ID,$Time_Slot_ID]);
        if (!$ret) {
            $errorInfo = $stmt->errorInfo();
            die("SQL 錯誤：" . $errorInfo[2]);
        }
    }
    public function insert_TimeTable(int $course_ID,int $time_slot_id,int $user_id)
    {
        $sql = "INSERT INTO TimeTable (`course_ID`,`time_slot_id`,`user_id`) VALUES (?,?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$course_ID,$time_slot_id,$user_id]);
        if (!$ret) {
            $errorInfo = $stmt->errorInfo();
            die("SQL 錯誤：" . $errorInfo[2]);
        }
    }
    public function delete_TimeSlot(int $time_slot_id)
    {
        $sql = "DELETE from `TimeSlot` WHERE `time_slot_id` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$time_slot_id]);
        if (!$ret) {
            $errorInfo = $stmt->errorInfo();
            die("SQL 錯誤：" . $errorInfo[2]);
        }
    }
    public function delete_Course(int $course_id)
    {
        $sql = "DELETE from `Course` WHERE `ID` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$course_id]);
        if (!$ret) {
            $errorInfo = $stmt->errorInfo();
            die("SQL 錯誤：" . $errorInfo[2]);
        }
    }
    public function delete_TimeTable(int $course_id, int $time_slot_id, int $user_id)
    {
        $sql = "DELETE FROM TimeTable WHERE course_ID = ? AND time_slot_id = ? AND user_id = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$course_id, $time_slot_id, $user_id]);
        if (!$ret) {
            $errorInfo = $stmt->errorInfo();
            die("SQL 錯誤：" . $errorInfo[2]);
        }
    }
    public function insertdata(){
        $this->insert_TimeSlot("Monday","08:00:00","10:00:00");
        $this->insert_Course(1,"Math",3);
        $this->insert_CourseTimeSlots(1,1);
        $this->insert_TimeTable(1,1,1);
        
    }
    
}
