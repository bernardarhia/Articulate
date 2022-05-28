<?php

// enable all php errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/vendor/autoload.php";

use App\Request;
use App\Router;

include_once 'functions.php';
$router = new Router;

$router->get("/", function () {
    view("home");
});

$router->get("/about", function ($request,  $response) {
    view("about");
});

$router->get('/contact', function ($request) {
    view("users/contact");
});




// Handle php file
$router->addNotFoundHandler(function () {
    $request = new Request;
    $title = "Not Found";
    require_once __DIR__ . "/templates/404.php";
});
$router->run();