<?php

namespace App;
require "src/RtcTokenBuilder.php";
use RtcTokenBuilder, DateTime, DateTimeZone;

class Rtc {
    static function get_token($channelName): string {
        $config = new Config();
        $appID = $config->get_rtc_app_id();
        $appCertificate = $config->get_rtc_certificate();
        $uid = Session::get_user_id();
        $role = RtcTokenBuilder::RoleAttendee;
        $expireTimeInSeconds = 3600;
        $currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
        return RtcTokenBuilder::buildTokenWithUid(
            $appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs
        );
    }
}