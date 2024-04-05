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
}
