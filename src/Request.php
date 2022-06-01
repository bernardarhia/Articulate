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
    public function session($args = [
        "path" => "/",
        "domain" => null,
        "data" => [],
        "httponly" => false,
        "secure" => false,
        "expires_at" => null
    ]): void
    {
        session_set_cookie_params($args['expires_at'], $args['path'], $args['domain'],  $args['htpponly'], $args['secure']); //uncomment this when on production
        session_start();
        session_regenerate_id(true);

        if ($args['data'] != null) {
            foreach ($args['data'] as $key => $value) {
                $_SESSION[$key] = $value;
            }
        }
    }
    public function postBody()
    {
        echo "<pre>";
        print_r($_SERVER);
        return $_POST ?? null;
    }
}