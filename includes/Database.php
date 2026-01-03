<?php
require_once __DIR__ . '/../config/config.php';

class Database {
    private $conn;

    public function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw $e;
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql) {
        return $this->conn->query($sql);
    }

    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    public function escapeString($string) {
        return $this->conn->real_escape_string($string);
    }

    public function lastInsertId() {
        return $this->conn->insert_id;
    }

    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
