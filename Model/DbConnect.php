<?php

class DbConnect
{
    private $host = "127.0.0.1";
    private $user = "ll_lms_assignment";
    private $pass = "8CkPsMY0xbB4N7Uq";
    private $dbName = "ll_lms_assignment";
    private $conn;

    public function connect()
    {
        try {
            // Initialize database connection
            $this->conn = new PDO('mysql:host=' . $this->host . '; dbname=' . $this->dbName, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->conn;
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    }
    public function close()
    {
        $this->conn = null;
    }
}
