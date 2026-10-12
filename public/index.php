<?php

date_default_timezone_set('Asia/Manila');

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

require_once __DIR__ . '/../app/Helpers/Functions.php';

require_once __DIR__ . '/../routes/web.php';

use App\Helpers\Router;

Router::dispatch();
