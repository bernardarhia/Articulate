<?php

use App\Database\Schema as DatabaseSchema;

$router->get("/", function () {
    view("home");
    DatabaseSchema::connection("altisend")->rename("users", "user1")->save();
});