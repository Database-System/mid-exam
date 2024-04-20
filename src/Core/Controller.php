<?php

namespace Exam\Core;

use DateTime;
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
            `cls_name` varchar(255) NULL,
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
            `user_id` INT(11) NOT NULL,
            `check` INT(1) NOT NULL DEFAULT 0,
            FOREIGN KEY (`course_ID`) REFERENCES `CourseTimeSlots`(`Course_ID`),
            FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
    ];

    /**
     * __construct
     *
     * 建立資料庫連線
     * 
     * @return void
     */
    public function __construct()
    {
        $connect = new Connect();
        $this->handler = $connect->getHandler();
        if (!isset($this->handler))
            die("Can't get DB handler");
        $this->init_Table();
    }    
    
    /**
     * init_Table
     *
     * 初始化資料表
     * 
     * @return void
     */
    private function init_Table(): void
    {
        foreach ($this->need_tables as $table => $val) {
            if (!$this->table_Exists($table)) {
                $this->handler->exec($val);
                if ($table == "TimeSlot") $this->init_TimeSlot();
                if ($table == "CourseTimeSlots") $this->init_Course_data();
            }
        }
        $this->update_currentpeople(69,1350);
        $this->update_currentpeople(64,1334);
        $this->update_currentpeople(74,1324);
    }    

    /**
     * init_Course_data
     *
     * 添加課程資料利用json檔案
     * 
     * @return void
     */
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
                $cls_name = $record['cls_name'];
                $dept1 = substr($record['cls_id'], 0, 4);
                $dept = $this->search_dept($dept1);
                $request = $record['scj_scr_mso'] == "選修" ? 0 : 1;
                $Credits = $record['scr_credit'];
                $MaxPeople = $record['scr_acptcnt'];
                $this->insert_Course($ID, $Name, $cls_name, $dept, $request, $Credits, $MaxPeople);
                preg_match_all('/\([一二三四五六日]\)\d{2}(-\d{2})?/u', $record['scr_period'], $matches);
                if (!empty($matches[0])) {
                    $results = [];
                    foreach ($matches[0] as $match) {
                        $clean = str_replace(['(', ')'], '', $match);
                        if (strpos($clean, '-') !== false) {
                            list($firstPart, $secondPart) = explode('-', $clean);
                            $weekDay = mb_substr($firstPart, 0, 1, "UTF-8");
                            $firstNumber = mb_substr($firstPart, 1);
                            $secondNumber = mb_substr($secondPart, 0);
                            $weekDayNumber = $this->chineseToNumber($weekDay);
                            for ($i = intval($firstNumber); $i <= intval($secondNumber); $i++) {
                                $results[] = $weekDayNumber . str_pad($i, 2, "0", STR_PAD_LEFT);
                            }
                        } else {
                            $weekDay = mb_substr($clean, 0, 1, "UTF-8");
                            $number = mb_substr($clean, 1);
                            $weekDayNumber = $this->chineseToNumber($weekDay);
                            $results[] = $weekDayNumber . str_pad($number, 2, "0", STR_PAD_LEFT);
                        }
                    }
                    $times = array_unique($results);
                } else {
                    continue;
                }
                foreach ($times as $time) {
                    if ($time % 100 == 0) {
                        continue;
                    }
                    $time = (intdiv($time, 100) - 1) * 14 + $time % 100;
                    $this->insert_CourseTimeSlots($ID, $time);
                }
            }
        }
    }
    
    /**
     * chineseToNumber
     *
     * 轉換星期幾的中文到數字
     * 
     * @param  string $chinese 星期幾
     * @return int|string
     */
    private function chineseToNumber(string $chinese): int|string
    {
        $numbers = [
            '一' => 1, '二' => 2, '三' => 3, '四' => 4,
            '五' => 5, '六' => 6, '日' => 7
        ];

        return isset($numbers[$chinese]) ? $numbers[$chinese] : 'Unknown';
    }
    
    /**
     * search_dept
     * 
     * 利用課程系所代碼
     * 找尋該課程系所
     * 
     * @param  string $dept_id 課程系所代碼
     * @return string|NULL
     */
    private function search_dept(string $dept_id): string|NULL
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
    
    /**
     * checkIfIdExists
     *
     * 利用課程ID
     * 檢查該課程是否存在
     * 
     * @param  int $ID
     * @return bool
     */
    private function checkIfIdExists(int $ID): bool
    {
        $ret = $this->check_Course($ID);

        if (!$ret) {
            return false;
        }
        return true;
    }

    /**
     * init_TimeSlot
     * 
     * 初始化時間段
     * 
     * @return void
     */
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
        
    /**
     * table_Exists
     *
     * 利用表名稱
     * 檢查該表是否存在
     * 
     * @param  string $table
     * @return bool
     */
    private function table_Exists(string $table): bool
    {
        $stmt = $this->handler->query("SHOW TABLES LIKE '$table'");
        return !($stmt->rowCount() == 0);
    }
    
    /**
     * insert_User
     *
     * 利用使用者名稱與密碼
     * 插入使用者
     * 
     * @param  string $user 使用者名稱
     * @param  string $password 密碼
     * @return bool|array
     */
    public function insert_User(string $user, string $password): bool|array
    {
        $sql = "INSERT INTO Users (`username`,`password`) VALUES (?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$user, $password]);
        if (!$ret)
            return false;
        return $stmt->fetch();
    }
        
    /**
     * check_User
     *
     * 利用使用者名稱
     * 檢查該使用者是否存在
     * 
     * @param  string $user 使用者名稱
     * @return bool|array
     */
    public function check_User(string $user): bool|array
    {
        $sql = "SELECT * from `Users` WHERE `username` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$user]);
        if (!$ret)
            return false;
        return $stmt->fetch();
    }
    
    /**
     * insert_TimeSlot
     *
     * 利用星期、開始時間、結束時間
     * 插入時間段
     * 
     * @param  string $day
     * @param  string $start_time
     * @param  string $end_time
     * @return bool
     */
    public function insert_TimeSlot(string $day, string $start_time, string $end_time): bool
    {
        $sql = "INSERT INTO TimeSlot (`day`,`start_time`,`end_time`) VALUES (?,?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$day, $start_time, $end_time]);
        if (!$ret) return false;
        return true;
    }
    
    /**
     * insert_Course
     *
     * 利用課程ID、課程名稱、課程班級名稱、課程系所、課程類型、學分、最大人數
     * 插入課程
     * 
     * @param  int $ID
     * @param  string $Name
     * @param  mixed $cls_name
     * @param  mixed $dept
     * @param  int $request
     * @param  int $Credits
     * @param  int $MaxPeople
     * @return bool
     */
    public function insert_Course(int $ID, string $Name, ?string $cls_name, ?string $dept, int $request, int $Credits, int $MaxPeople): bool
    {
        $sql = "INSERT INTO Course (`ID`,`Name`,`cls_name`,`dept`,`request`,`Credits`,`MaxPeople`) VALUES (?,?,?,?,?,?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$ID, $Name, $cls_name, $dept, $request, $Credits, $MaxPeople]);
        if (!$ret) return false;
        return true;
    }
    
    /**
     * insert_CourseTimeSlots
     *
     * @param  mixed $Course_ID
     * @param  mixed $Time_Slot_ID
     * @return bool
     */
    public function insert_CourseTimeSlots(int $Course_ID, int $Time_Slot_ID): bool
    {
        $sql = "INSERT INTO CourseTimeSlots (`Course_ID`,`Time_Slot_ID`) VALUES (?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$Course_ID, $Time_Slot_ID]);
        if (!$ret) return false;
        return true;
    }

    //加選條件
    // public function add_course(int $course_ID, int $user_id, string $username,int $check): bool
    // {
    //     /*
    //     加選：
    //          同學只能加選本系的課程
    //          人數已滿的課程不可加選 check_people_number
    //          不可加選與已選課程同名的課程
    //          加選後學分不可超過最高學分限制 (30 學分)  insert_check_Credits
    //          不可加選衝堂的課程
    //     */
    //     if (!$this->insert_check_Credits($course_ID, $username)) {
    //         return false;
    //     }

    //     if (!$this->check_people_number($course_ID)) {
    //         return false;
    //     }

    //     return $this->insert_TimeTable($course_ID, $user_id,$check);
    // }    
    
    /**
     * check_request
     *
     * 利用課程ID
     * 檢查該課程是否為選修或必修
     * 
     * @param  int $course_ID 課程ID
     * @return bool
     */
    public function check_request(int $course_ID) : bool
    {
        $sql = "SELECT request From Course WHERE ID = ?";
        $stmt = $this->handler->prepare($sql);
        $stmt->execute([$course_ID]);

        $row = $stmt->fetch();
        $course_request = $row['request'];

        if ($course_request == 1) {
            return false; //是必修
        }
        return true;
    }
    
    /**
     * insert_check_Credits
     *
     * 利用課程ID與使用者名稱
     * 檢查該使用者加選該課程後檢查最高學分限制
     * 
     * @param  int $course_ID
     * @param  string $username
     * @return bool
     */
    public function insert_check_Credits(int $course_ID, string $username): bool
    {
        $totalCreditsAfterAdd = $this->get_total_credits($username) + $this->Course_credits($course_ID);
        if ($totalCreditsAfterAdd > 30) {
            return false;
        }

        return true;
    }
    
    /**
     * remove_check_Credits
     *
     * 利用課程ID與使用者名稱
     * 檢查該使用者移除該課程後檢查最低學分限制
     * 
     * @param  int $course_ID
     * @param  string $username
     * @return bool
     */
    public function remove_check_Credits(int $course_ID, string $username): bool
    {
        $totalCreditsAfterRemove =  $this->get_total_credits($username) - $this->Course_credits($course_ID);

        if ($totalCreditsAfterRemove < 9) {
            return false;
        }
        return true;
    }
    
    /**
     * Course_credits
     *
     * 利用課程ID
     * 找尋該課程的學分
     * 
     * @param  int $course_ID 課程ID
     * @return int
     */
    public function Course_credits(int $course_ID): int
    {
        $sql = "SELECT Credits FROM Course WHERE ID = ?";
        $stmt = $this->handler->prepare($sql);
        $stmt->execute([$course_ID]);
        $row = $stmt->fetch();
        return $row['Credits'];
    }

    /**
     * updateTotalCredits
     *
     * 利用使用者名稱與總學分
     * 更新該使用者的總學分
     * 
     * @param  string $username
     * @param  int $totalCredits
     * @return bool
     */
    public function updateTotalCredits(string $username, int $totalCredits) : bool
    { //直接插入學分
        $sql = "UPDATE Users SET Total_credits = ? WHERE username = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$totalCredits, $username]);
        if (!$ret) return false;
        return true;
    }

    /**
     * check_people_number
     *  
     * 利用課程ID
     * 檢查該課程是否人數已滿
     * 
     * @param  int $course_ID 課程ID
     * @return bool
     */
    public function check_people_number(int $course_ID) : bool
    {
        $sql = "SELECT CurrentPeople,MaxPeople From Course WHERE ID = ?";
        $stmt = $this->handler->prepare($sql);
        $stmt->execute([$course_ID]);

        $row = $stmt->fetch();
        $CurrentPeople = $row['CurrentPeople'];
        $MaxPeople = $row['MaxPeople'];

        if ($CurrentPeople >= $MaxPeople) {
            return false;
        }
        return true;
    }
    
    /**
     * insert_TimeTable
     *
     * 利用課程ID、使用者ID、選課狀態
     * 插入該使用者的時間表
     * 
     * @param  int $course_ID 課程ID
     * @param  int $uid 使用者ID
     * @param  int $check 0:未選課 1:關注課程 2:已選課
     * @return bool
     */
    public function insert_TimeTable(int $course_ID, int $uid, int $check) : bool
    {
        $sql = "INSERT INTO TimeTable (`course_ID`,`user_id`,`check`) VALUES (?,?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$course_ID, $uid, $check]);
        if (!$ret) return false;
        return true;
    }
    
    /**
     * delete_TimeSlot
     *
     * 利用星期、開始時間、結束時間
     * 刪除該時間段
     * 
     * @param  string $day 星期
     * @param  string $start_time 開始時間
     * @param  string $end_time 結束時間
     * @return bool
     */
    public function delete_TimeSlot(string $day, string $start_time, string $end_time): bool
    {
        $sql = "DELETE FROM `TimeSlot` WHERE `day` = ? AND `start_time` = ? AND `end_time` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$day, $start_time, $end_time]);
        if (!$ret) return false;
        return true;
    }
    
    /**
     * delete_Course
     *
     * 利用課程ID與課程名稱
     * 刪除該課程
     * 
     * @param  int $ID
     * @param  string $Name
     * @return bool
     */
    public function delete_Course(int $ID, string $Name): bool
    {
        $sql = "DELETE FROM `Course` WHERE `ID` = ? AND `Name` = ? ";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$ID, $Name]);
        if (!$ret) return false;
        return true;
    }
    
    /**
     * delete_TimeTable
     *
     * 利用課程ID與使用者ID
     * 刪除該使用者的時間表
     * 
     * @param  int $course_id 課程ID
     * @param  int $user_id 使用者ID
     * @return bool
     */
    public function delete_TimeTable(int $course_id, int $user_id): bool
    {
        $sql = "DELETE FROM `TimeTable` WHERE `course_ID` = ?  AND `user_id` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$course_id, $user_id]);
        if (!$ret) return false;
        return true;
    }
    
    /**
     * delete_CourseTimeSlots
     *
     * 利用課程ID與課程時間ID
     * 刪除該課程的時間表
     * 
     * @param  int $course_id 課程ID
     * @param  int $time_slot_id 課程時間ID
     * @return bool
     * 
     */
    public function delete_CourseTimeSlots(int $course_id, int $time_slot_id): bool
    {
        $sql = "DELETE FROM `CourseTimeSlots` WHERE `course_ID` = ? AND `time_Slot_ID` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$course_id, $time_slot_id]);
        if (!$ret) return false;
        return true;
    }

    /**
     * search_User_TimeTable
     *
     * 利用使用者名稱與課程ID
     * 找尋該使用者是否有該課程的時間表
     * 
     * @param  string $username 使用者名稱
     * @param  int $courseID 課程ID
     * @return bool
     */
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

    /**
     * display_User_TimeTable
     *
     * 利用使用者名稱
     * 找尋該使用者的所有課程時間表
     * 
     * @param  string $username 使用者名稱
     * @return bool|array
     */
    public function display_User_TimeTable(string $username): bool|array
    {
        $sql = "SELECT Course.Name, TimeSlot.day, TimeSlot.start_time, TimeSlot.end_time
                FROM Users
                INNER JOIN TimeTable ON Users.id = TimeTable.user_id
                INNER JOIN Course ON TimeTable.course_ID = Course.ID
                WHERE Users.username = ?";
        $stmt = $this->handler->prepare($sql);
        // INNER JOIN TimeSlot ON TimeTable.time_slot_id = TimeSlot.time_slot_id
        $ret = $stmt->execute([$username]);
        if (!$ret)
            return false;
        return $stmt->fetchAll();
    }

    /**
     * check_Course
     *
     * 利用課程ID
     * 找尋所有該課程所有資料
     * 
     * @param  int    $courseID 課程ID
     * @return bool|array
     */
    public function check_Course(int $courseID): bool|array
    {
        $sql = "SELECT * FROM Course WHERE ID = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$courseID]);
        if (!$ret)
            return false;
        return $stmt->fetchall();
    }
    
    /**
     * get_course_info
     *  獲取單獨課程資訊
     * @param  int $courseID
     * @return bool|array
     */
    public function get_course_info(int $courseID): bool|array
    {
        $sql = "SELECT * FROM Course WHERE ID = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$courseID]);
        if (!$ret)
            return false;
        return $stmt->fetch();
    }

    /**
     * updateCourse
     *
     * 利用課程ID與欄位名稱
     * 更新該課程的欄位值
     * 
     * @param  int      $ID     課程ID
     * @param  string   $column 欄位名稱
     * @param  string   $Value  欄位值
     * @return bool
     */
    public function updateCourse(int $ID, string $column, string $Value): bool
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

    /**
     * Update_User_dept
     * 
     * 利用使用者名稱與系所
     * 搜尋該使用者名稱
     *
     * @param  string $username 使用者名稱
     * @param  string $dept    系所
     * @return bool
     */
    public function Update_User_dept(string $username, string $dept): bool
    {
        $sql = "UPDATE Users SET `dept` = ? WHERE `username` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$dept, $username]);
        if (!$ret) return false;
        return true;
    }

    /**
     * Update_User_clsname
     *
     * 利用使用者名稱與系級
     * 搜尋該使用者名稱
     * 更新該使用者的系級
     * 
     * @param  string $username 使用者名稱
     * @param  string $cls_name 系級
     * @return bool
     */
    public function Update_User_clsname(string $username, string $cls_name): bool
    {
        $sql = "UPDATE Users SET `cls_name` = ? WHERE `username` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$cls_name, $username]);
        if (!$ret) return false;
        return true;
    }

    /**
     * Update_User_TotalCerdits
     *
     * 利用使用者名稱和已選課程
     * 先去找尋該使用者的所有課程ID
     * 再去找尋該課程ID的學分
     * 總學分 = 所有課程的學分總和
     * 最後更新該使用者的總學分
     * 
     * @param  string $username 使用者名稱
     * @return bool
     */
    public function Update_User_TotalCerdits(string $username): bool
    {
        $calcTotalCreditsSql = "SELECT course_ID 
                                FROM TimeTable
                                WHERE user_id in (SELECT id FROM Users WHERE username = ?)
                                AND `check` = 2";
        $calcStmt = $this->handler->prepare($calcTotalCreditsSql);
        $calcStmt->execute([$username]);
        $result = $calcStmt->fetchAll();

        $temp = [];
        foreach ($result as $row) {
            $sql = "SELECT Credits FROM Course WHERE ID = ?";
            $stmt = $this->handler->prepare($sql);
            $stmt->execute([$row['course_ID']]);
            $temp[] = $stmt->fetchall();
        }

        $totalCredits = 0;
        foreach ($temp as $row) {
            $totalCredits += $row[0]['Credits'];
        }

        $sql = "UPDATE Users SET `Total_credits` = ? WHERE `username` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$totalCredits, $username]);
        if (!$ret) return false;
        return true;
    }

    /**
     * updateTimeSlots
     *
     * 搜尋時間ID
     * 新增該時間的星期、開始時間、結束時間
     * 更新該時間的資料
     * 
     * @param  int $time_slot_id  課程時間ID
     * @param  string $day        星期
     * @param  string $start_time 開始時間
     * @param  string $end_time   結束時間
     * @return bool
     */
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

    /**
     * updateCourseTimeSlots
     *
     * 利用課程ID與課程時間ID
     * 更新該課程的時間
     * 
     * @param  int $Course_ID    課程ID
     * @param  int $Time_Slot_ID 課程時間ID
     * @return bool
     */
    public function updateCourseTimeSlots(int $Course_ID, int $Time_Slot_ID): bool
    {
        $sql = "UPDATE CourseTimeSlots SET `Time_Slot_id`=? WHERE `Course_ID` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$Course_ID, $Time_Slot_ID]);
        if (!$ret) return false;
        return true;
    }

    /**
     * update_currentpeople
     *
     * 利用課程ID與課程中的人數
     * 更新該課程的人數
     * 
     * @param  int $people 課程中的人數
     * @param  int $ID     課程ID
     * @return bool
     */
    public function update_currentpeople(int $people, int $ID): bool
    {
        $sql = "UPDATE Course SET `CurrentPeople` = ? WHERE `ID` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$people, $ID]);
        if (!$ret) return false;
        return true;
    }

    /**
     * search_Courses_By_TimeSlot
     *
     * 利用課程時間
     * 來去搜索資料裡有包含該課程時間的課程
     * 
     * @param  int $class 課程時間
     * @return bool|array
     */
    public function search_Courses_By_TimeSlot(int $class): bool| array
    {
        $sql = "SELECT * FROM Course WHERE Course.ID IN (SELECT Course_ID FROM CourseTimeSlots WHERE Time_Slot_ID = ? )";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$class]);
        if (!$ret) {
            return false;
        }

        return $stmt->fetchAll();
    }

    /**
     * search_Courses_By_Name
     *
     * 利用課程名稱
     * 來去搜索資料裡有包含該課程名稱的課程
     * 
     * @param  string $Name 課程名稱
     * @return bool|array
     */
    public function search_Courses_By_Name(string $Name): bool|array
    {
        $sql = "SELECT * FROM Course WHERE Name LIKE  ? ";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute(['%' . $Name . '%']);
        if (!$ret) {
            return false;
        }
        return $stmt->fetchall();
    }

    /**
     * search_Courses_By_Dept
     *
     * 利用系所
     * 來去搜索資料裡有包含該系所的課程
     * 
     * @param  mixed $dept 系所
     * @return bool|array
     */
    public function search_Courses_By_Dept(?string $dept): bool|array
    {
        $sql = "SELECT * FROM Course WHERE dept LIKE ? ";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$dept]);
        if (!$ret) {
            return false;
        }
        return $stmt->fetchAll();
    }

    /**
     * search_Courses_By_clsname
     *
     * 利用課程班級名稱
     * 來去搜索資料裡有包含該名稱的課程
     * 
     * @param  mixed $cls_name
     * @return bool|array
     */
    public function search_Courses_By_clsname(?string $cls_name): bool|array
    {
        $sql = "SELECT * FROM Course WHERE cls_name LIKE ? ";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute(['%' . $cls_name . '%']);
        if (!$ret) {
            return false;
        }
        return $stmt->fetchAll();
    }

    /**
     * Insert_Request_Course
     *
     * 利用使用者名稱，系所，課程班級名稱
     * 將該使用者的必修課程加入已選課程清單
     * 
     * @param  string $username 使用者名稱NID
     * @param  string $dept     系所
     * @param  string $cls_name 課程班級名稱
     * @return bool
     */
    public function Insert_Request_Course(string $username, string $dept, string $cls_name): bool
    {
        if (!isset($dept) || !isset($cls_name)) {
            return false;
        }
        $sql = "SELECT DISTINCT Course_ID FROM CourseTimeSlots WHERE Course_ID IN(SELECT ID FROM Course WHERE dept = ? AND cls_name = ? AND request = 1)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$dept, $cls_name]);
        if (!$ret) {
            return false;
        }
        $result = $stmt->fetchAll();
        $user = $this->check_User($username);
        foreach ($result as $row) {
            $temp = intval($row['Course_ID']);
            $this->insert_TimeTable($temp, $user['id'], 2);
        }
        return true;
    }

    /**
     * get_total_credits
     *
     * 利用使用者名稱
     * 取得使用者的總學分
     * 
     * @param  string $username 使用者名稱NID
     * @return int
     */
    public function get_total_credits(string $username): int
    {
        $sql = "SELECT Total_credits FROM Users WHERE username = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$username]);
        if (!$ret) {
            return false;
        }
        $temp = $stmt->fetch();
        $totalCredits = $temp['Total_credits'];
        return $totalCredits;
    }

    /**
     * get_Courses_Time
     *
     * 利用使用者名稱
     * 取得使用者的課程時間，課程ID，課程名稱
     * 
     * @param  string $username 使用者名稱NID
     * @return bool|array
     */
    public function get_Courses_Time(string $username): bool|array
    {
        $user = $this->check_User($username);
        $sql = "SELECT CourseTimeSlots.Course_ID,CourseTimeSlots.Time_Slot_ID,Course.Name 
                FROM CourseTimeSlots,Course
                WHERE CourseTimeSlots.course_ID in(SELECT course_ID FROM TimeTable WHERE user_id = ?) 
                AND CourseTimeSlots.Course_ID = Course.ID";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$user['id']]);
        if (!$ret) {
            return false;
        }
        return $stmt->fetchall();
    }

    /**
     * get_Courses_Timeslot
     *
     * 利用課程ID
     * 取得課程的時間，課程ID，課程名稱
     * 
     * @param  int $courseID 課程時間，課程ID，課程名稱
     * @return bool|array
     */
    public function get_Courses_Timeslot(int $courseID): bool|array
    {
        $sql = "SELECT CourseTimeSlots.Course_ID,CourseTimeSlots.Time_Slot_ID,Course.Name 
                FROM CourseTimeSlots,Course 
                WHERE Course_ID = ? AND CourseTimeSlots.Course_ID = Course.ID";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$courseID]);
        if (!$ret) {
            return false;
        }
        return $stmt->fetchall();
    }

    /**
     * get_Courses_courseid_check
     *
     * 利用課程ID
     * 搜尋出該課程的選課狀態與課程ID
     * 
     * @param  int $courseID 課程ID
     * @return bool|array
     */
    public function get_Courses_courseid_check(int $courseID): bool|array
    {
        $sql = "SELECT `course_ID`,`check` FROM `TimeTable` WHERE `course_ID` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$courseID]);
        if (!$ret) {
            return false;
        }
        return $stmt->fetchall();
    }

    /**
     * update_courseid_check
     *
     * 利用課程ID和選課狀態
     * 更新課程的選課狀態
     * 
     * @param int $courseID 課程ID
     * @param int $check 0:未選課 1:關注課程 2:已加選
     * @return bool
     */
    public function update_courseid_check(int $courseID, int $check): bool
    {
        $sql = "UPDATE TimeTable SET `check` = ? WHERE `course_ID` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$check, $courseID]);
        if (!$ret) return false;
        return true;
    }

    /**
     * Courses_Time_check
     *
     * 利用使用者名稱與選課狀態
     * 確認使用者是否有關注的課程
     * 
     * @param  string $username
     * @param  int $check_NUM
     * @return bool|array
     */
    public function Courses_Time_check(string $username, int $check_NUM): bool|array
    {
        $user = $this->check_User($username);
        $sql = "SELECT CourseTimeSlots.Course_ID,CourseTimeSlots.Time_Slot_ID,Course.Name 
                FROM CourseTimeSlots,Course
                WHERE CourseTimeSlots.course_ID in(SELECT course_ID FROM TimeTable WHERE user_id = ? AND `check` = ?) 
                AND CourseTimeSlots.Course_ID = Course.ID";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$user['id'], $check_NUM]);
        if (!$ret) {
            return false;
        }
        return $stmt->fetchall();
    }

    /**
     * get_Courses_Time_check1
     *
     * 利用使用者名稱與選課狀態
     * 取得使用者關注的課程所有資訊
     *
     * @param  string $username 使用者名稱NID
     * @param  int $check_NUM 0:未選課 1:關注課程 2:已加選
     * @return bool|array
     */
    public function get_Courses_Time_check1(string $username, int $check_NUM): bool|array
    {
        $user = $this->check_User($username);
        $sql = "SELECT * From Course Where ID in (SELECT Course_ID FROM TimeTable WHERE user_id = ? AND `check` = ?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$user['id'], $check_NUM]);
        if (!$ret) {
            return false;
        }
        return $stmt->fetchall();
    }

    /**
     * Update_TimeTable
     *
     * 利用課程ID、使用者ID、選課狀態
     * 使關注的課程變成已加選的課程
     * 
     * @param  string $courseID 課程ID
     * @param  int $user_id 使用者的ID
     * @param  int $check 0:未選課 1:關注課程 2:已加選
     * @return bool
     */
    public function Update_TimeTable(int $courseID, int $user_id, int $check): bool
    {
        $sql = "UPDATE TimeTable SET `check` = ? WHERE `course_ID` = ? AND `user_id` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$check, $courseID, $user_id]);
        if (!$ret) return false;
        return true;
    }
    
    /**
     * get_course_currentpeople
     *
     * 利用課程ID
     * 取得該課程的人數
     * 
     * @param  int $course_id 課程ID
     * @return int
     */
    public function get_course_currentpeople(int $course_id): int
    {
        $sql = "SELECT CurrentPeople FROM Course WHERE ID = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$course_id]);
        $result = $stmt->fetch();
        if (!$ret) {
            return false;
        }
        return $result['CurrentPeople'];
    }
    
    /**
     * get_course_Name
     *
     * 利用課程ID
     * 取得該課程的名稱
     * 
     * @param  int $course_id
     * @return string
     */
    public function get_course_Name(int $course_id): string
    {
        $sql = "SELECT Name FROM Course WHERE ID = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$course_id]);
        $result = $stmt->fetch();
        if (!$ret) {
            return false;
        }
        return $result['Name'];
    }
    
    /**
     * check_TimeSlot_Cursh
     *
     * 利用課程ID與使用者名稱
     * 檢查該課程時間是否與已選課程時間衝突
     * 
     * @param  int $courseID 課程ID
     * @param  string $username 使用者名稱
     * @return bool
     */
    public function check_TimeSlot_Cursh(int $courseID, string $username): bool   //true=不衝突，false=衝突
    {
        $newCourse = $this->get_Courses_Timeslot($courseID);
        $result = $this->Courses_Time_check($username,2);
         
        foreach ($newCourse as $newSlot) {
            foreach ($result as $row) {
                if($newSlot['Time_Slot_ID']==$row['Time_Slot_ID']){
                    return false;
                }
            }
        }
        return true;
    }
}
