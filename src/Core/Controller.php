<?php
namespace Exam\Core;
use Exam\Core\Connect;
class Controller {
    private $handler = null;
    private $need_tables = [
        "Users" => "CREATE TABLE Users (id INT AUTO_INCREMENT PRIMARY KEY,`username` VARCHAR(50) NOT NULL UNIQUE,`password` VARCHAR(255) NOT NULL)",
    ];
    public function __construct() {
        $connect = new Connect();
        $this->handler = $connect->getHandler();
        if(!isset($this->handler)) die("Can't get DB handler");
        $this->init_Table();
    }
    private function init_Table(){
        foreach($this->need_tables as $table => $val){
            if (!$this->table_Exists($table)){
                $this->handler->exec($val);
            }
        }
    }
    private function table_Exists($table){
        $stmt = $this->handler->query("SHOW TABLES LIKE '$table'");
        return !($stmt->rowCount() == 0);
    }
    public function insert_User(string $user,string $password){
        $sql = "INSERT INTO Users (`username`,`password`) VALUES (?,?)";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$user, $password]);
        if (!$ret){
            $errorInfo = $stmt->errorInfo();
            die("SQL 錯誤：" . $errorInfo[2]);
        }
    }
    public function check_User(string $user){
        $sql = "SELECT * from `Users` WHERE `username` = ?";
        $stmt = $this->handler->prepare($sql);
        $ret = $stmt->execute([$user]);
        if (!$ret) return false;
        return $stmt->fetch();
    }
}
