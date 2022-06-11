<?php

namespace Controllers;

class Home
{
    public function index()
    {
        View::make("index");
    }
}