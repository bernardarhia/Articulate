<?php

use Controllers\Home;

include_once __DIR__ . "/../controllers/HomeController.php";
$router->get("/", [Home::class, "index"]);