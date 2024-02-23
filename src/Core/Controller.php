<?php

namespace Exam\Core;

use Exam\Core\Connect;

class Controller
{
    private $handler = null;
    private $need_tables = [
        "Users" => "CREATE TABLE Users (id INT AUTO_INCREMENT PRIMARY KEY,username VARCHAR(50) NOT NULL UNIQUE,password VARCHAR(255) NOT NULL)",
    ];
    public function __construct()
    {
        $connect = new Connect();
        $this->handler = $connect->getHandler();
        if (!isset($this->handler)) die("Can't get DB handler");
        $this->init_Table();
    }
    private function init_Table()
    {
        foreach ($this->need_tables as $table => $val) {
            if (!$this->table_Exists($table)) {
                $this->handler->exec($val);
            }
        }
    }
    private function table_Exists($table)
    {
        $stmt = $this->handler->query("SHOW TABLES LIKE '$table'");
        return !($stmt->rowCount() == 0);
    }
}
