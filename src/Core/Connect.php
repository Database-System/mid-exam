<?php
namespace Exam\Core;
use PDO;
use PDOException;
use Exam\Setting\Config;

class Connect {
    private $db_handle = null;
    private $db_host = null;
    private $db_name = null;
    private $db_user = null;
    private $db_pass = null;

    function __construct() {
        $this->db_host = Config::get("db_host");
        $this->db_name = Config::get("db_name");
        $this->db_user = Config::get("db_user");
        $this->db_pass = Config::get("db_pass");
    }

    public function connect() {
        try {
            $dsn = "mysql:host={$this->db_host};dbname={$this->db_name}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->db_handle = new PDO($dsn, $this->db_user, $this->db_pass, $options);
        } catch (PDOException $e) {
            die("DB Connection Failed: " . $e->getMessage());
        }
    }

    public function getHandler() {
        if ($this->db_handle === null) {
            $this->connect();
        }
        return $this->db_handle;
    }
}
