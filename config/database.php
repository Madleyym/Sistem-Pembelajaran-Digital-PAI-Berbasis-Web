<?php
// config/database.php

class Database
{
    private $host = "localhost";
    private $db_name = "pai";
    private $username = "root";  // Ganti sesuai dengan username database Anda
    private $password = "";      // Ganti sesuai dengan password database Anda
    private $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->exec("SET NAMES utf8");
        } catch (PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed. Please check your configuration.");
        }

        return $this->conn;
    }

    public function closeConnection()
    {
        $this->conn = null;
    }

    // Method untuk testing koneksi
    public function testConnection()
    {
        try {
            $this->getConnection();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

