<?php

namespace App;


class Request
{
    public $body = [];
    public $getCurrentPathParams = [];
    public $params = [];
    public function __construct()
    {
        $this->body = $this->getRequestBody();
        $this->getCurrentPathParams = $this->getParams();
    }

    private function getRequestBody()
    {
        $json = file_get_contents('php://input') ?? null;
        $object = json_decode($json);

        return ($object) ?? null;
    }
    private function getParams()
    {
        $urls = explode('/', $_SERVER['REQUEST_URI']);
        $params = array();
        for ($i = 0; $i < count($urls); $i++) {
            $params[] = ($urls[$i]);
        }
        return $params;
    }
    public function session()
    {
        return json_decode(json_encode($_SESSION)) ?? null;
    }
    public function postBody()
    {
        return $_POST ?? null;
    }
}