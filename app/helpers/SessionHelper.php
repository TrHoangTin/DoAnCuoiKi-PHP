<?php
class SessionHelper {
    public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
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

    public static function getJWTToken() {
        self::startSession();
        return $_SESSION['jwt_token'] ?? null;
    }

    public static function destroySession() {
        self::startSession();
        session_unset();
        session_destroy();
    }
}