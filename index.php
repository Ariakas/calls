<?php
    header("Access-Control-Allow-Origin: *");
    require "vendor/autoload.php";
    use App\Router;
    Router::resolve();