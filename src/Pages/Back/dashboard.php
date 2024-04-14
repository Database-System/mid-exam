<?php

namespace Exam\Pages\Back;

use Exam\Pages\twigLoader;
use Exam\Utils\Utils;
if (!session_id()) session_start();

class Dashboard
{
    private $options = array();
    public function __construct()
    {
        Utils::isLogin();
        $this->userdata_preload($_SESSION['userID']);
        $this->options = [
            "x0y0" => ["1411","1433","1435"],
            "x0y0-title" => ["國文","國文1","國文2"],
            "x0y1" => 1412,
            "x0y1-title" => "英文",
            "x0y13" => [1413,1415,1444],
        ];
        $this->options["x0y13-title"] = ["數學","數學1","數學2"];
        $this->options["display"] = true;
        if ($_SERVER["REQUEST_METHOD"] == "POST") $this->parse_arg();
        $this->renderPage($this->options);
    }
    private function parse_arg(){
        $class_data = array();
        $condition_data = array();
        $class_search_name = ["deptId", "unitId", "classId"];
        $condition_search_name = ["code", "week", "unit","course","checkcode","checkweek","checkcourse"];
        foreach ($class_search_name as $name) {
            if(!isset($_POST[$name]) || empty($_POST[$name])){
                continue;
            }
            $class_data[$name] = $_POST[$name];
        }
        foreach ($condition_search_name as $name) {
            if(!isset($_POST[$name]) || empty($_POST[$name])){
                continue;
            }
            $condition_data[$name] = $_POST[$name];
        }
        if(count($class_data)!=0 && count($condition_data)==0){
            $this->process_class_form($class_data);
        }
        else if(count($class_data)==0 && count($condition_data)!=0){
            $this->process_condition_form($condition_data);
        }
        else die("Invalid form data");
    }
    private function process_class_form(array $data){
        $this->options["searchResult"] = $data;
    }
    private function process_condition_form(array $data){

        $this->options["searchResult"] = $data;
    }
    private function renderPage(array $OPTION){
        new twigLoader(__FILE__,false, $OPTION);
    }
    private function userdata_preload(string $user){
        $this->options["NID"] = $user;
    }
}
