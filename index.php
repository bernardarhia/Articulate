<?php
// enable all php errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/vendor/autoload.php";
include_once 'functions.php';

use App\Router;

$router = new Router;

// STUDENT ROUTES
require_once __DIR__ . "/routes/user.php";

// STUDENT ROUTES
require_once __DIR__ . "/routes/student.php";



// 404 ROUTE
$router->addNotFoundHandler(function () {
    $title = "Not Found";
    require_once __DIR__ . "/templates/404.php";
});

$router->run();