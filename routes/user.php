<?php

use App\Schema;

$router->get("/", function () {
    view("home");
    Schema::connection("altisend")->rename("users", "user1")->save();
});