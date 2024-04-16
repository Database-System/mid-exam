<?php

namespace Exam\Core;

use DateTime;
use Time;
use Exam\Core\Connect;

class Controller
{
    private $handler = null;
    private $need_tables = [
        "Users" => "CREATE TABLE `Users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(50) NOT NULL,
            `password` varchar(255) NOT NULL,
            `dept` varchar(255) NULL,
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
            `cls_name` VARCHAR(255) NULL,
            `dept` varchar(255) NULL,
            `request` TINYINT(1) NOT NULL DEFAULT 0,
            `Credits` INT(10) UNSIGNED NOT NULL,
            `CurrentPeople` INT(10) UNSIGNED NOT NULL DEFAULT 0,
            `MaxPeople` INT(10) UNSIGNED NOT NULL DEFAULT 0,
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
        if (!isset($this->handler))
            die("Can't get DB handler");
        $this->init_Table();
    }
    private function init_Table(): void
    {
        foreach ($this->need_tables as $table => $val) {
            if (!$this->table_Exists($table)) {
                $this->handler->exec($val);
                if ($table == "TimeSlot") $this->init_TimeSlot();
                if ($table == "CourseTimeSlots") $this->init_Course_data();
            }
        }
    }
    private function init_Course_data(): void
    {
        $jsonFiles = glob('./src/Resource/112/1122*.json');
        foreach ($jsonFiles as $file) {
            $jsondata = file_get_contents($file);
            $records = json_decode($jsondata, true);
            foreach ($records["items"] as $record) {
                $ID_temp = $record["scr_selcode"];
                $ID = (int)$ID_temp;
                if ($this->checkIfIdExists($ID)) {
                    continue;
                }
                $Name = $record['sub_name'];
                $cls_name=$record['cls_name'];
                preg_match_all('/\([一二三四五六日]\)\d{2}(-\d{2})?/u', $record["scr_period"], $matches);
                if (!empty($matches[0])) {
                    $times = $matches[0];
                } else {
                    continue;
                }
                $dept1 = substr($record['cls_id'], 0, 4);
                $dept = $this->search_dept($dept1);
                $request = $record['scj_scr_mso'] == "選修" ? 0 : 1;
                $Credits = $record['scr_credit'];
                $MaxPeople = $record['scr_acptcnt'];
                $this->insert_Course($ID, $Name,$cls_name, $dept, $request, $Credits, $MaxPeople);
            }
        }
    }
    private function search_dept($dept_id): string|NULL
    {

        $dept = [
            "OD00" => "跨領域設計學院(籌備)",
            "CC00" => "創能學院",
            "GE01" => "全校國際生大一不分系學士班",
            "CA00" => "工程與科學學院",
            "CE04" => "機電系",
            "CE05" => "纖維複材系",
            "CE06" => "工工系",
            "CE08" => "化工系",
            "CE13" => "航太系",
            "CE26" => "精密系統設計學位學程",
            "CS01" => "應數系",
            "CS02" => "環科系",
            "CS03" => "材料系",
            "CS06" => "光電系",
            "CB00" => "商學院",
            "CB01" => "會計系",
            "CM01" => "企管系",
            "CB02" => "國際經營與貿易學系",
            "CB05" => "財稅系",
            "CB07" => "統計系",
            "CB08" => "經濟系",
            "CB06" => "合作經濟暨社會事業經營學系",
            "CB26" => "行銷系",
            "CB25" => "國企學士學程英語專班",
            "CS04" => "中文系",
            "CH06" => "外文系",
            "CI00" => "資電學院",
            "CE07" => "資訊系",
            "CE09" => "電子系",
            "CE11" => "電機系",
            "CE12" => "自控系",
            "CI02" => "通訊系",
            "CI05" => "資訊電機學院學士班",
            "CD00" => "建設學院",
            "CE01" => "土木系",
            "CE02" => "水利系",
            "CM02" => "運輸與物流學系",
            "CE10" => "都資系",
            "CM03" => "土管系",
            "CF00" => "金融學院",
            "CB03" => "財金系",
            "CB04" => "風保系",
            "CF03" => "財務工程與精算學程",
            "NM00" => "國際科技與管理學院",
            "NM02" => "澳洲墨爾本皇家理工大學商學",
            "NM03" => "電機資訊雙學士學位學程",
            "NM04" => "商學大數據分析雙學士學位學",
            "NM06" => "美國加州聖荷西州立大學工程",
            "AS00" => "建築專業學院",
            "AS01" => "建築專業學院學士班",
            "CE03" => "建築學士學位學程",
            "AS02" => "創新設計學士學位學程",
            "AS03" => "室內設計學士學位學程",
            "PC04" => "法律經濟學程",
            "PC14" => "不動產管理學程",
            "PC15" => "景觀與遊憩管理學程",
            "PC22" => "社會傳播學程",
            "PC28" => "水土環境經理學程",
            "PC29" => "華語教師學程",
            "PC30" => "計算科學學程",
            "PC38" => "資通安全學程",
            "PC44" => "勞工安全衛生學程",
            "PC46" => "專案管理學程",
            "PC50" => "再生能源與永續社會學程",
            "PC52" => "物聯網學程",
            "PC56" => "皮革科技與管理學程",
            "PC63" => "流體傳動科技學程",
            "PC64" => "成衣菁英學程",
            "PC67" => "鞋類產業人才學程",
            "PC68" => "智慧軌道運輸學程",
            "PC69" => "飛機製造學分學程",
            "PC80" => "跨領域產業學程",
            "PC81" => "設計未來學程",
            "PC84" => "文化創意學分學程",
            "XA01" => "外語文選修",
            "XA02" => "英語選修",
            "XC01" => "通識核心",
            "XD01" => "體育選項課",
            "XE01" => "綜合班",
            "XF01" => "英文綜合班",
            "XF02" => "國文綜合班",
            "XF07" => "核心必修綜合班",
            "XH01" => "軍訓",
            "CE19" => "電聲碩士學位學程",
            "CE24" => "綠能碩士學位學程",
            "CE29" => "產業碩士專班",
            "CE30" => "智能製造與工程管理碩士在職",
            "CB15" => "財法所",
            "CB21" => "科技管理碩士學位學程",
            "CB31" => "商學院商學專業碩士在職專班",
            "CB36" => "商學專業碩士在職學位學程",
            "CH07" => "歷史文物所",
            "CH08" => "公共事務與社會創新研究所",
            "CI06" => "資電碩士在職班",
            "CI10" => "產業研發碩士班",
            "CI13" => "生醫碩士學位學程",
            "CI17" => "光電能源碩士在職專班",
            "CI23" => "資訊電機工程碩士在職學位學",
            "CI24" => "視光科技碩士在職學位學程",
            "CD03" => "景憩碩士學位學程",
            "CD13" => "建設學院專案管理碩士在職專",
            "CD16" => "建設碩士在職學位學程",
            "CD17" => "專案管理碩士在職學位學程",
            "CD18" => "智慧城市國際碩士學位學程",
            "CF02" => "金融碩士在職專班",
            "CF13" => "金融碩士在職學位學程",
            "CB23" => "國際經管碩士學位學程",
            "AS04" => "建築碩士學位學程",
            "AS05" => "建築碩士在職學位學程",
            "CE25" => "創意設計碩士學位學程",
            "PC76" => "智財技轉學程(碩士班)",
            "PC82" => "離岸風電學程(碩士)",
            "PC86" => "大數據分析與實務應用碩士學",
            "CB13" => "經營在職專班",
            "MB03" => "經營管理碩士在職學位學程",
            "CE17" => "機航博士學位學程",
            "CB16" => "商學博士學位學程",
            "CI03" => "電通博士學位學程",
            "CI21" => "智慧聯網產業博士學位學程",
            "CE14" => "土水博士學位學程",
            "CF01" => "金融博士學位學程",
            "CB14" => "商學進修班",
            "CB35" => "商學進修學士學位學程",
            "CD06" => "營建工程與管理進修學士班",
            "CD01" => "室內設計進修學士班"
        ];


        if (array_key_exists($dept_id, $dept)) {
            return $dept[$dept_id];
        } else {
            return NULL;
        }
    }

