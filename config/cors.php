<?php

namespace Config;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$allowedUrls = explode(',', $_ENV['ALLOWED_URLS_FRONTEND']);
$httpOrigin = $_SERVER['HTTP_ORIGIN'];

if (in_array($httpOrigin, $allowedUrls, true)) {
    header("Access-Control-Allow-Origin: $httpOrigin");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    header('Content-Type: application/json');
}
