<?php
namespace Exam\Utils;
// chdir(dirname(__DIR__,2));
// require_once('./vendor/autoload.php');


use Exam\Core\Controller;


class Insert_data
{
    protected $controller; 

    public function __construct()
    {
        $this->controller = new Controller();
    }

    public function insert_json_data():bool
    {
        // $jsonFiles = glob('./*.json');
        $jsonFiles = glob('./src/Resource/112/1122*.json');
        $allSuccess = true; 
        foreach ($jsonFiles as $file) {
            $jsondata = file_get_contents($file);
            $records = json_decode($jsondata, true);
            foreach ($records["items"] as $record) {
                $ID_temp = $record["scr_selcode"];
                $ID = (int)$ID_temp;
                if ($this->checkIfIdExists($ID)) {
                    // 如果ID已存在，跳過這條記錄
                    continue;
                }
                $Name = $record['sub_name'];
                $cls_name=$record['cls_name'];
                $dept1 = substr($record['cls_id'],0,4);
                $dept = $this->search_dept($dept1); 
                $request = $record ['scj_scr_mso'] == "選修" ? 0 : 1;
                $Credits = $record ['scr_credit'];
                $MaxPeople = $record ['scr_acptcnt'];
                $ret = $this->controller->insert_Course($ID, $Name,$cls_name, $dept, $request, $Credits, $MaxPeople);
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

                    if($this->checkIfIdExists($time)||$time % 100 == 0)
                    {
                        continue;
                    }
                    $time = (intdiv($time, 100)-1) * 14 + $time % 100;
                    $ret = $this->controller->insert_CourseTimeSlots($ID, $time);
                }
                if (!$ret) {
                    $allSuccess = false;
                }
            }
        }

        return $allSuccess; 
    }
    
    public function chineseToNumber($chinese)
    {
        $numbers = [
            '一' => 1, '二' => 2, '三' => 3, '四' => 4,
            '五' => 5, '六' => 6, '日' => 7
        ];

        return isset($numbers[$chinese]) ? $numbers[$chinese] : 'Unknown';
    }

    public function search_dept($dept_id): string|NULL
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
        $ret= $this->controller->check_Course($ID);

        if(!$ret)
        {
            return false;
        }
        return true;
    }
}


// $test = new Insert_data();
// $test->insert_json_data();
