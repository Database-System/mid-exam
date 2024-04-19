<?php 
namespace Exam\Pages\Back;

use Exam\Pages\twigLoader;
use Exam\Utils\Utils;
use Exam\Core\Controller;

if (!session_id()) session_start();

class GetCalendar
{
    protected $controller;
    private $options = array();
    public function __construct() {
        $this->controller = new Controller();
        Utils::isLogin();
        $this->puttable();
        $this->renderPage($this->options);
    }
    private function renderPage(array $OPTION)
    {
        new twigLoader(__FILE__, false, $OPTION);
    }
    private function puttable()
    {
        $result = $this->controller->get_Courses_Time($_SESSION['userID']);
        foreach ($result as $row) {
            $weekday = intdiv($row['Time_Slot_ID'], 14);
            $unit = $row['Time_Slot_ID'] % 14 - 1;
            $this->options["x" . $weekday . "y" . $unit][] = $row['Course_ID'];
            $this->options["x" . $weekday . "y" . $unit . "-title"][] = $row['Name'];
        }
    }
}