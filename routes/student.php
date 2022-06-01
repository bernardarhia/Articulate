<?php

use App\MiddleWare;

$router->get("/student/add", function () {
    view("students/add-student-template");
});


// POST ROUTES
$router->post("/student/add", function ($request, $response) {
    $name = $request->body->name;
    $age = $request->body->age;
    $request->session([
        "data" => ["name" => "Bernard", "id" => 1, "isAuth" => true],
        "path" => "",
        "httpOnly" => true,
        "secure" => true,
        "expiresAt" => "2022-05-01"
    ]);
});