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
            PRIMARY KEY (`ID`),
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

        "CourseTimeSlots" => "CREATE TABLE CourseTimeSlots (
            Course_ID INT(10) UNSIGNED NOT NULL,
            Time_Slot_ID INT(10) UNSIGNED NOT NULL,
            PRIMARY KEY (Course_ID, Time_Slot_ID),
            FOREIGN KEY (Course_ID) REFERENCES Course(ID),
            FOREIGN KEY (Time_Slot_ID) REFERENCES TimeSlot(time_slot_id)
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

    private function users(){
        $sql = "INSERT INTO Users (id,username) VALUES 
            (0001, 'test_user')";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute();
        if (!$ret) {
            $errorInfo = $stmt->errorInfo();
            die("SQL 錯誤：" . $errorInfo[2]);
        }
    }

    private function insert_timeslot(){
        $sql = "INSERT INTO TimeSlot (time_slot_id, day, start_time,end_time) VALUES 
            (1312, '星期一', '10:10:00', '12:00:00'),
            (1314, '星期一', '13:10:00', '15:00:00'),
            (1313, '星期一', '15:10:00', '17:00:00'),
            (2864, '星期二', '13:10:00', '15:00:00'),
            (1311, '星期二', '16:10:00', '17:00:00'),
            (1324, '星期三', '08:10:00', '11:00:00'),
            (2990, '星期四', '10:10:00', '12:00:00'),
            (1365, '星期四', '13:10:00', '15:00:00'),
            (2809, '星期四', '18:30:00', '20:15:00'),
            (3320, '星期五', '08:10:00', '10:00:00')";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute();
        if (!$ret) {
            $errorInfo = $stmt->errorInfo();
            die("SQL 錯誤：" . $errorInfo[2]);
        }
    }

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
            echo "<h2>课程表</h2>";
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