    private function checkIfIdExists($ID): bool
    {
        $ret = $this->check_Course($ID);

        if (!$ret) {
            return false;
        }
        return true;
    }
    private function init_TimeSlot(): void
    {
        $start = [
            "08:10:00",
            "09:10:00",
            "10:10:00",
            "11:10:00",
            "12:10:00",
            "13:10:00",
            "14:10:00",
            "15:10:00",
            "16:10:00",
            "17:10:00",
            "18:30:00",
            "19:25:00",
            "20:25:00",
            "21:20:00",
        ];
        $end = [
            "09:00:00",
            "10:00:00",
            "11:00:00",
            "12:00:00",
            "13:00:00",
            "14:00:00",
            "15:00:00",
            "16:00:00",
            "17:00:00",
            "18:00:00",
            "19:20:00",
            "20:15:00",
            "21:15:00",
            "22:10:00",
        ];
        $day = ["星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期天"];
        foreach ($day as $d) {
            for ($i = 0; $i < 14; $i++) {
                $this->insert_TimeSlot($d, $start[$i], $end[$i]);
            }
        }
    }
    private function table_Exists(string $table): bool
    {
        $stmt = $this->handler->query("SHOW TABLES LIKE '$table'");
        return !($stmt->rowCount() == 0);
    }
    public function insert_User(string $user, string $password): bool|array
    {
        $sql = "INSERT INTO Users (`username`,`password`) VALUES (?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$user, $password]);
        if (!$ret)
            return false;
        return $stmt->fetch();
    }
    public function check_User(string $user): bool|array
    {
        $sql = "SELECT * from `Users` WHERE `username` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$user]);
        if (!$ret)
            return false;
        return $stmt->fetch();
    }

