<?php
    namespace App;

    class Router {
        static function resolve() {
            $url = $_SERVER["REQUEST_URI"];
            $parts = explode("/", $url);
            $name = $parts[1];
            if ($name === "api") {
                $action = $parts[2] ?? "";
                $controller = "App\\Api";
                if (method_exists($controller, $action)) {
                    $params = $_POST;
                    if ($params) {
                        $controller::$action($params);
                    }
                    else {
                        $controller::$action();
                    }
                }
                else {
                    http_response_code(404);
                }
            }
            else {
                switch (true) {
                    case $name === "favicon.ico":
                    case $name === "fav.svg": {
                        header("Content-Type: image/svg+xml");
                        echo file_get_contents("dist/fav.svg");
                    } break;
                    case $name === "js": {
                        header("Content-Type: application/javascript");
                        include "dist" . $url;
                    } break;
                    case $name === "css": {
                        header("Content-Type: text/css");
                        include "dist" . $url;
                    } break;
                    case $name === "img":
                    case $name === "fonts": {
                        $file = "dist" . $url;
                        $mime_type = mime_content_type($file);
                        header("Content-Type: $mime_type");
                        echo file_get_contents($file);
                    } break;
                    case $name === "images": {
                        $file = "public" . $url;
                        header("Content-Type: " . mime_content_type($file));
                        echo file_get_contents($file);
                    } break;
                    default: {
                        include "dist/index.html";
                    } break;
                }
            }
        }
    }