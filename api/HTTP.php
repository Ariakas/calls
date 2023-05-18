<?php
    namespace App;

    class HTTP {
        static function response_error($detail = null) {
            $response = [
                "status" => "error"
            ];
            if (!is_null($detail)) {
                $response["detail"] = $detail;
            }
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }

        static function response_success($detail = null) {
            $response = [
                "status" => "success"
            ];
            if (!is_null($detail)) {
                $response["detail"] = $detail;
            }
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }
    }