    public function insert_TimeSlot(string $day, string $start_time, string $end_time)
    {
        $sql = "INSERT INTO TimeSlot (`day`,`start_time`,`end_time`) VALUES (?,?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$day, $start_time, $end_time]);
        if (!$ret) return false;
        return true;
    }

    public function insert_Course(int $ID, string $Name,?string $cls_name,?string $dept, int $request, int $Credits, int $MaxPeople): bool
    {
        $sql = "INSERT INTO Course (`ID`,`Name`,`cls_name`,`dept`,`request`,`Credits`,`MaxPeople`) VALUES (?,?,?,?,?,?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$ID, $Name,$cls_name, $dept, $request, $Credits, $MaxPeople]);
        if (!$ret) return false;
        return true;
    }

    public function insert_CourseTimeSlots(int $Course_ID, int $Time_Slot_ID)
    {
        $sql = "INSERT INTO CourseTimeSlots (`Course_ID`,`Time_Slot_ID`) VALUES (?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$Course_ID, $Time_Slot_ID]);
        if (!$ret) return false;
        return true;
    }

    public function insert_TimeTable(int $course_ID, int $time_slot_id, int $user_id)
    {
        $sql = "INSERT INTO TimeTable (`course_ID`,`time_slot_id`,`user_id`) VALUES (?,?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$course_ID, $time_slot_id, $user_id]);
        if (!$ret) return false;
        return true;
    }

    public function delete_TimeSlot(string $day, string $start_time, string $end_time)
    {
        $sql = "DELETE FROM `TimeSlot` WHERE `day` = ? AND `start_time` = ? AND `end_time` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$day, $start_time, $end_time]);
        if (!$ret) return false;
        return true;
    }

