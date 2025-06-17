<?php
class Database {
    private $host = "localhost";
    private $db_name = "wshooes_db";
    private $username = "root";
    private $password = "";
    private static $instance = null;
    private $conn = null;

    private function __construct() {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            // Set charset to utf8
            $this->conn->set_charset("utf8");
        } catch(Exception $e) {
            error_log("Connection error: " . $e->getMessage());
            throw new Exception("Sorry, there was a problem connecting to the database. Please try again later.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        if ($this->conn === null || !$this->conn->ping()) {
            // Try to reconnect
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            $this->conn->set_charset("utf8");
        }
        return $this->conn;
    }

    // Function to sanitize input data
    public static function sanitize_input($data) {
        if (empty($data)) {
            return '';
        }
        
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        
        $conn = self::getInstance()->getConnection();
        return $conn->real_escape_string($data);
    }

    // Function to handle database errors
    public static function handle_db_error($query) {
        $conn = self::getInstance()->getConnection();
        error_log("MySQL Error: " . $conn->error . " in query: " . $query);
        throw new Exception("Sorry, there was a database error. Please try again later.");
    }
}