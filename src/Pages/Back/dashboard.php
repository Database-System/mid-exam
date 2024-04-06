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
        $this->options["NID"] = $_SESSION['userID'];
        $this->options["x0y0"] = 1411;
        $this->options["x0y1"] = 1412;
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
}
