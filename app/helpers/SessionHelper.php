<?php
class SessionHelper {
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_lifetime' => 86400, // 1 ngày
                'cookie_secure' => isset($_SERVER['HTTPS']),
                'cookie_httponly' => true,
                'use_strict_mode' => true
            ]);
        }
    }

    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin() {
        self::startSession();
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public static function getUserId() {
        self::startSession();
        return $_SESSION['user_id'] ?? null;
    }

    public static function getUsername() {
        self::startSession();
        return $_SESSION['username'] ?? null;
    }

    public static function setUser($user) {
        self::startSession();
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['role'] = $user->role;
    }

    public static function destroySession() {
        self::startSession();
        session_unset();
        session_destroy();
        session_write_close();
        setcookie(session_name(), '', time() - 3600, '/');
    }

    public static function setFlash($key, $value) {
        self::startSession();
        $_SESSION['flash'][$key] = $value;
    }

    public static function getFlash($key) {
        self::startSession();
        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $value;
    }
}