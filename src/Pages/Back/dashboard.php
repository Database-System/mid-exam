<?php

namespace Exam\Pages\Back;

use Exam\Pages\twigLoader;
use Exam\Utils\Utils;
use Exam\Core\Controller;

if (!session_id()) session_start();

class Dashboard
{
    protected $controller;
    private $options = array();
    public function __construct()
    {
        $this->controller = new Controller();
        Utils::isLogin();
        $this->userdata_preload($_SESSION['userID']);
        $this->options["total"] = $this->controller->get_total_credits($_SESSION['userID']);
        $this->options["display"] = true;
        if ($_SERVER["REQUEST_METHOD"] == "PUT") $this->handlePut();
        if ($_SERVER["REQUEST_METHOD"] == "POST") $this->parse_arg();
        $this->puttable();
        $this->renderPage($this->options);
    }
    private function parse_arg()
    {
        $this->options["queryPerformed"] = true;
        $class_data = array();
        $condition_data = array();
        $class_search_name = ["deptId", "unitId", "classId"];
        $condition_search_name = ["code", "week", "unit", "course", "checkcode", "checkweek", "checkcourse"];
        foreach ($class_search_name as $name) {
            if (!isset($_POST[$name]) || empty($_POST[$name])) {
                continue;
            }
            if ($name == "deptId")
                $class_data = $this->controller->search_Courses_By_Dept($_POST['deptId']);
            else if ($name == "unitId")
                $class_data = $this->controller->search_Courses_By_Dept($_POST['unitId']);
            else if ($name == "classId")
                $class_data = $this->controller->search_Courses_By_clsname($_POST['classId']);
        }
        foreach ($condition_search_name as $name) {
            if (!isset($_POST[$name]) || empty($_POST[$name])) {
                continue;
            }
            if ($name == "code") {
                $condition_data = $this->controller->check_Course($_POST['code']);
            } else if ($name == "week") {

                $temp_week = intval($_POST['week']);
                $temp_unit = intval($_POST['unit']);
                $temp = ($temp_week - 1) * 14 + ($temp_unit);
                $condition_data = $this->controller->search_Courses_By_TimeSlot($temp);
            } else if ($name == "course") {
                $condition_data = $this->controller->search_Courses_By_Name($_POST[$name]);
            }
        }
        if (count($class_data) != 0 && count($condition_data) == 0) {
            $this->process_class_form($class_data);
        } else if (count($class_data) == 0 && count($condition_data) != 0) {

            $this->process_condition_form($condition_data);
        }
    }
    private function process_class_form(array $data)
    {
        $allCoursesInfo = [];

        foreach ($data as $course) {
            $courseInfo = [
                'courseCode' => $course['ID'],
                'department' => $course['dept'],
                'subject' => $course['Name'],
                'class' => $course['cls_name'],
                'type' => $course['request'] == 0 ? '選修' : '必修',
                'credits' => $course['Credits']
            ];
            $allCoursesInfo[] = $courseInfo;
        }

        $this->options["searchResult"] = $allCoursesInfo;
    }
    private function process_condition_form(array $data)
    {
        $allCoursesInfo = [];

        foreach ($data as $course) {
            $courseInfo = [
                'courseCode' => $course['ID'],
                'department' => $course['dept'],
                'subject' => $course['Name'],
                'class' => $course['cls_name'],
                'type' => $course['request'] == 0 ? '選修' : '必修',
                'credits' => $course['Credits']
            ];
            $allCoursesInfo[] = $courseInfo;
        }

        $this->options["searchResult"] = $allCoursesInfo;
    }
    private function handlePut()
    {
        $temp1 = [
            "msg" => "success"
        ];

        $temp = json_encode($temp1);
        $data = json_decode(file_get_contents('php://input'), true);
        die($temp);
    }
    private function puttable()
    {
        $result = $this->controller->get_Courses_Time($_SESSION['userID']);
        foreach($result as $row){
            $weekday = intdiv($row['Time_Slot_ID'],14);
            $unit = $row['Time_Slot_ID']%14; 
            $this->options["x".$weekday."y".$unit] = $row['Course_ID'];
            $this->options["x".$weekday."y".$unit."-title"] = $row['Name'];
        }
    }
    private function renderPage(array $OPTION)
    {
        new twigLoader(__FILE__,false, $OPTION);
    }
    private function userdata_preload(string $user)
    {
        $this->options["NID"] = $user;
    }
}
