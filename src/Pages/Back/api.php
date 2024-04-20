<?php

namespace Exam\Pages\Back;

use Exam\Utils\Utils;
use Exam\Core\Controller;

if (!session_id()) session_start();


class Api
{
    protected $controller;
    // private $options = array();
    public function __construct()
    {
        $this->controller = new Controller();
        Utils::isLogin();
        switch ($_SERVER["REQUEST_METHOD"]) {
            case "PATCH":
                $this->parsePatch();
                break;
            case "POST":
                $this->parsePost();
                break;
            default:
                die("Method not found");
        }
    }
    private function getTotalCreadits()
    {
        $this->controller->Update_User_TotalCerdits($_SESSION['userID']);
        die(json_encode($this->controller->get_total_credits($_SESSION['userID'])));
    }

    private function countCheckcourse()
    {
        $ret = $this->controller->get_Courses_Time_check1($_SESSION['userID'], 1);
        $counter = count($ret);
        die(json_encode($counter));
    }
    private function get_user_dept()
    {
        $ret = $this->controller->check_User($_SESSION['userID']);
        die(json_encode($ret["dept"],JSON_UNESCAPED_UNICODE));
    }
    private function get_course_credit(int $courseID)
    {
        $ret = $this->controller->get_course_info($courseID);
        die(json_encode([
            "CourseID" => $ret["ID"],
            "Credits" => $ret["Credits"],
            "dept" => $ret["dept"],
            "Name"  => $ret["Name"]
        ],JSON_UNESCAPED_UNICODE));
    }
    private function parsePost()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $function = $data['function'] ?? "0";
        if ($function == "0") {
            die(json_encode("Action not found"));
        }
        switch ($function) {
            case "get_course_credit":
                $courseID = $data['courseID'] ?? "0";
                $courseID = intval($courseID);
                $this->get_course_credit($courseID);
                break;
            case "get_user_dept":
                $this->get_user_dept();
                break;
            default:
                die(json_encode("Function not found"));
        }
    }

    private function parsePatch()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $function = $data['function'] ?? "0";
        if ($function == "0") {
            die(json_encode("Action not found"));
        }
        switch ($function) {
            case "getTotalCreadits":
                $this->getTotalCreadits();
                break;
            case "countCheckcourse":
                $this->countCheckcourse();
                break;
            default:
                die(json_encode("Function not found"));
        }
    }
}
