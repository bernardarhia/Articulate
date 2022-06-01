<?php

namespace App;

class MiddleWare
{
    static function isAuthenticated(Request $request, Response $response)
    {
        $isAuth = $request->session->user;
        if (!$isAuth) return $response->statusCode(400)->send("User is not authenticated");
        self::next();
    }

    public static function next()
    {
        // code will run fine
    }
}