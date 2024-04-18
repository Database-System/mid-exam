<?php
use PHPUnit\Framework\TestCase;
use Exam\Core\Controller;
use Exam\Core\Connect;
use Exam\Utils\Insert_data;
require_once './src/Core/Controller.php';
require_once './src/Utils/Insert_data.php';

class Insert_data_Test extends TestCase
{
    protected static $controller;
    protected static $insert_data;
    private static $handler;
    
    public static function setUpBeforeClass(): void
    {
        $connect = new Connect();
        self::$handler = $connect->getHandler();

    }
    protected function setUp():void
    {
        $this->delete_table();
        self::$controller = new Controller();
        self::$insert_data = new Insert_data();
    }
    private function delete_table()
    {
        $need_table=["TimeTable","CourseTimeSlots","TimeSlot","Course"];
        foreach($need_table as $table)
        {
            if(!$this->table_Exists($table))
            {
                continue;
            }
            self::$handler->exec("DROP TABLE `$table`");
        }
    }
    private function table_Exists($table)
    {
        $stmt = self::$handler->query("SHOW TABLES LIKE '$table'");
        return !($stmt->rowCount() == 0);
    }
    public function testInsertData()
    {
        
        $result=self::$insert_data->insert_json_data();
        $this->assertTrue($result);
        
    }

    // protected function tearDown(): void
    // {
    //     $this->delete_table();
    // }
    // public static function tearDownAfterClass(): void
    // {
    //     self::$controller = null;
    //     self::$handler = null;
    // }
}