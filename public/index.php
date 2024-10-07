<?php

require_once '../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require_once '../config/cors.php';
require_once '../routes/web.php';
