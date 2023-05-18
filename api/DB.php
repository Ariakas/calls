<?php

namespace App;

use DateTime;
use mysqli;

class DB {
    public mysqli $link;

    public function __construct() {
        $config = new Config();
        $this->link = new mysqli($config->get_db_host(), $config->get_db_user(), $config->get_db_pass(), $config->get_db_name());
    }

    private function escape_all(&...$params) {
        foreach ($params as &$param) {
            $param = $this->link->real_escape_string($param);
        }
    }

    public function add_user($login, $password, $name): bool {
        $this->escape_all($login, $password, $name);
        $login_exists = $this->link->query("SELECT * FROM `users` WHERE `Login` = '$login'");
        if ($login_exists && !$login_exists->num_rows) {
            $this->link->query("INSERT INTO `users` (`Login`, `Password`, `Name`) VALUES ('$login', '$password', '$name')");
            return true;
        }
        return false;
    }

    public function get_user_by_id($id): array {
        $this->escape_all($id);
        $user = $this->link->query("SELECT * FROM `users` WHERE `Id` = $id");
        if ($user && $user->num_rows) {
            return $user->fetch_assoc();
        }
        return [];
    }

    public function get_user_by_login($login): array {
        $this->escape_all($login);
        $user = $this->link->query("SELECT * FROM `users` WHERE `Login` = '$login'");
        if ($user && $user->num_rows) {
            return $user->fetch_assoc();
        }
        return [];
    }

    public function add_link(): string {
        $short = sha1(time() . rand(0, 1000));
        $user_id = Session::get_user_id();
        $this->link->query("INSERT INTO `links` (`User_id`, `Link`) VALUES ($user_id, '$short')");
        return $short;
    }

    public function get_link_by_id($id): array {
        $this->escape_all($id);
        $link = $this->link->query("SELECT * FROM `links` WHERE `Id` = $id");
        if ($link && $link->num_rows) {
            return $link->fetch_assoc();
        }
        return [];
    }

    public function get_link_by_text($link): array {
        $this->escape_all($link);
        $link = $this->link->query("SELECT * FROM `links` WHERE `Link` = '$link'");
        if ($link && $link->num_rows) {
            return $link->fetch_assoc();
        }
        return [];
    }

    public function add_history($link) {
        $link = $this->get_link_by_text($link);
        $link_id = $link["Id"];
        $user_id = Session::get_user_id();
        $exists = $this->link->query("SELECT * FROM `history` WHERE `Link_id` = $link_id");
        if ($exists && $exists->num_rows) {
            $this->escape_all($user_id);
            $this->link->query("UPDATE `history` SET `User_id_2` = $user_id WHERE `Link_id` = $link_id");
        }
        else {
            $date = (new DateTime())->format("Y-m-d H:i:s");
            $this->escape_all($user_id, $date);
            $this->link->query("INSERT INTO `history` (`User_id`, `User_id_2`, `Link_id`, `Date`, `Duration`) VALUES ($user_id, NULL, $link_id, '$date', 0)");
        }
    }

    public function update_history($link) {
        $link = $this->get_link_by_text($link);
        if ($link) {
            $link_id = $link["Id"];
            $this->escape_all($link_id);
            $this->link->query("UPDATE `history` SET `Duration` = `Duration` + 1 WHERE `Link_id` = $link_id");
        }
    }

    public function get_history(): array {
        $user_id = Session::get_user_id();
        $this->escape_all($user_id);
        $history = $this->link->query(
            "SELECT `h`.`Date`, `h`.`Duration`, `u`.`Name` FROM `history` `h`
                    LEFT JOIN `users` `u` ON `u`.`Id` = `h`.`User_id_2`
                    WHERE `h`.`User_id` = $user_id AND `User_id_2` IS NOT NULL 
                    UNION 
                    SELECT `h`.`Date`, `h`.`Duration`, `u`.`Name` FROM `history` `h`
                    LEFT JOIN `users` `u` ON `u`.`Id` = `h`.`User_id`
                    WHERE `h`.`User_id_2` = $user_id"
        );
        if ($history && $history->num_rows) {
            return $history->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function get_history_record($link_id): array {
        $this->escape_all($link_id);
        $record = $this->link->query("SELECT * FROM `history` WHERE `Link_id` = $link_id AND `User_id_2` IS NULL ");
        if ($record && $record->num_rows) {
            return $record->fetch_assoc();
        }
        return [];
    }
}