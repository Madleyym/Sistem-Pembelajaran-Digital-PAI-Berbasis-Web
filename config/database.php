<?php
// database.php

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'pai');
define('DB_USER', 'root');  // Ganti sesuai dengan username database Anda
define('DB_PASSWORD', '');   // Ganti sesuai dengan password database Anda
define('DB_CHARSET', 'utf8mb4');

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
        } catch (PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Get database instance (Singleton pattern)
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Get database connection
    public function getConnection()
    {
        return $this->connection;
    }

    // Helper method untuk select query
    public function select($query, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Query error: " . $e->getMessage());
        }
    }

    // Helper method untuk single select query
    public function selectOne($query, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Query error: " . $e->getMessage());
        }
    }

    // Helper method untuk insert query
    public function insert($table, $data)
    {
        try {
            $fields = array_keys($data);
            $values = array_values($data);
            $placeholders = str_repeat('?,', count($fields) - 1) . '?';

            $query = "INSERT INTO {$table} (" . implode(',', $fields) . ") VALUES ($placeholders)";
            $stmt = $this->connection->prepare($query);
            $stmt->execute($values);

            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Insert error: " . $e->getMessage());
        }
    }

    // Helper method untuk update query
    public function update($table, $data, $where)
    {
        try {
            $fields = array_keys($data);
            $values = array_values($data);

            $set = implode('=?,', $fields) . '=?';
            $query = "UPDATE {$table} SET {$set} WHERE {$where}";

            $stmt = $this->connection->prepare($query);
            $stmt->execute($values);

            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Update error: " . $e->getMessage());
        }
    }

    // Helper method untuk delete query
    public function delete($table, $where, $params = [])
    {
        try {
            $query = "DELETE FROM {$table} WHERE {$where}";
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);

            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Delete error: " . $e->getMessage());
        }
    }

    // Helper method untuk begin transaction
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    // Helper method untuk commit transaction
    public function commit()
    {
        return $this->connection->commit();
    }

    // Helper method untuk rollback transaction
    public function rollback()
    {
        return $this->connection->rollBack();
    }
}

// Example usage dalam aplikasi:
/*
try {
    $db = Database::getInstance();
    
    // Select multiple rows
    $users = $db->select("SELECT * FROM users WHERE role = ?", ['siswa']);
    
    // Select single row
    $user = $db->selectOne("SELECT * FROM users WHERE id = ?", [1]);
    
    // Insert
    $userId = $db->insert('users', [
        'username' => 'john_doe',
        'email' => 'john@example.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'siswa'
    ]);
    
    // Update
    $affected = $db->update('users', 
        ['email' => 'new_email@example.com'],
        'id = 1'
    );
    
    // Delete
    $deleted = $db->delete('users', 'id = ?', [1]);
    
    // Transaction example
    $db->beginTransaction();
    try {
        $db->insert('table1', ['field' => 'value']);
        $db->insert('table2', ['field' => 'value']);
        $db->commit();
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    // Handle error
    echo "Error: " . $e->getMessage();
}
*/
