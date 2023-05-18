<?php

namespace App;

class Session {
    private static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function un_init() {
        self::init();
        session_unset();
    }

    public static function set_user_id($id) {
        self::init();
        $_SESSION["user_id"] = $id;
    }

    public static function get_user_id(): int {
        self::init();
        return $_SESSION["user_id"] ?? 0;
    }
}