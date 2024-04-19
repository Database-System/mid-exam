<?php

namespace Exam\Pages\Back;

use Exam\Pages\twigLoader;
use Exam\Utils\Utils;
use Exam\Core\Controller;

if (!session_id()) session_start();
class GetDoneTable
{
    protected $controller;
    private $options = array();
    public function __construct()
    {
        $this->controller = new Controller();
        Utils::isLogin();
        // $this->checkout();
        $this->puttable();
        $this->renderPage($this->options);
    }
    private function renderPage(array $OPTION)
    {
        new twigLoader(__FILE__, false, $OPTION);
    }
    private function puttable()
    {

        $result = $this->controller->Courses_Time_check($_SESSION['userID'],2);
        foreach ($result as $row) {
            $weekday = intdiv($row['Time_Slot_ID'], 14);
            $unit = $row['Time_Slot_ID'] % 14 - 1;
            $this->options["x" . $weekday . "y" . $unit][] = $row['Course_ID'];
            $this->options["x" . $weekday . "y" . $unit . "-title"][] = $row['Name'];
        }
    }
    private function checkout()
    {
        $result = $this->controller->Courses_Time_check($_SESSION['userID'],1);
        if($result==null){
            $this->options["display"] = true;
            return ;
        }
        else{
            $this->options["display"] = false;
        }
        $course = $this->controller->get_Courses_Time_check1($_SESSION['userID'],1);
        $allCoursesInfo = [];
        foreach($course as $CoursesInfo){
            $CoursesInfo = [
                'courseCode' => $CoursesInfo['ID'],
                'department' => $CoursesInfo['dept'],
                'subject' => $CoursesInfo['Name'],
                'class' => $CoursesInfo['cls_name'],
                'type' => $CoursesInfo['request'] == 0 ? '¿ï­×' : '¥²­×',
                'credits' => $CoursesInfo['Credits']
            ];
            $allCoursesInfo[] = $CoursesInfo;
        }
        $this->options["choiceResult"] = $allCoursesInfo;
    }
}
