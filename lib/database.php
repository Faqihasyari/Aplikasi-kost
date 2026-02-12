<?php
/**
 * Database Configuration & Connection
 * File: lib/database.php
 */

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'coba_kost');

// Koneksi Database
class Database {
    private static $connection = null;
    
    /**
     * Get database connection
     */
    public static function connect() {
        if (self::$connection === null) {
            self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if (self::$connection->connect_error) {
                die("Koneksi database gagal: " . self::$connection->connect_error);
            }
            
            self::$connection->set_charset("utf8mb4");
        }
        
        return self::$connection;
    }
    
    /**
     * Execute query
     */
    public static function query($sql) {
        $conn = self::connect();
        $result = $conn->query($sql);
        
        if (!$result) {
            die("Query error: " . $conn->error);
        }
        
        return $result;
    }
    
    /**
     * Escape string untuk keamanan
     */
    public static function escape($string) {
        $conn = self::connect();
        return $conn->real_escape_string($string);
    }
    
    /**
     * Get last insert ID
     */
    public static function getLastId() {
        $conn = self::connect();
        return $conn->insert_id;
    }
    
    /**
     * Fetch single row
     */
    public static function fetchOne($sql) {
        $result = self::query($sql);
        return $result->fetch_assoc();
    }
    
    /**
     * Fetch all rows
     */
    public static function fetchAll($sql) {
        $result = self::query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
}
