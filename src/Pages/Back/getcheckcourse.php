<?php

namespace Exam\Pages\Back;

use Exam\Pages\twigLoader;
use Exam\Utils\Utils;
use Exam\Core\Controller;

if (!session_id()) session_start();
class getcheckcourse
{
    protected $controller;
    private $options = array();
    public function __construct()
    {
        $this->controller = new Controller();
        Utils::isLogin();
        $this->checkout();
        $this->renderPage($this->options);
    }
    private function renderPage(array $OPTION)
    {
        new twigLoader(__FILE__, false, $OPTION);
    }
    private function checkout()
    {
        $course = $this->controller->get_Courses_Time_check1($_SESSION['userID'],1);
        $allCoursesInfo = [];
        foreach($course as $CoursesInfo){
            $CoursesInfo = [
                'courseCode' => $CoursesInfo['ID'],
                'department' => $CoursesInfo['dept'],
                'subject' => $CoursesInfo['Name'],
                'class' => $CoursesInfo['cls_name'],
                'type' => $CoursesInfo['request'] == 0 ? '選修' : '必修',
                'credits' => $CoursesInfo['Credits']
            ];
            $allCoursesInfo[] = $CoursesInfo;
        }
        $this->options["choiceResult"] = $allCoursesInfo;
    }
}