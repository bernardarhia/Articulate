<?php

use App\MiddleWare;

$router->get("/student/add", function () {
    view("students/add-student-template");
});


// POST ROUTES
$router->post("/student/:id", function ($request, $response) {
    $name = $request->body->name ?? null;
    $age = $request->body->age ?? null;
});