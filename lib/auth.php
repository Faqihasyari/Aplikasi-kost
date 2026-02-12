<?php
/**
 * Authentication & Authorization Library
 * File: lib/auth.php
 */

require_once __DIR__ . '/database.php';

class Auth {
    
    /**
     * Start session
     */
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Login user
     */
    public static function login($username, $password) {
        $username = Database::escape($username);
        
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $user = Database::fetchOne($sql);
        
        if ($user && password_verify($password, $user['password'])) {
            self::init();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        self::init();
        session_unset();
        session_destroy();
    }
    
    /**
     * Check if user is logged in
     */
    public static function check() {
        self::init();
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get current user
     */
    public static function user() {
        self::init();
        if (self::check()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'],
                'nama_lengkap' => $_SESSION['nama_lengkap']
            ];
        }
        return null;
    }
    
    /**
     * Get user ID
     */
    public static function id() {
        self::init();
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get user role
     */
    public static function role() {
        self::init();
        return $_SESSION['role'] ?? null;
    }
    
    /**
     * Check if user has specific role
     */
    public static function hasRole($role) {
        return self::role() === $role;
    }
    
    /**
     * Require login - redirect if not logged in
     */
    public static function requireLogin() {
        if (!self::check()) {
            header('Location: /coba_kost/auth/login.php');
            exit;
        }
    }
    
    /**
     * Require specific role - redirect if not authorized
     */
    public static function requireRole($role) {
        self::requireLogin();
        
        if (!self::hasRole($role)) {
            header('Location: /coba_kost/index.php');
            exit;
        }
    }
    
    /**
     * Register new user
     */
    public static function register($data) {
        $username = Database::escape($data['username']);
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $nama_lengkap = Database::escape($data['nama_lengkap']);
        $email = Database::escape($data['email']);
        $no_hp = Database::escape($data['no_hp']);
        $role = Database::escape($data['role']);
        
        // Check if username exists
        $check = Database::fetchOne("SELECT id FROM users WHERE username = '$username'");
        if ($check) {
            return false;
        }
        
        $sql = "INSERT INTO users (username, password, nama_lengkap, email, no_hp, role) 
                VALUES ('$username', '$password', '$nama_lengkap', '$email', '$no_hp', '$role')";
        
        Database::query($sql);
        return true;
    }
}
