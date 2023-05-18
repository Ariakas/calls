<?php

namespace App;

class Config {
    private array $config;
    public function __construct() {
        $config = file_get_contents("rtc_config.json");
        $this->config = json_decode($config, true);
        $config = file_get_contents("db_config.json");
        $this->config = array_merge($this->config, json_decode($config, true));
    }

    public function get_rtc_app_id() {
        return $this->config["appID"];
    }

    public function get_rtc_certificate() {
        return $this->config["appCertificate"];
    }

    public function get_db_host() {
        return $this->config["host"];
    }

    public function get_db_user() {
        return $this->config["user"];
    }

    public function get_db_pass() {
        return $this->config["pass"];
    }

    public function get_db_name() {
        return $this->config["db"];
    }
}