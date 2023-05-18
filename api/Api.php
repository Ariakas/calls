<?php

namespace App;

use DateTime;

class Api {
    static function login($params) {
        $login = $params["login"];
        $password = $params["password"];
        $db = new DB();
        $user = $db->get_user_by_login($login);
        if ($user && password_verify($password, $user["Password"])) {
            Session::set_user_id($user["Id"]);
            HTTP::response_success($user["Id"]);
        }
        else {
            HTTP::response_error("Неверный логин или пароль!");
        }
    }

    static function logout() {
        Session::un_init();
        HTTP::response_success();
    }

    static function register($params) {
        $login = $params["login"];
        $password = password_hash($params["password"], PASSWORD_BCRYPT);
        $name = $params["name"];
        $db = new DB();
        $user = $db->get_user_by_login($login);
        if (!$user) {
            $db->add_user($login, $password, $name);
            HTTP::response_success();
        }
        else {
            HTTP::response_error("Логин уже занят!");
        }
    }

    static function get_user_id() {
        $user_id = Session::get_user_id();
        if ($user_id) {
            HTTP::response_success($user_id);
        }
        else {
            HTTP::response_error();
        }
    }

    static function get_link() {
        $db = new DB();
        $link = $db->add_link();
        $db->add_history($link);
        HTTP::response_success($link);
    }

    static function test_link($params) {
        $link = $params["link"];
        $db = new DB();
        $link = $db->get_link_by_text($link);
        if ($link) {
            $history = $db->get_history_record($link["Id"]);
            if ($history && !$history["User_id_2"]) {
                HTTP::response_success();
            }
            else {
                HTTP::response_error();
            }
        }
        else {
            HTTP::response_error();
        }
    }

    static function get_rtc_token($params) {
        $link = $params["link"];
        $token = Rtc::get_token($link);
        HTTP::response_success($token);
    }

    static function connect($params) {
        $link = $params["link"];
        $db = new DB();
        $db->add_history($link);
        HTTP::response_success();
    }

    static function indicate_usage($params) {
        $link = $params["link"];
        $db = new DB();
        $db->update_history($link);
        HTTP::response_success();
    }

    static function get_history() {
        $db = new DB();
        $history = $db->get_history();
        $history = array_map(function($call) {
            $call["Date"] = DateTime::createFromFormat("Y-m-d H:i:s", $call["Date"])->format("d.m.Y H:i:s");
            $minutes = $call["Duration"] % 60;
            $hours = floor($call["Duration"] / 60);
            $call["Duration"] =  substr("0$hours", -2) . ":" . substr("0$minutes", -2);
            return array_change_key_case($call);
        }, $history);
        HTTP::response_success($history);
    }
}