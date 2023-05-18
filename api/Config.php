<?php

namespace App;

class Config {
    private array $config;
    public function __construct() {
        $config = file_get_contents("rtc_config.json");
        $this->config = json_decode($config, true);
    }

    public function get_rtc_app_id() {
        return $this->config["appID"];
    }

    public function get_rtc_certificate() {
        return $this->config["appCertificate"];
    }
}