    public function delete_Course(int $ID, string $Name): bool
    {
        $sql = "DELETE FROM `Course` WHERE `ID` = ? AND `Name` = ? ";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$ID, $Name]);
        if (!$ret) return false;
        return true;
    }


    public function delete_TimeTable(int $course_id, int $time_slot_id, int $user_id)
    {
        $sql = "DELETE FROM `TimeTable` WHERE `course_ID` = ? AND `time_slot_id` = ? AND `user_id` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$course_id, $time_slot_id, $user_id]);
        if (!$ret) return false;
        return true;
    }

    public function delete_CourseTimeSlots(int $course_id, int $time_slot_id)
    {
        $sql = "DELETE FROM `CourseTimeSlots` WHERE `course_ID` = ? AND `time_Slot_ID` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$course_id, $time_slot_id]);
        if (!$ret) return false;
        return true;
    }

    //search
    public function search_User_TimeTable(string $username, int $courseID): bool
    {
        $user = $this->check_User($username);

        $course = $this->check_Course($courseID);
        if (!$course) return false;

        $sql = "SELECT * FROM TimeTable WHERE user_id = ? AND course_ID = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$user['id'], $course['ID']]);
        if (!$ret)
            return false;
        return true;
    }
    public function display_User_TimeTable(string $username): bool|array
    {
        $sql = "SELECT Course.Name, TimeSlot.day, TimeSlot.start_time, TimeSlot.end_time
                FROM Users
                INNER JOIN TimeTable ON Users.id = TimeTable.user_id
                INNER JOIN Course ON TimeTable.course_ID = Course.ID
                INNER JOIN TimeSlot ON TimeTable.time_slot_id = TimeSlot.time_slot_id
                WHERE Users.username = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$username]);
        if (!$ret)
            return false;
        return $stmt->fetchAll();
    }

    public function check_Course(int $courseID): bool|array
    {
        $sql = "SELECT * FROM Course WHERE ID = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$courseID]);
        if (!$ret)
            return false;
        return $stmt->fetchall();
    }

    public function updateCourse(int $ID, string $column, $Value): bool
    {
        if (!$this->check_Course($ID)) {
            return false;
        }
        $validColumns = ['Name', 'dept', 'request', 'Credits', 'MaxPeople'];
        if (!in_array($column, $validColumns)) {
            return false;
        }

        $sql = "UPDATE Course SET `$column` = ? WHERE `ID` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$Value, $ID]);
        if (!$ret) return false;
        return true;
    }

    public function Update_User_dept(string $username, string $dept): bool
    {
        $sql = "UPDATE Users SET `dept` = ? WHERE `username` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$dept, $username]);
        if (!$ret) return false;
        return true;
    }

    public function Update_User_TotalCerdits(string $username): bool
    {
        $calcTotalCreditsSql = "SELECT SUM(Course.Credits) as Total_credits
                            FROM Users
                            INNER JOIN TimeTable ON Users.id = TimeTable.user_id
                            INNER JOIN Course ON TimeTable.course_ID = Course.ID
                            WHERE Users.username = ?";
        $calcStmt = $this->handler->prepare($calcTotalCreditsSql);
        $calcStmt->execute([$username]);
        $result = $calcStmt->fetch();

        $sql = "UPDATE Users SET `Total_credits` = ? WHERE `username` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$result['Total_credits'], $username]);
        if (!$ret) return false;
        return true;
    }

    public function updateTimeSlots(int $time_slot_id, string $day, string $start_time, string $end_time): bool
    {
        $start_datetime = DateTime::createFromFormat('H:i:s', $start_time);
        $end_datetime = DateTime::createFromFormat('H:i:s', $end_time);

        if (!$start_datetime || !$end_datetime) return false;

        $start_time_formatted = $start_datetime->format('H:i:s');
        $end_time_formatted = $end_datetime->format('H:i:s');

        $sql = "UPDATE TimeSlot SET `day` = ?,`start_time` = ?,`end_time`=? WHERE `time_slot_id` = ?";
        $stmt = $this->handler->prepare($sql);

        $ret = $stmt->execute([$day, $start_time_formatted, $end_time_formatted, $time_slot_id]);
        if (!$ret) return false;
        return true;
    }
    public function updateCourseTimeSlots(int $Course_ID, int $Time_Slot_ID): bool
    {
        $sql = "UPDATE CourseTimeSlots SET `Time_Slot_id`=? WHERE `Course_ID` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$Course_ID, $Time_Slot_ID]);
        if (!$ret) return false;
        return true;
    }

    public function update_currentpeople(int $people, int $ID):bool
    {
        $sql = "UPDATE Course SET `CurrentPeople` = `CurrentPeople` + ? WHERE `ID` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$people, $ID]);
        if (!$ret) return false;
        return true;
    }

    public function search_Courses_By_TimeSlot(int $class):bool| array
    {   
        $sql = "SELECT * FROM Course WHERE Course.ID IN (SELECT Course_ID FROM CourseTimeSlots WHERE Time_Slot_ID = ? )";
        $stmt = $this->handler->prepare($sql);
        $ret=$stmt->execute([$class]);
        if(!$ret){
            return false;
        }

        $courses = $stmt->fetchAll();
    }

    public function search_Courses_By_Name(string $Name):bool|array
    {
        $sql = "SELECT * FROM Course WHERE Name LIKE  ? ";
        $stmt = $this->handler->prepare($sql);
        $ret=$stmt->execute(['%'.$Name.'%']);
        if(!$ret){
            return false;
        }
        return $stmt->fetchall();
    }
    
    public function search_Courses_By_Dept(?string $dept):bool|array
    {
        $sql = "SELECT * FROM Course WHERE dept LIKE ? ";
        $stmt = $this->handler->prepare($sql);
        $ret=$stmt->execute([$dept]);
        if(!$ret){
            return false;
        }
        return $stmt->fetchAll();
    }

    
}
